# Jasleen - Parse VFDB information 
# Simran - Select top hits

import pandas as pd
import os
import re

# Set paths
input_dir = "/path/to/input/folder"     # folder with BLAST filtered .tsv files
output_dir = "/path/to/output/folder"   # final output folder
os.makedirs(output_dir, exist_ok=True)

# Patterns for parsing stitle in blast output
vfdb_outer_pattern = re.compile(
    r"^(VFG\d+)\(gb\|([^|)]+)\)\s+(.*)\[([^\[]+?\(VF\d+\)\s+-\s+[^[]+)\]\s+\[([^\]]+)\]$"
)

vf_id_pattern = re.compile(r"\(VF(\d+)\)")
vf_category_pattern = re.compile(r"-\s+([^(]+)\(")

def parse_vfdb_header(full_header: str):
    match = vfdb_outer_pattern.search(full_header)
    if not match:
        return {
            "Resource_Subject_ID": "",
            "Protein_ID": "",
            "Subject_Gene": "",
            "Subject_Description": "",
            "VFID": "",
            "VF_Category": "",
            "Subject_Organism": ""
        }

    resource_id = match.group(1)
    protein_id = match.group(2)
    gene_and_desc = match.group(3).strip()
    vf_info = match.group(4)
    organism = match.group(5)

    # Extract Subject_Gene with nested parentheses 
    gene = ""
    desc = gene_and_desc
    if gene_and_desc.startswith("("):
        depth = 0
        gene_end = -1
        for i, char in enumerate(gene_and_desc):
            if char == "(":
                depth += 1
            elif char == ")":
                depth -= 1
                if depth == 0:
                    gene_end = i
                    break
        if gene_end != -1:
            gene = gene_and_desc[1:gene_end]
            desc = gene_and_desc[gene_end + 1:].strip()

    # Extract VFID and Category 
    vf_id_match = vf_id_pattern.search(vf_info)
    vf_cat_match = vf_category_pattern.search(vf_info)

    return {
        "Resource_Subject_ID": resource_id,
        "Protein_ID": protein_id,
        "Subject_Gene": gene,
        "Subject_Description": desc,
        "VFID": f"VF{vf_id_match.group(1)}" if vf_id_match else "",
        "VF_Category": vf_cat_match.group(1).strip() if vf_cat_match else "",
        "Subject_Organism": organism
    }

# Process each BLAST result file 
for file in os.listdir(input_dir):
    if not file.endswith(".tsv"):
        continue

    file_path = os.path.join(input_dir, file)
    print(f">> Processing {file} ...")

    # Load BLAST results
    df = pd.read_csv(file_path, sep="\t", header=None,
                     names=["qseqid", "sseqid", "pident", "length",
                            "qcovs", "evalue", "bitscore", "stitle"])

    # Ensure numeric columns
    for col in ["pident", "qcovs", "evalue", "bitscore"]:
        df[col] = pd.to_numeric(df[col], errors="coerce")

    # Sort & select top hits per query 
    df_sorted = df.sort_values(by=["qseqid", "pident", "qcovs", "evalue"],
                               ascending=[True, False, False, True])
    top_hits = df_sorted.groupby("qseqid").first().reset_index()

    # Parse VFDB headers
    parsed_rows = []
    for _, row in top_hits.iterrows():
        header = str(row.get("stitle", "")).strip()
        if not header:
            continue

        meta = parse_vfdb_header(header)
        parsed_rows.append({
            **row.to_dict(),
            **meta
        })

    parsed_df = pd.DataFrame(parsed_rows)

    # Remove stitle from final output
    parsed_df.drop(columns=["stitle", "Resource_Subject_ID", "Protein_ID"], inplace=True)

    # Save final combined output
    output_file = os.path.join(output_dir, file.replace(".tsv", "_top_hits_parsed.csv")) #save output as csv
    parsed_df.to_csv(output_file, index=False)

print(f"\nDone! VF top hits saved in: {output_dir}")

