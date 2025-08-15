# Evolutionary Analysis Pipeline

This section contains scripts for running the evolutionary analysis pipeline.  
The scripts are organized in the `scripts/` directory under the following folders:

---

## **1_Sequence Extraction**
**Scripts:**
- `fasta_extract.py` — Sequence extraction from Reference Proteome multi-fasta.

---

## **2_Ortholog Identification**

**Environment Setup:**
```bash
conda activate eggnog   # Set Environment
export PATH=/home/user/eggnog-mapper:/home/user/eggnog-mapper/eggnogmapper/bin:"$PATH"   # Set Path
export EGGNOG_DATA_DIR=/home/anshu/upasana/Mitoinfect/Orthologs/eggnog-mapper/data  # Set data directory
```

**Steps:**
```bash
create_dbs.py -m diamond --dbname eggnog_Bacteria --taxa Bacteria   # Create Database
eggnog_automate.py   # Ortholog identification from eggNOG
one2one_hits.py      # Extract One-to-One orthologs from eggNOG output
FetchProtSeq.py      # Fetch protein sequences of Orthologs from eggNOG database raw files
SeqAliases.py        # Get sequence aliases of Ortholog identifiers and keep only RefSeq IDs
Fetch_CDS.py         # Fetch CDS using RefSeq sequence aliases
```

## **3_Evolutionary Analysis**

**Preparation:**
```bash
seqkit seq -w 0 {ID}_FetchedCDS.fasta > {ID}_CDS.fasta   # Make single-line FASTA
files conda activate hyphy_env  # Dependencies: ClustalW, pal2nal, iqtree2, hyphy
```

**Steps:**
```bash
DataPreprocessing.py   # Filter out common Protein and CDS sequences to do evolutionary analysis
Automation_Evo.py      # Automation of evolutionary analysis
```

**Source scripts used in `Automation_Evo.py`:**
- `gap.py` (In-house script)
- `iqtree2`
- `pal2nal.pl`

**Note:** Visualize results using **HyPhy Vision**.

## **4_SelectionSites Figures**

**Scripts:**

- `Figures_Evo.py` — Plot interactive visualization of sites under selection pressure and Pfam domains (using InterPro) in sequences.

---
## **Requirements & Dependencies**

### **Programming Language**

- **Python** ≥ 3.8

#### **Python Libraries**

The following Python packages are required:

- `biopython`
- `pandas`
- `numpy`
- `plotly`
- `os` _(built-in)_
- `subprocess` _(built-in)_
- `glob` _(built-in)_
- `re` _(built-in)_
- `pathlib` _(built-in)_
- `traceback` _(built-in)_
- `threading` _(built-in)_
- `queue` _(built-in)_

Install Python dependencies with:
```bash
pip install biopython pandas numpy plotly
```

#### **Command-line Tools**

Ensure the following tools are installed and available in `PATH`:
- **eggNOG-mapper**
- **diamond**
- **seqkit**
- **ClustalW**
- **pal2nal**
- **IQ-TREE 2**
