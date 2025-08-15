#!/usr/bin/env python3
import os
import subprocess
import glob

def run_emapper_commands(fasta_file, output_dir=None):

    # Get the base name of the file without extension
    base_name = os.path.splitext(os.path.basename(fasta_file))[0]
    
    # If output directory is specified, create it if it doesn't exist
    if output_dir:
        os.makedirs(output_dir, exist_ok=True)
        base_output_path = os.path.join(output_dir, base_name)
    else:
        base_output_path = base_name
    
    # Command 1: Initial mapping
    cmd1 = [
        "emapper.py",
        "-m", "diamond",
        "--dmnd_db", "/home/anshu/upasana/Mitoinfect/Orthologs/eggnog-mapper/data/eggnog_BacteriaDB.dmnd",
        "--sensmode", "more-sensitive",
        "--pident", "80",
        "--query_cover", "90",
        "--evalue", "0.0001", 
        "--no_annot",
        "-i", fasta_file,
        "-o", base_output_path,
        "--cpu", "16"
    ]
    
    print(f"\nProcessing {fasta_file}...")
    print("Running first command...")
    
    try:
        subprocess.run(cmd1, check=True)
    except subprocess.CalledProcessError as e:
        print(f"Error running first command for {fasta_file}: {e}")
        return False
    
    # Command 2: Annotation
    seed_orthologs = f"{base_output_path}.emapper.seed_orthologs"
    cmd2 = [
        "emapper.py",
        "-m", "no_search",
        "--annotate_hits_table", seed_orthologs,
        "-o", f"{base_output_path}_annot",
        "--dbmem",
        "--report_orthologs",
        "--target_orthologs", "one2one",
        "--target_taxa", "2",
        "--tax_scope", "2"
    ]
    
    print("Running second command...")
    
    try:
        subprocess.run(cmd2, check=True)
    except subprocess.CalledProcessError as e:
        print(f"Error running second command for {fasta_file}: {e}")
        return False
    
    return True

def main():
    # Specify the input and output directories
    input_dir = "/home/anshu/upasana/Mitoinfect/Orthologs/eggnog-mapper/ARKbase/strep_agalactaie/fasta"  
    output_dir = "/home/anshu/upasana/Mitoinfect/Orthologs/eggnog-mapper/ARKbase/strep_agalactaie/eggnog_output"     
    
    # Get all FASTA files from the specified directory
    fasta_files = []
    for ext in ['*.fasta', '*.faa', '*.fa']:
        fasta_files.extend(glob.glob(os.path.join(input_dir, ext)))
    
    if not fasta_files:
        print(f"No FASTA files found in {input_dir}!")
        return
    
    print(f"Found {len(fasta_files)} FASTA files to process")
    
    # Process each FASTA file
    for fasta_file in fasta_files:
        success = run_emapper_commands(fasta_file, output_dir)
        if success:
            print(f"Successfully processed {fasta_file}")
        else:
            print(f"Failed to process {fasta_file}")

if __name__ == "__main__":
    main()
