# Jasleen - Run blastp with VFDB blast db and set thresholds

# Set path
input_folder="/path/to/input/folder/with/.faa/files"
if [ ! -d "$input_folder" ]; then
    echo "ERROR: The input folder '$input_folder' does not exist."
    exit 1
fi

blast_db="/path/to/local/blast/database" # Path to blast db with downloaded VFDB proteins 
blast_out="/path/to/blast/output/folder" # Filtered blast output will get saved in this folder

mkdir -p "$blast_out" 

# Loop through input FASTA files
for file in "$input_folder"/*.faa*; do
    filename=$(basename "$file" .faa) # Set basename 
    echo "Running BLASTP for : $filename "
    
    blast_file="$blast_out/${filename}.tsv"

    # Run BLASTP
    blastp -query "$file" -db "$blast_db" \
        -outfmt '6 qseqid sseqid pident length qcovs evalue bitscore stitle' \
        -evalue 1e-6 -num_threads 10 | awk -F'\t' '$3 >= 40 && $5 >= 70 && $6 <= 1e-6' >"$blast_file" #set thresholds for pident>=30, qcovs>=70 and e-value<= 1e-6
done

echo "BLAST done. Filtered files in $blast_out"
