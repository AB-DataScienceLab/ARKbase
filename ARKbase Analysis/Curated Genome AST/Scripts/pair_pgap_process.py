import os
import glob
import re
import subprocess

input_dir = "/home/dell/ark_base/Enterococcus_faecium/gff_files"
output_dir = "/home/dell/ark_base/Enterococcus_faecium/gff_files/pgap_processed_gff"
os.makedirs(output_dir, exist_ok=True)

fna_files = glob.glob(os.path.join(input_dir, "*.fna"))
gff_files = glob.glob(os.path.join(input_dir, "*.gff"))


accession_pattern = re.compile(r"(GCA|GCF)_\d+\.\d+")

def get_accession(filename):
    match = accession_pattern.search(os.path.basename(filename))
    return match.group(0) if match else None


fna_dict = {get_accession(f): f for f in fna_files if get_accession(f)}
gff_dict = {get_accession(f): f for f in gff_files if get_accession(f)}


for accession in sorted(fna_dict.keys()):
    if accession in gff_dict:
        fna_file = fna_dict[accession]
        gff_file = gff_dict[accession]
        output_file = os.path.join(output_dir, f"{accession}_pp.gff")

        print(f"Processing {accession}")
        subprocess.run([
            "python3",
            "/home/dell/ark_base/Enterococcus_faecium/gff_files/convert_refseq_to_prokka_gff.py",
            "-f", fna_file,
            "-g", gff_file,
            "-o", output_file
        ])
    else:
        print(f"❌ No GFF found for {accession}")
