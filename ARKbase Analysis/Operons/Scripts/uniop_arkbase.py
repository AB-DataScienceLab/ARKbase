#!/usr/bin/env python
import argparse
import sys
import os
import re

import pandas as pd
import numpy as np
from datetime import datetime
from sklearn.neighbors import KernelDensity

def create_parser():
    '''
    get command line arguments
    '''
    parser = argparse.ArgumentParser(description='Operon prediction using intergenic distance.',
                                     epilog='''Examples:
    python3 UniOP.py -g input.gff
    python3 UniOP.py -g input.gff -a GCF_009035845.1
    ''',
                                     formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument('-g','--gff_file',required=True,help='GFF annotation file.')
    parser.add_argument('-a','--assembly_id',required=False,help='Assembly ID for unique operon naming (e.g., GCF_009035845.1). If not provided, will attempt to extract from filename.')
    parser.add_argument('-t','--path',required=False,help='optional folder path where output files go (if not specified, the input file path is used)')
    parser.add_argument('-n','--n_sample',required=False,help='optional, the number of samples to generate a random combination of convergent and divergent distances, default 10**4)')
    parser.add_argument("--operon_flag", default=True, help='Output operon prediction.')
    return parser


def extract_assembly_id(gff_file, provided_assembly_id=None):
    """
    Extract assembly ID from GFF filename or use provided assembly ID
    """
    if provided_assembly_id:
        return provided_assembly_id
    
    # To extract from filename
    filename = os.path.basename(gff_file)
    
    # Common patterns for assembly IDs (ordered by specificity)
    patterns = [
        r'(GCF_\d+\.\d+)',      # GCF_009035845.1
        r'(GCA_\d+\.\d+)',      # GCA_001199155.1
        r'(NZ_[A-Z0-9]+\.\d+)', # NZ_CP123456.1
        r'([A-Z]+\d+\.\d+)',    # General pattern like ABC123.1
    ]
    
    for pattern in patterns:
        match = re.search(pattern, filename)
        if match:
            assembly_id = match.group(1)
            print(f"Extracted assembly ID '{assembly_id}' from filename '{filename}'")
            return assembly_id
    
    # If no pattern matches, use filename without extension
    fallback_id = os.path.splitext(filename)[0]
    print(f"No standard assembly ID pattern found, using '{fallback_id}' from filename")
    return fallback_id


def read_gff(gff_file):
    """
    Reads a GFF file and extracts gene/CDS information into a Pandas DataFrame.
    Also extracts gene IDs, prioritizing locus_tag/Name
    """
    data = []
    with open(gff_file, 'r') as f:
        for line in f:
            if line.startswith('#'):
                continue  # Skip comment lines
            fields = line.strip().split('\t')
            if len(fields) >= 9 and (fields[2] == "gene" or fields[2] == "CDS"): # the feature type can be either 'gene' or 'CDS'
                seqid = fields[0]
                feature_type = fields[2]
                start = int(fields[3])
                end = int(fields[4])
                strand = fields[6]
                attributes = fields[8]
                gene_id = None
                # Prioritize locus_tag
                match = re.search(r'locus_tag=([^;]+)', attributes)
                if match:
                  gene_id = match.group(1)
                else:
                  # Then prioritize Name
                  match = re.search(r'Name=([^;]+)', attributes)
                  if match:
                    gene_id = match.group(1)
                  else:
                    # Fallback to ID
                    match = re.search(r'ID=([^;]+)', attributes)
                    if match:
                      gene_id = match.group(1)
                data.append([seqid, start, end, strand, feature_type, gene_id])

    df = pd.DataFrame(data, columns=['nc', 'start', 'stop', 'strand', 'feature', 'gene_id'])
    return df


def extract_pairs_singlecontig(df_GFF):
    '''
    extract three types of adjacent gene pairs including the same strand, the convergent, and the divergent adjacent gene pairs
    '''
    # extract different types of neighboring genes
    df_gff = df_GFF.copy()
    df_gff['strandnext'] = df_gff.strand.shift(-1)
    df_gff['startnext'] = df_gff.start.shift(-1)
    df_gff['IGD'] = df_gff['startnext'] - df_gff['stop'] + 1
    df_gff = df_gff.dropna() ## exclude the last gene
    pairs = []
    for i in df_gff.index:
        if df_gff.at[i,"strand"] == df_gff.at[i,"strandnext"]:
            pairs.append("SGs") ## the same strand adjacent gene pairs
        elif df_gff.at[i,"strand"] == "+":
            pairs.append("CGs") ## the convergent adjacent gene pairs
        else:
            pairs.append("DGs") ## the divergent adjacent gene pairs
    df_gff['pairs'] = pairs
    return df_gff

def extract_pairs_multicontigs(df_GFF):
    '''
    this is for the genome consisting of multiple contigs.
    '''
    df_gff = df_GFF.copy()
    n_ctgs = df_gff['nc'].unique() # all contigs
    all_df_pairs = []
    if len(n_ctgs) == 1:
        df_i_ctg = df_gff[df_gff['nc']==n_ctgs[0]][['nc','start','stop','strand']]
        df_pairs = extract_pairs_singlecontig(df_i_ctg)
        return df_pairs
    else:
        for i in n_ctgs:
            df_i_ctg = df_gff[df_gff['nc']==i][['nc','start','stop','strand']]
            if len(df_i_ctg) <= 1: # remove the contigs with less than 2 genes
                continue
            df_pairs = extract_pairs_singlecontig(df_i_ctg)
            all_df_pairs.append(df_pairs) # contain all contigs with >= 2 genes

        df_allpairs = pd.concat(all_df_pairs, axis=0)
        return df_allpairs

def estimate_q(df_pairs):
    '''
    q is a probability that a pair of genes, located adjacent to each other on the same DNA strand, are part of an operon.
    '''
    M = len(df_pairs) # the total number of gene-gene transitions.
    S = len(df_pairs[df_pairs['pairs']=='SGs']) # the number of same-strand gene-gene transitions.
    O = M - S # the number of opposite-strand gene-gene transitions.
    q = (M-2*O)/(M-O)
    return q

def distPred(q, df_pairs, n_sample, smooth=1):
    '''
    generate the probability of each same-strand adjacent gene pair belonging to the same operon.
    '''
    ## sort the distances
    df_igd = df_pairs[df_pairs['pairs']=='SGs'].sort_values("IGD")
    igd_lst = df_igd.IGD.values
    ## transform all intergenic distancesN_data = len(igd_lst)
    # get the unique distance and its frequency
    N_data = len(igd_lst)
    val, counts = np.unique(igd_lst, return_counts=True)
    # store the unique distance into a dict
    dist_dict = {}
    for i in range(len(val)):
        dist_dict[val[i]] = counts[i]
    Qd_igd = []
    for v in igd_lst:
        lt_v = [e for e in val if e <= v]
        num = sum([dist_dict[e] for e in lt_v]) + 0.5
        Qd_igd.append(num/(N_data + 1))
    Qd_igd = np.array(Qd_igd).reshape(len(Qd_igd), 1)
    ## sampling the distance of non-operonic pairs by convergent and divergent gene pairs
    np.random.seed(0)
    CGDs = df_pairs[df_pairs['pairs']=='CGs'].IGD.values # the distances of convergent gene pairs
    DGDs = df_pairs[df_pairs['pairs']=='DGs'].IGD.values # the distances of divergent gene pairs
    # sample with repeats
    new_CGDs = np.random.choice(CGDs, n_sample)
    new_DGDs = np.random.choice(DGDs, n_sample)
    acd = np.add(new_CGDs, new_DGDs)/2.0 # the average of convergent and divergent distances
    Qd_acd = []
    for v in acd:
        lt_v = [e for e in val if e <= v]
        num = sum([dist_dict[e] for e in lt_v]) + 0.5
        Qd_acd.append(num/(N_data + 1))
    Qd_acd = np.array(Qd_acd).reshape(len(Qd_acd), 1)
    ## estimate the probability of abjacent genes belonging to the same operon using KDE
    bw0 = float("{:.2f}".format(100/N_data)) ## bandwidth
    kde1 = KernelDensity(kernel="gaussian", bandwidth=bw0*2).fit(Qd_acd)
    kde2 = KernelDensity(kernel="gaussian", bandwidth=bw0*2).fit(Qd_igd)
    p1 = (1-q)*np.exp(kde1.score_samples(Qd_igd)) ## p(d|zij=0)
    p2 = np.exp(kde2.score_samples(Qd_igd)) ## p(d)
    p = []
    for i in range(len(p1)):
        pij = 1 - p1[i]/p2[i]
        if pij <= 0:
            pij = 10**-2
        p.append(pij)
    ## smooth the prediction
    p_final = []
    if smooth:
        p_min = min(p)
        min_idx = p.index(p_min)
        p_modify = [p[i] if i<=min_idx else p_min for i in range(len(p))]
        p_final = p_modify
    else:
        p_final = p
    ## put the output into data frame
    prob = pd.DataFrame()
    prob['IGD'] = igd_lst
    prob['p'] = p_final
    prob['idx'] = df_igd.index.values
    prob['nc'] = df_igd.nc.values
    prob = prob.astype({'idx':'int','IGD':'int','p':'float'})
    out = prob.sort_values('idx')
    out.index = out.idx.values
    return out


def collect_prediction_perMethod(pred_file):
    ## pred_file: prediction of all abjacent gene pairs including the same-strand and opposite-strand pairs
    with open(pred_file) as infile:
        lines = infile.read().splitlines()
    # Find the header line
    header_line = None
    for line in lines:
        if line.startswith("Gene A"):
            header_line = line
            break
    if not header_line:
        raise ValueError("Header line not found in the prediction file")
    headers = header_line.split('\t')
    # Extract data lines
    tar_lines = [l.split('\t') for l in lines if not l.startswith("operon prediction scores") and not l.startswith("Gene A")]
    df_pred = pd.DataFrame(tar_lines)
    df_pred.columns = headers
    ## remove the opposite-strand pairs
    col = df_pred.columns[-1]
    df_pred = df_pred[df_pred[col].str.strip().astype(bool)]
    return df_pred


def pairs2operon(pred_file, assembly_id, d_source='Prediction', cutoff=0.5):
    """
    Extract operons based on predictive operonic pairs and remove duplicates
    Generate unique operon IDs with assembly ID
    """
    df_pred = collect_prediction_perMethod(pred_file)
    rcols = [col for col in df_pred.columns if col not in ['Gene A','Gene B','']]
    df_pred[rcols] = df_pred[rcols].astype('float')
    df_pred['ex_GeneB'] = df_pred['Gene B'].shift(1)
    
    operons = {}
    n = 1
    flag = 0
    for i in df_pred.index:
        if df_pred.at[i,f'{d_source}'] >= cutoff:
            if flag == 1:
                n += 1
                operons[n] = []
                operons[n].extend([df_pred.at[i,'Gene A'],df_pred.at[i,'Gene B']])
            elif n not in operons:
                operons[n] = []
                operons[n].extend([df_pred.at[i,'Gene A'],df_pred.at[i,'Gene B']])
            elif df_pred.at[i,'Gene A'] != df_pred.at[i,'ex_GeneB']:
                n += 1
                operons[n] = []
                operons[n].extend([df_pred.at[i,'Gene A'],df_pred.at[i,'Gene B']])
            else:
                operons[n].append(df_pred.at[i,'Gene B'])
            flag = 0
        else:
            flag = 1
            
    # Remove duplicates while preserving order
    cleaned_operons = {}
    for key, genes in operons.items():
        cleaned_operons[key] = list(dict.fromkeys(genes))
    
    # Create DataFrame with assembly ID in operon names
    operon_data = []
    for key, genes in cleaned_operons.items():
        operon_id = f"{assembly_id}_op{key}"
        operon_data.append([operon_id, genes])
    
    df_operons = pd.DataFrame(operon_data, columns=['operon_id', 'genes'])
    
    return df_operons

def output_distPred(inputfile, n_sample, path, gene_id_mapping, assembly_id, operon_flag=False):
    '''
    with a prediction file in the format Gene A\tGene B\tPrediction
    '''
    df_cds = read_gff(inputfile)
    df_pairs = extract_pairs_multicontigs(df_cds)
    q = estimate_q(df_pairs)
    preds = distPred(q, df_pairs, n_sample)

    head, tail = os.path.split(inputfile)
    # Include assembly ID in output filenames
    outfile = os.path.join(path, f"{assembly_id}_uniop.pred")
    if not os.path.exists(path):
        os.makedirs(path)
    with open(outfile, 'w') as fout:
        fout.write(f"operon prediction scores\n")
        fout.write(f"Gene A\tGene B\tPrediction\n")
        for i in df_cds.index:
            if i == df_cds.index[-1]:
                continue
            row = preds[preds.index == i]
            if len(row) > 0:
                prob = "{:.6f}".format(preds.at[i,'p'])
            else:
                prob = ''

            fout.write(f"{gene_id_mapping[i+1]}\t{gene_id_mapping[i+2]}\t{prob.ljust(9)}\n")

    if operon_flag:
        df_operon = pairs2operon(outfile, assembly_id)
        outfile_op = os.path.join(path, f"{assembly_id}_uniop.operon")
        
        # Write output in the desired format
        with open(outfile_op, 'w') as f:
            for _, row in df_operon.iterrows():
                genes_str = str(row['genes'])
                f.write(f"{row['operon_id']},{genes_str}\n")

def main():
    parser = create_parser()
    args = parser.parse_args(args=None if sys.argv[1:] else ['--help'])

    start = datetime.now()
    if args.gff_file:
        gff_file = args.gff_file
        if args.path:
            data_path = args.path
        else:
            head, tail = os.path.split(gff_file)
            data_path = head if head else '.'
    else:
        raise ValueError('Please specify the input GFF file')

    # Extract assembly ID
    assembly_id = extract_assembly_id(gff_file, args.assembly_id)
    print(f"Using assembly ID: {assembly_id}")

    # predict operons
    if args.n_sample:
        n_sample = int(args.n_sample) # ensure n_sample is an integer
    else:
        n_sample = 10**4

    df_cds = read_gff(gff_file)
    gene_id_mapping = {}
    gene_count = 1
    for i in df_cds.index:
        if df_cds.at[i,'feature'] == "gene" or df_cds.at[i,'feature'] == "CDS":
            gene_id_mapping[gene_count] = df_cds.at[i,'gene_id']
            gene_count += 1

    output_distPred(gff_file, n_sample, path=data_path, gene_id_mapping=gene_id_mapping, assembly_id=assembly_id, operon_flag=args.operon_flag)
    print(f"Time for UniOP prediction: {datetime.now() - start}")


if __name__ == '__main__':
    main()