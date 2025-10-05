import pandas as pd
from Bio import SeqIO
import glob
import os
import sys
from io import StringIO
import re

csv_file = glob.glob("*.csv")[0]
faa_files = glob.glob("*.faa") + glob.glob("*.fna") + glob.glob("*.fasta")

df = pd.read_csv(csv_file)
seed_id = df[df['orth_type'] == 'seed'].iloc[0]['orthologs'].lstrip('*')

asserted_protein_lengths = {}
for faa_file in faa_files:
    records = {}
    idlist = []
    for record in SeqIO.parse(faa_file, "fasta"):
        records[record.id] = record.seq
        if record.id == seed_id:
            idlist = [seed_id] + idlist
        else:
            idlist.append(record.id)
    with open(faa_file, "w") as f:
        for sid in idlist:
            for rid, seq in records.items():
                if rid != sid:
                    continue
                if ('CDS' in faa_file or 'cds' in faa_file) and (
                    seq.endswith('TAA') or seq.endswith('TAG') or seq.endswith('TGA')
                ):
                    seq = seq[:-3]
                    assert len(seq) % 3 == 0
                    asserted_protein_lengths[rid] = len(seq) // 3
                f.write(f">{rid}\n{seq}\n")
                break
        f.close()
protein_faa_file = None
cds_faa_file = None
ID = None
for faa_file in faa_files:
    if 'protein' in faa_file or 'Protein' in faa_file:
        protein_faa_file = faa_file
        records = SeqIO.parse(faa_file, "fasta")
        for record in records:
            if record.id in asserted_protein_lengths:
                assert len(record.seq) == asserted_protein_lengths[record.id]
    if 'cds' in faa_file or 'CDS' in faa_file:
        ID = faa_file.split('_CDS')[0] if 'CDS' in faa_file else faa_file.split('_cds')[0]
        cds_faa_file = faa_file

assert protein_faa_file is not None
assert cds_faa_file is not None
assert ID is not None


def run_again_again_again():
    # ====================================================================================
    aln_faa_file = protein_faa_file.split('.')[0] + '_alignment.fasta'
    os.system(f"clustalw -INFILE={protein_faa_file} -TYPE=PROTEIN -OUTFILE={aln_faa_file} -OUTPUT=FASTA")
    # ====================================================================================

    cds_id_seq = []
    for record in SeqIO.parse(cds_faa_file, "fasta"):
        cds_id_seq.append(record.id)

    records = {record.id: record.seq for record in SeqIO.parse(aln_faa_file, "fasta")}
    with open(aln_faa_file, "w") as f:
        for sid in cds_id_seq:
            for rid, seq in records.items():
                if rid != sid:
                    continue
                f.write(f">{rid}\n{seq}\n")
                break
        f.close()

    # ====================================================================================

    os.system(f"pal2nal.pl {aln_faa_file} {cds_faa_file} -codontable 11 -nogap -output fasta > {ID}_pal2nalOutput.fa")
   # os.system(f"python gap.py --input_file {ID}_pal2nalOutput.fa --output_file {ID}_filtered_pal2nalOutput.fa")
    os.system(f"./iqtree2 -s {ID}_pal2nalOutput.fa -st CODON11")

    # ====================================================================================

    logfile = glob.glob("*.log")[0]
    with open(logfile, "r") as f:
        lines = list(map(lambda x: x.strip('\n'), f.readlines()))
        f.close()

    flg = False
    parse_lines = []
    output = None
    for line in lines:
        if re.match(r"^\*{4}\s+TOTAL\s+([\d.]+)%\s+(\d+)\s+sequences failed composition chi2 test \(p-value<5%; df=(\d+)\)", line):
            assert flg
            flg = False
        if flg:
            parse_lines.append(line)
        if re.match(r"^Analyzing sequences: done in ([\d.eE+-]+) secs", line):
            flg = True
        model = re.match(r"^Corrected Akaike Information Criterion:\s+(.+)$", line)
        if model:
            output = model.group(1)

    DF = pd.read_csv(StringIO("\n".join(parse_lines)), sep=r"\s+", header=None)
    print(DF)

    failed_ids = []
    for i, r in DF.iterrows():
        if r[3] == 'failed' and r[1] != seed_id:
            failed_ids.append(r[1])

    if len(failed_ids) == 0:
        return output
        # with open(f"model_{ID}.txt", "w") as f:
        #     f.write(output)
        #     f.close()
        print("All sequences passed. Exiting...")
    else:
        records = {record.id: record.seq for record in SeqIO.parse(cds_faa_file, "fasta")}
        with open(cds_faa_file, "w") as f:
            for rid, seq in records.items():
                if rid in failed_ids:
                    continue
                f.write(f">{rid}\n{seq}\n")
            f.close()

        records = {record.id: record.seq for record in SeqIO.parse(protein_faa_file, "fasta")}
        with open(protein_faa_file, "w") as f:
            for rid, seq in records.items():
                if rid in failed_ids:
                    continue
                f.write(f">{rid}\n{seq}\n")
            f.close()
        for file in glob.glob(f"{ID}_pal2nalOutput.fa*"):
            os.remove(file)
        os.remove(f"{ID}_Protein.dnd")
        os.remove(f"{ID}_Protein_alignment.fasta")
        return run_again_again_again()


# ------------------------------
out = run_again_again_again()
# ------------------------------

# ====================================================================================
os.system(f"./iqtree2 -s {ID}_pal2nalOutput.fa -st CODON11 -m {out} --redo-tree -bb 1000 -alrt 1000 -nt AUTO" )
os.system(f"hyphy BUSTED --alignment {ID}_pal2nalOutput.fa --tree {ID}_pal2nalOutput.fa.treefile --branches All --pvalue 0.1 --output BUSTED_output.json")
os.system(f"hyphy FEL --alignment {ID}_pal2nalOutput.fa --tree {ID}_pal2nalOutput.fa.treefile --branches All --pvalue 0.1 --output fel_output.json")
os.system(f"hyphy slac --alignment {ID}_pal2nalOutput.fa --tree {ID}_pal2nalOutput.fa.treefile --branches All --pvalue 0.1 --output slac_results.json")
os.system(f"hyphy FUBAR --alignment {ID}_pal2nalOutput.fa --tree {ID}_pal2nalOutput.fa.treefile --output fubar_results.json") 
os.system(f"hyphy meme --alignment {ID}_pal2nalOutput.fa --tree {ID}_pal2nalOutput.fa.treefile --branches All --pvalue 0.1 --resample 100 --output MEME_results.json")

# with open('codemlParam.ctl.ctl', 'w') as file:
#     file.write(f'''seqfile = {ID}_pal2nalOutput.fa
# treefile = {ID}_pal2nalOutput.fa.treefile
# outfile = {ID}_outputPAML.txt

# CodonFreq = 2
# Malpha = 0
# Mgene = 0
# RateAncestor = 0
# Small_Diff = 0.5e-6
# aaDist = 0
# alpha = 0
# cleandata = 1
# clock = 0
# estFreq = 0
# fix_alpha = 1
# fix_blength = -1
# fix_rho = 1
# getSE = 0
# icode = 0
# method = 0
# ndata = 1
# omega = 1
# outfile = mlc
# rho = 0
# runmode = 0
# seqtype = 1''')
#     file.close()
# os.system(f"paPAML.pl -p 8 -f codemlParam.ctl -t 123 h -s 0.05 -d codemlparams")
print("\n\n=======\nDONE\n========\n\n")