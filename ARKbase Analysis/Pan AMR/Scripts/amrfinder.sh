# Jasleen -run AMRFinderPlus

set -euo pipefail

INPUT_DIR="/path/to/input/folder" # Directory containing .faa files
OUTPUT_BASE="/path/to/output/folder"

mkdir -p "${OUTPUT_BASE}"
 
# Loop over protein fasta files
for prot in "${INPUT_DIR}"/*.faa; do
    
    base=$(basename "${prot}" .faa)
    echo "Processing ${base}..."

    amrfinder \
      --organism Escherichia \
      -p "${prot}" \
      -o "${OUTPUT_BASE}/${base}" \  # Specify organism

done
echo "AMRFinderPlus output saved in $OUTPUT_BASE"
