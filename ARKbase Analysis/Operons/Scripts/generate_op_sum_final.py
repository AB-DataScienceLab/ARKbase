import pandas as pd
import re
import ast  

def parse_gff_data_final(gff_file_path):
    """
    Parses a GFF3 file with robust, feature-aware logic. It correctly sources
    information from the appropriate feature types and includes a fallback to
    find inferred protein IDs for pseudogenes from the 'inference' tag.

    Args:
        gff_file_path (str): The path to the GFF file.

    Returns:
        dict: A dictionary mapping each locus_tag to its consolidated data.
    """
    gff_info = {}
    print(f"INFO: Parsing GFF file: {gff_file_path}...")

    try:
        with open(gff_file_path, 'r') as f:
            for line in f:
                if line.startswith('#'):
                    continue

                parts = line.strip().split('\t')
                if len(parts) < 9:
                    continue
                
                feature_type = parts[2]
                attributes = parts[8]

                locus_tag_match = re.search(r"locus_tag=([^;]+)", attributes)
                if not locus_tag_match:
                    continue
                
                locus_tag = locus_tag_match.group(1)

                if locus_tag not in gff_info:
                    gff_info[locus_tag] = {}

                #Sourcing information from the correct feature type

                # 1. Get coordinates and official gene symbol (if it exists).
                if feature_type in ['gene', 'pseudogene']:
                    gff_info[locus_tag]['start'] = parts[3]
                    gff_info[locus_tag]['end'] = parts[4]
                    gff_info[locus_tag]['strand'] = parts[6]
                    
                    # ONLY look for the 'gene=' tag for the official symbol.
                    gene_symbol_match = re.search(r";gene=([^;]+)", attributes)
                    if gene_symbol_match:
                        gff_info[locus_tag]['gene_name'] = gene_symbol_match.group(1)

                # 2. Get protein_id from 'CDS' lines, with fallback for inferred IDs.
                elif feature_type == 'CDS':
                    protein_id_match = re.search(r"protein_id=([^;]+)", attributes)
                    if protein_id_match:
                        gff_info[locus_tag]['protein_id'] = protein_id_match.group(1)
                    else:
                        # Fallback: search for an inferred ID within the 'inference' tag.
                        inference_match = re.search(r"RefSeq:((?:WP|NP|YP)_\d+\.\d+)", attributes)
                        if inference_match:
                            # --- CHANGE 1: Do NOT add "(inferred)" text ---
                            gff_info[locus_tag]['protein_id'] = inference_match.group(1)

                # 3. Get product from ANY line that has it.
                product_match = re.search(r"product=([^;]+)", attributes)
                if product_match:
                    gff_info[locus_tag]['product'] = product_match.group(1).replace("%2C", ",")

    except FileNotFoundError:
        print(f"FATAL ERROR: GFF file not found at: {gff_file_path}")
        return None

    print(f"INFO: Successfully parsed details for {len(gff_info)} locus tags from GFF.")
    return gff_info


def main():
    """
    Main function to run the operon summary generation.
    """
    # 1.FILE PATHS 
    operon_file = "/mnt/c/Users/Dell/Desktop/UniOP/OUTPUT/SF_UNIOP_OUTPUT/GCF_000006925.2_uniop.OPERON"
    gff_file = "/mnt/c/Users/Dell/Desktop/UniOP/OUTPUT/SF_UNIOP_OUTPUT/SF_ref_genome.gff"
    output_file = "operon_summary_final_SF_test.tsv"
 
    # Step 1: Parse the GFF file to build our database
    gff_data_map = parse_gff_data_final(gff_file)
    if gff_data_map is None:
        return

    # Step 2: Load the operon predictions file
    try:
        operon_df = pd.read_csv(operon_file)
    except FileNotFoundError:
        print(f"FATAL ERROR: Operon file not found at: {operon_file}")
        return

    # Step 3: Prepare a list to hold all our output rows
    output_rows = []

    print("INFO: Processing operons and generating summary...")
    # Step 4: Iterate through each operon
    for index, row in operon_df.iterrows():
        operon_id = row['operon_id']
        
        try:
            gene_locus_tags = ast.literal_eval(row['genes'])
        except (ValueError, SyntaxError):
            print(f"  - WARNING: Could not parse gene list for operon {operon_id}. Skipping.")
            continue
        
        operon_gene_data = [gff_data_map.get(lt) for lt in gene_locus_tags if gff_data_map.get(lt)]

        if not operon_gene_data: continue

        all_starts = [int(g['start']) for g in operon_gene_data if 'start' in g]
        all_ends = [int(g['end']) for g in operon_gene_data if 'end' in g]
        
        if not all_starts or not all_ends: continue

        operon_length = max(all_ends) - min(all_starts) + 1

        for locus_tag in gene_locus_tags:
            if locus_tag in gff_data_map:
                gene_info = gff_data_map[locus_tag]
                
                #CHANGE 2: Default to a hyphen '-' if gene_name is not found
                gene_name_for_output = gene_info.get('gene_name', '-')
                
                output_row = {
                    'Operon_ID': operon_id,
                    'Operon_Length': operon_length,
                    'Locus_Tag': locus_tag,
                    'Gene_Name': gene_name_for_output,
                    'Protein_ID': gene_info.get('protein_id', 'N/A'),
                    'Start': gene_info.get('start', 'N/A'),
                    'Stop': gene_info.get('end', 'N/A'),
                    'Strand': gene_info.get('strand', 'N/A'),
                    'Product': gene_info.get('product', 'N/A')
                }
                output_rows.append(output_row)
            else:
                print(f"  - WARNING: Locus tag '{locus_tag}' from operon {operon_id} not found in GFF map. Skipping gene.")

    if not output_rows:
        print("WARNING: No output was generated. Please check your input files and paths.")
        return

    summary_df = pd.DataFrame(output_rows)
    summary_df.to_csv(output_file, sep='\t', index=False)

    print(f"\n✅ Success! Corrected operon summary file saved to: {output_file}")


if __name__ == "__main__":
    main()