#!/bin/bash

# Input file containing BioSample IDs
INPUT_FILE="efaecium.txt"

# Output file to store results
OUTPUT_FILE="ef_genomes.csv"

# Log file to store failures
LOG_FILE="failed_log_ef.txt"

# Check if the input file exists
if [[ ! -f "$INPUT_FILE" ]]; then
    echo "Input file ${INPUT_FILE} does not exist!"
    exit 1
fi

# Clear previous output and add a header if the file doesn't exist
if [[ ! -f "$OUTPUT_FILE" ]]; then
    echo "BioSample_ID,Assembly_Accession,Assembly_Name" > "$OUTPUT_FILE"
fi

# Clear the log file before running
> "$LOG_FILE"

while read -r biosample; do
    echo "Processing: $biosample"

    # Step 1: Fetch Assembly ID using esearch
    asm_id=$(wget -qO- "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esearch.fcgi?db=assembly&term=${biosample}")
   
    if [[ $? -ne 0 ]]; then
        echo "Error fetching data for ${biosample}" | tee -a "$LOG_FILE"
        continue
    fi

    asm_id=$(echo "$asm_id" | grep -oP '(?<=<Id>)[^<]+' | head -1)

    if [[ -n "$asm_id" ]]; then
        # Step 2: Fetch Assembly Accession and Assembly Name using esummary
        response=$(wget -qO- "https://eutils.ncbi.nlm.nih.gov/entrez/eutils/esummary.fcgi?db=assembly&id=${asm_id}")
       
        if [[ $? -ne 0 ]]; then
            echo "Error fetching details for ${biosample} (asm_id: ${asm_id})" | tee -a "$LOG_FILE"
            continue
        fi

        accession=$(echo "$response" | grep -oP '(?<=<AssemblyAccession>)[^<]+')
        asm_name=$(echo "$response" | grep -oP '(?<=<AssemblyName>)[^<]+')

        if [[ -n "$accession" && -n "$asm_name" ]]; then
            echo "${biosample} -> ${accession} (${asm_name})"
            echo "${biosample},${accession},${asm_name}" >> "$OUTPUT_FILE"
        else
            echo "Missing accession or name for ${biosample}" | tee -a "$LOG_FILE"
            echo "${biosample},NA,NA" >> "$OUTPUT_FILE"
        fi
    else
        echo "No assembly found for ${biosample}" | tee -a "$LOG_FILE"
        echo "${biosample},NA,NA" >> "$OUTPUT_FILE"
    fi

    # Sleep to avoid hitting API limits
    sleep 1.5
done < "$INPUT_FILE"

echo "✅ Processing complete! Results saved in $OUTPUT_FILE"
echo "❌ Failed lookups logged in $LOG_FILE"


