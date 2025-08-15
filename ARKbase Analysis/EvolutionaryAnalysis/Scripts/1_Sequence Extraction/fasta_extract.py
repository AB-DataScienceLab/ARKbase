from Bio import SeqIO
import os

# Input files
multi_fasta_file = "GCF_000008865.2_protein.faa"
id_list_file = "Protein_ID.txt"

# Output directory
output_dir = "extracted_fastas"
os.makedirs(output_dir, exist_ok=True)

# Read IDs to extract
with open(id_list_file, "r") as f:
    target_ids = set(line.strip() for line in f if line.strip())

# Parse and extract
for record in SeqIO.parse(multi_fasta_file, "fasta"):
    seq_id = record.id.split('|')[0] if '|' in record.id else record.id
    if seq_id in target_ids:
        out_file = os.path.join(output_dir, f"{seq_id}.faa")
        with open(out_file, "w") as out_f:
            SeqIO.write(record, out_f, "fasta")

