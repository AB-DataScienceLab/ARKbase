#!/bin/bash

# === Input files ===
GENOME="/home/suhani/Desktop/Genome_browser/kp_genomic.fna"
GFF="/home/suhani/genome_browser/gff_files/K_pneumoniae.gff"

# === Input GFF3 files ===
OPERON_GFF="/home/suhani/genome_browser/Operon/k_pneumoniae_op.gff"
VIR_GFF="/home/suhani/genome_browser/VFDB/k_pneumoniae_vfdb_output.gff3"
AMR1_GFF="/home/suhani/genome_browser/AMRFinderPlus/k_pneumoniae_amrfinder.gff3"
AMR2_GFF="/home/suhani/genome_browser/ResFinder/kp_resfinder_output.gff3"
AMR3_GFF="/home/suhani/genome_browser/RGI/k_pneumoniae_rgi.gff3"
AMR4_GFF="/home/suhani/genome_browser/MEGARes/k_pneumoniae_megares.gff3"
COG_GFF="/home/suhani/genome_browser/COG/k_pneumoniae_cog.gff3"
IS_ELEMENTS="/home/suhani/genome_browser/IS_Elements/is_elements.gff"
EC="/home/suhani/genome_browser/EC/k_pneumoniae_ec_output.gff3"
KEGG="/home/suhani/genome_browser/KEGG_ko/k_pneumoniae_ko.gff3"
PPI_DC_Q1="/home/suhani/genome_browser/PPI/dg_q1.gff"
PPI_DC_Q2="/home/suhani/genome_browser/PPI/dg_q2.gff"
PPI_DC_Q3="/home/suhani/genome_browser/PPI/dg_q3.gff"
PPI_DC_Q4="/home/suhani/genome_browser/PPI/dg_q4.gff"
PPI_CC_Q1="/home/suhani/genome_browser/PPI/cc_q1.gff"
PPI_CC_Q2="/home/suhani/genome_browser/PPI/cc_q2.gff"
PPI_CC_Q3="/home/suhani/genome_browser/PPI/cc_q3.gff"
PPI_CC_Q4="/home/suhani/genome_browser/PPI/cc_q4.gff"
PPI_BC_Q1="/home/suhani/genome_browser/PPI/bw_q1.gff"
PPI_BC_Q2="/home/suhani/genome_browser/PPI/bw_q2.gff"
PPI_BC_Q3="/home/suhani/genome_browser/PPI/bw_q3.gff"
PPI_BC_Q4="/home/suhani/genome_browser/PPI/bw_q4.gff"
RESTRICTION_SITES="/home/suhani/genome_browser/Restriction_sites/combined_restriction_sites.gff3"
PROTEIN_FEATURE1_GFF="/home/suhani/genome_browser/Drug_targets/virulence_kp_with_product.gff3"
PROTEIN_FEATURE2_GFF="/home/suhani/genome_browser/Drug_targets/ttd_novel_kp_with_product.gff3"
PROTEIN_FEATURE3_GFF="/home/suhani/genome_browser/Drug_targets/non_paralog_kp_with_product.gff3"
PROTEIN_FEATURE4_GFF="/home/suhani/genome_browser/Drug_targets/human_NH_kp_with_product.gff3"
PROTEIN_FEATURE5_GFF="/home/suhani/genome_browser/Drug_targets/essential_kp_with_product.gff3"
PROTEIN_FEATURE6_GFF="/home/suhani/genome_browser/Drug_targets/drugbank_novel_kp_with_product.gff3"
PROTEIN_FEATURE7_GFF="/home/suhani/genome_browser/Drug_targets/betweenness_kp_with_product.gff3"
PROTEIN_FEATURE8_GFF="/home/suhani/genome_browser/Drug_targets/anti_target_kp_with_product.gff3"

# === Create or reset JBrowse project ===
jbrowse create kp-browser --force
cd kp-browser || exit 1

# === Add genome assembly ===
jbrowse add-assembly "$GENOME" \
  --load copy \
  --name "K.pneumoniae Genome" \
  --force

# === Add reference gene annotations ===
jbrowse add-track "$GFF" \
  --load copy \
  --trackId kp-genes \
  --name "Gene Annotations" \
  --force

# === Add other GFF3 annotation tracks ===
declare -A TRACKS=(
  ["$OPERON_GFF"]="Operons"
  ["$VIR_GFF"]="Virulence Genes"
  ["$AMR1_GFF"]="AMR Genes (AMRFinderPlus)"
  ["$AMR2_GFF"]="AMR Genes (ResFinder)"
  ["$AMR3_GFF"]="AMR Genes (RGI)"
  ["$AMR4_GFF"]="AMR Genes (MEGARes)"
  ["$COG_GFF"]="Cluster of Genes (COG)"
  ["$IS_ELEMENTS"]="IS Elements"
  ["$EC"]="Enzyme Classification"
  ["$KEGG"]="KEGG Ontology"
  ["$PPI_DC_Q1"]="Degree Centrality (Q1)"
  ["$PPI_DC_Q2"]="Degree Centrality (Q2)"
  ["$PPI_DC_Q3"]="Degree Centrality (Q3)"
  ["$PPI_DC_Q4"]="Degree Centrality (Q4)"
  ["$PPI_CC_Q1"]="Closeness Centrality (Q1)"
  ["$PPI_CC_Q2"]="Closeness Centrality (Q2)"
  ["$PPI_CC_Q3"]="Closeness Centrality (Q3)"
  ["$PPI_CC_Q4"]="Closeness Centrality (Q4)"
  ["$PPI_BC_Q1"]="Betweenness Centrality (Q1)"
  ["$PPI_BC_Q2"]="Betweenness Centrality (Q2)"
  ["$PPI_BC_Q3"]="Betweenness Centrality (Q3)"
  ["$PPI_BC_Q4"]="Betweenness Centrality (Q4)"
  ["$RESTRICTION_SITES"]="Restriction Sites (length >= 5 bp)"
  ["$PROTEIN_FEATURE1_GFF"]="Virulence (VFDB experimentally validated)"
  ["$PROTEIN_FEATURE2_GFF"]="TTD novel"
  ["$PROTEIN_FEATURE3_GFF"]="Non paralog"
  ["$PROTEIN_FEATURE4_GFF"]="Human non-homologs"
  ["$PROTEIN_FEATURE5_GFF"]="Essential (DEG)"
  ["$PROTEIN_FEATURE6_GFF"]="Drugbank novel"
  ["$PROTEIN_FEATURE7_GFF"]="Betweenness (top 10%)"
  ["$PROTEIN_FEATURE8_GFF"]="Anti-target"
)

for gff_path in "${!TRACKS[@]}"; do
  if [[ ! -f "$gff_path" ]]; then
    echo "⚠️ Warning: File not found: $gff_path"
    continue
  fi

  track_name="${TRACKS[$gff_path]}"
  ext="${gff_path##*.}"
  base=$(basename "$gff_path" ".$ext")
  track_id=$(echo "$base" | tr '.' '-' | tr ' ' '_' | tr '[:upper:]' '[:lower:]')

  jbrowse add-track "$gff_path" \
    --load copy \
    --trackId "$track_id" \
    --name "$track_name" \
    --category "$track_name" \
    --force
done

# === Launch local admin server ===
echo "🚀 Launching genome browser at http://localhost:9090"
jbrowse admin-server start

