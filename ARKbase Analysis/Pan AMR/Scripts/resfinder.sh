# Jasleen - run ResFinder

set -euo pipefail

# Paths
RESFINDER_SCRIPT="/path/to/run_resfinder.py"
INPUT_DIR="/path/to/input"  # .fna files
OUTPUT_DIR="/path/to/output"

mkdir -p "$OUTPUT_DIR"

# Loop through all .fna files
for file in "$INPUT_DIR"/*.fna; do
    # Extract base filename 
    base=$(basename "$file" .fna)
   # Check if the output folder already exists and then run on the rest of the files
    if [ -d "$OUTPUT_DIR/$base" ]; then
        echo "Output folder for $base already exists. Skipping..."
        continue
    fi
    
    echo "Running ResFinder on: $base"

    python3 "$RESFINDER_SCRIPT" \
        -ifa "$file" \
        -o "$OUTPUT_DIR/$base" \
        -db_res "/home/snp/JK_TEST3/ResFinder/resfinder/db_resfinder" \
        -db_point "/home/snp/JK_TEST3/ResFinder/resfinder/db_pointfinder" \
        --acquired 
       # --species "Escherichia coli"  # Specify organism

    echo "Result saved in: $OUTPUT_DIR/$base"
done
echo "ResFinder results saved in $INPUT_DIR"
