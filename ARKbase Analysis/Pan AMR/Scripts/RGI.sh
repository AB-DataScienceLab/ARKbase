# Jasleen - Run RGI 

# Set paths
INPUT_DIR="/path/to/INPUT" # Folder with protein fasta files
OUTPUT_DIR="/path/to/RGI/output" #RGI output will get saved here
RGI_EXEC="/path/to/rgi"  # RGI executable path 

# Ensure the folders exist
mkdir -p "$OUTPUT_DIR"

# Check for input .faa files 
if compgen -G "$INPUT_DIR/*.faa" > /dev/null; then
    # Loop 
    for file in "$INPUT_DIR"/*.faa; do
        # Extract base filename 
        base=$(basename "$file" .faa)

        # Output path
        out_file="$OUTPUT_DIR/${base}"  

        # Run RGI
        $RGI_EXEC main --input_sequence "$file" \
                       --output_file "$out_file" \
                       --local \
                       --clean \
                       -t protein \
                       -n 10 \
                       --alignment_tool BLAST

        echo "Processed: $base"
    done

echo "RGI output saved in "$OUTPUT_DIR" ."

