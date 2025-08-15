import os
import pandas as pd
from pathlib import Path

def process_ortholog_file(input_file, output_folder):
    """
    Process a single ortholog file and save filtered results to CSV.
    
    Args:
        input_file (str): Path to input file
        output_folder (str): Path to output folder
    """
    # Create output folder if it doesn't exist
    os.makedirs(output_folder, exist_ok=True)
    
    # Read the file, skipping comment lines
    with open(input_file, 'r') as f:
        lines = f.readlines()
    
    # Find the header line (starts with #query)
    header_idx = next(i for i, line in enumerate(lines) if line.startswith('#query'))
    
    # Read the data into a pandas DataFrame
    df = pd.read_csv(input_file, 
                     sep='\t',
                     skiprows=header_idx,
                     names=['query', 'orth_type', 'species', 'orthologs'])
    
    # Filter for seed or one2one entries
    filtered_df = df[df['orth_type'].isin(['seed', 'one2one'])]
    
    # Create output filename
    input_filename = os.path.basename(input_file)
    output_filename = input_filename.replace('annot.emapper.orthologs', 'filtered_orthologs.csv')
    output_path = os.path.join(output_folder, output_filename)
    
    # Save to CSV
    filtered_df.to_csv(output_path, index=False)
    print(f"Processed {input_file} -> {output_path}")

def main():
    # Get the current working directory
    input_folder = "."
    output_folder = "./filtered_output"
    
    # Process all matching files in the folder
    for file in Path(input_folder).glob("*annot.emapper.orthologs"):
        process_ortholog_file(str(file), output_folder)

if __name__ == "__main__":
    main()
