# Genome Browser Module


## Workflow Overview

### Step 1: Retrieve GFF Files  
GFF files for each pathogen are retrieved from NCBI.

### Step 2: Merge Data with GFFs  
TSV files (per module, e.g., AMR, Operon, PPI) for 14 pathogens—containing key identifiers like `protein_id`—are mapped to their corresponding NCBI GFFs to obtain genomic coordinates and supplementary information.

### Step 3: Convert to GFF3  
The merged TSV data are formatted as GFF3 files.

### Step 4: Visualize with JBrowse 2  
The generated GFF3 files are used as inputs to `genome_browser_run.sh` (customized per pathogen). Outputs are visualised  via **JBrowse 2**.