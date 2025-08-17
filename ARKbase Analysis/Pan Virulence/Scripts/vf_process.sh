# Jasleen - Compile vf table for an organism

pathogen="E.faecium"  # name to use for output folder; eg: E.faecium
input_dir="/path/to/top_hits/folder" # Folder with top hits files for all genomes of an organism
output_file="./${pathogen}_vf.csv"

 # Write custom headers 
echo -e "Pathogen,Genome Accession,Protein ID,Resource Subject Identifier,Subject Gene,VF ID,VF Category,Subject Organism,% Identity,Query Coverage" > "$output_file"

for file in "$input_dir"/*.csv; do
    [ -f "$file" ] || continue

    base=$(basename "$file" top_hits_parsed.csv) # define file basename i.e. genome accession 
    
    # Data to print
    awk -v base="$base" -F',' 'NR > 1 {print "E.faecium"","base","$1","$2","$8","$10","$11","$12","$3","$5}' "$file" >> "$output_file"    # Set pathogen name to print in first coolumn
done
echo "VF results for $pathogen combined and saved in $output_file"
