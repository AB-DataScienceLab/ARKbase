#!/bin/bash

# ==============================================================================
# Script to generate pathogen-specific protein ID lists from a FASTA file.
#
# This script reads a master FASTA file with headers formatted as:
# >prot_ID | protein_name | pathogen_name
#
# It then creates a separate text file for each specified pathogen,
# containing one protein ID per line.
# ==============================================================================

# --- Configuration ---
# 1. The master FASTA file containing all proteins.
FASTA_FILE="all_proteins_reformatted.faa"

# 2. An array holding all the pathogen names you want to process.
#    You can easily add or remove names from this list.
pathogens=(
    "a_baumannii"
    "n_gonorrhoeae"
    "s_sonnei"
    "s_pyogenes"
    "s_pneumoniae"
    "s_flexneri"
    "s_enterica"
    "s_aureus"
    "s_agalactiae"
    "p_aeruginosa"
    "k_pneumoniae"
    "h_influenzae"
    "e_faecium"
    "e_coli"
)
# --- End of Configuration ---


# Check if the input FASTA file exists before starting.
if [ ! -f "$FASTA_FILE" ]; then
    echo "Error: Input file '$FASTA_FILE' not found."
    exit 1
fi

echo "Starting to generate ID lists from '$FASTA_FILE'..."
echo "---------------------------------------------------"

# Loop through each item in the 'pathogens' array.
# The current item will be available in the variable '$pathogen'.
for pathogen in "${pathogens[@]}"; do

    # Define the name for the output file based on the pathogen name.
    OUTPUT_FILE="${pathogen}_master_ids.txt"

    echo "Processing: ${pathogen}  ==>  Creating file: ${OUTPUT_FILE}"

    # Use the robust awk command to extract the IDs.
    # -v p="$pathogen":  Passes the shell variable 'pathogen' into an awk variable 'p'.
    # -F ' \\| ':       Sets the field separator to " | ".
    # '$3 ~ p':          Checks if the third field ($3) contains the pathogen name stored in 'p'. This is robust against line-ending issues.
    # '{sub...; print}': The action to perform: remove the '>' from the first field and print it.
    awk -v p="$pathogen" -F ' \\| ' '$3 ~ p {sub(/^>/, "", $1); print $1}' "$FASTA_FILE" > "$OUTPUT_FILE"

done

echo "---------------------------------------------------"
echo "All ID lists have been created successfully!"
echo "Verifying created files:"
ls -lh *_master_ids.txt
