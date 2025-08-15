#!/bin/bash

# Directory containing GFF files
INPUT_DIR="/mnt/c/Users/Dell/Desktop/UniOP/gff_files_arkbase/NG"     
OUTPUT_DIR="/mnt/c/Users/Dell/Desktop/UniOP/UNIOP_OUT_arkbase/NG" 
# Create output directory if it doesn't exist
mkdir -p "$OUTPUT_DIR"

# Path to your Python script
SCRIPT="/mnt/c/Users/Dell/Desktop/UniOP/uniop_arkbase.py"

# Loop over each GFF file in the input directory
for gff_file in "$INPUT_DIR"/*.gff; do
    echo "Processing $gff_file"
    
    # Run the script
    python3 "$SCRIPT" -g "$gff_file" -t "$OUTPUT_DIR" --operon_flag True
done

echo "All GFF files processed."
