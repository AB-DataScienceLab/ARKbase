# Jasleen - run ABRICATE with MEGARes db

set -euo pipefail
INPUT_DIR="/path/to/input"  # Input folder containing .fna files
OUTPUT_DIR="/path/to/output"  # Output folder for results
DB="megares"  # Database to use 

mkdir -p "$OUTPUT_DIR"

# Loop over each .fna file 
for file in "$INPUT_DIR"/*.fna; do
    base_name=$(basename "$file" .fna)  # Extract the base filename

    echo "Running abricate on $base_name..."

    abricate "$file" --db "$DB" --quiet > "$OUTPUT_DIR/${base_name}_${DB}.txt"
done

echo "Saved results in $OUTPUT_DIR."