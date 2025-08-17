pathogen="E_faecium"  # Designate pathogen name for output file
input_dir="/path/to/eggnog-mapper/results"
output_file="path/to/${pathogen}_cog.tsv" # Output file for a pathogen

echo -e "Pathogen\tGenome Accession\tProtein ID\tCOG Category" > "$output_file"

for file in "$input_dir"/*.annotations; do
    [ -f "$file" ] || continue

    base=$(basename "$file" .tmp.emapper.annotations)     
    
    # Data to print
    awk -v base="$base" -F'\t' 'NR > 1 {print "Enterococcus faecium" "\t" base "\t" $1 "\t" $7}' "$file" >> "$output_file" # Write pathogen name in first column
    
done
echo "Done compiling $pathogen COG data in $output_file"
