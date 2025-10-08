import os 
import subprocess

output_file_dir = "/media/snp/drive/AB/last_seqtk"
data_dir = "/media/snp/drive/AB/"
name_files_dir = "/media/snp/drive/AB/longest_locus_files"

os.makedirs(output_file_dir, exist_ok=True)
final_faa_file = os.path.join(data_dir, "final.faa")
lst_files = [f for f in os.listdir(name_files_dir) if f.endswith(".lst")]


for lst_file in lst_files:
    name_file_path = os.path.join(name_files_dir, lst_file)
    output_file_path = os.path.join(output_file_dir, f"{os.path.splitext(lst_file)[0]}.faa")

   
    results = subprocess.run(
        ["/home/snp/new/mab_Ab/p_analysis/sample_seqtk/seqtk/seqtk", "subseq", final_faa_file, name_file_path],
        stdout=open(output_file_path, 'w'),
        stderr=subprocess.PIPE
    )

    if results.returncode != 0:
        print(f"Error processing final.faa with {lst_file}: {results.stderr.decode()}")
