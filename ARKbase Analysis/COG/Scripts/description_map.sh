# Jasleen - COG description mapping to combined table

file="path/to/input/file"   # Combined COG file

awk -F'\t' -v OFS='\t' '
  BEGIN {
      map["J"] = "Translation, ribosomal structure and biogenesis"
      map["A"] = "RNA processing and modification"
      map["K"] = "Transcription"
      map["L"] = "Replication, recombination and repair"
      map["B"] = "Chromatin structure and dynamics"
      map["D"] = "Cell cycle control, cell division, chromosome partitioning"
      map["Y"] = "Nuclear structure"
      map["V"] = "Defense mechanisms"
      map["T"] = "Signal transduction mechanisms"
      map["M"] = "Cell wall/membrane/envelope biogenesis"
      map["N"] = "Cell motility"
      map["Z"] = "Cytoskeleton"
      map["W"] = "Extracellular structures"
      map["U"] = "Intracellular trafficking, secretion, and vesicular transport"
      map["O"] = "Posttranslational modification, protein turnover, chaperones"
      map["C"] = "Energy production and conversion"
      map["G"] = "Carbohydrate transport and metabolism"
      map["E"] = "Amino acid transport and metabolism"
      map["F"] = "Nucleotide transport and metabolism"
      map["H"] = "Coenzyme transport and metabolism"
      map["I"] = "Lipid transport and metabolism"
      map["P"] = "Inorganic ion transport and metabolism"
      map["Q"] = "Secondary metabolites biosynthesis, transport and catabolism"
      map["R"] = "General function prediction only"
      map["S"] = "Function unknown"
      map["X"] = "No COG assignment"
  }
NR==1 {
    for (i=1; i<=NF; i++) {
        if ($i == "COG Category") cog_col = i
    }
    print $0, "COG Description"
    next
}
{
    cog = $cog_col
    split(cog, letters, "")
    seen = ""
    desc = ""
    for (j in letters) {
        l = letters[j]
        if (l ~ /[A-Z]/ && index(seen, l) == 0) {
            seen = seen l
            if (desc != "") desc = desc "; "
            desc = desc map[l]
        }
    }
    if (desc == "") desc = "-"
    print $0, desc
}' "$file" > "$file.tmp" && mv "$file.tmp" "$file"

echo "COG description added to $file"
