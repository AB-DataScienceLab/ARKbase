#Jasleen Eggnog e-mapper script

import os
import subprocess
import glob

# Set your input and output paths
input_dir = "/path/to/faa_files"
output_dir = "/path/to/output_folder" # The folder with three output files for each input file 
emapper_path = os.path.join(os.path.dirname(__file__), "/path/to/emapper.py")
finale_dir = "/path/to/folder/with/only/.annotations_files"

os.makedirs(output_dir, exist_ok=True)

# Input file extensions to include
extensions = ["*.faa", "*.fasta", "*.fa"]

input_files = []
for ext in extensions:
    input_files.extend(glob.glob(os.path.join(input_dir, ext)))

print(f"Found {len(input_files)} FASTA/FAA files.")

# Run emapper for each file in input folder
for infile in input_files:
    base_name = os.path.splitext(os.path.basename(infile))[0]
    output_path = os.path.join(output_dir, f"{base_name}.tmp")
    annotation_file = os.path.join(output_dir, f"{base_name}.tmp.emapper.annotations")

    if os.path.exists(annotation_file):
        print(f"Skipping {base_name}, already present.")
        continue

    cmd = [
        "python3", emapper_path,
        "--cpu", "10",
        "--mp_start_method", "forkserver",
        "-o", output_path,
        "--override",
        "-m", "diamond",
        "--dmnd_db", "/path/to/eggnog/diamondDB", # Define path to the eggnog diamond databse 
        "--dmnd_ignore_warnings",
        "-i", infile,
        "--evalue", "0.001",
        "--score", "60",
        "--pident", "40",
        "--query_cover", "30",
        "--subject_cover", "20",
        "--itype", "proteins",
        "--tax_scope", "bacteria",
        "--target_orthologs", "all",
        "--go_evidence", "non-electronic",
        "--pfam_realign", "none",
    ]

    print(f"\nProcessing {infile}...")
    try:
        subprocess.run(cmd, check=True)
        print(f"Done: {base_name}")
    except subprocess.CalledProcessError as e:
        print(f"Error processing {base_name}: {e}")


os.makedirs(finale_dir, exist_ok=True)

# Copy all .annotations files
for file_path in glob.glob(os.path.join(output_dir, "*.annotations")):
    filename = os.path.basename(file_path)
    dest_path = os.path.join(finale_dir, filename)
    
    subprocess.run(["cp", file_path, dest_path], check=True)

    # Filter headers
    awk_command = f"awk '!/^##/' '{dest_path}' | sed 's/#//g' > '{dest_path}.tmp' && mv '{dest_path}.tmp' '{dest_path}'"
    subprocess.run(awk_command, shell=True, check=True)

print("Final files created.")
