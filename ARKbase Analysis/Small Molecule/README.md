# Small Molecules Module

This module provides data and tools related to biosynthetic gene clusters (BGCs), known antibiotics, and molecular structure similarity search for 14 WHO Bacterial Priority Pathogens (BPPs).

---

## 1. BGCs
**Methodology:**
- **Genome Retrieval:** Reference genome sequences (.gb) of 14 BPPs were obtained from NCBI RefSeq.
- **BGC Prediction:** antiSMASH 8.0 was used to predict genomic regions linked to secondary metabolite production.
- **Clustering:** BiG-SCAPE 2.0 was used to cluster BGCs into Gene Cluster Families (GCFs) using:
  - Mode: `cluster`
  - Classify on: `category groups`
  - Weights: `mix`
  - Alignment mode: `glocal`
  - Extend strategy: `legacy`
  - Include singletons: `yes`
  - Include categories: `all`
  - Exclude categories: `none`
  - Include classes: `all`
  - Exclude classes: `none`

---

## 2. Known Antibiotics
**Methodology:**
- **Data Retrieval:** Antibiotics and MICs for BPPs from CLSI resources.
- **AWaRe Classification:** Categorized according to WHO AWaRe 2023 (Access, Watch, Reserve).
- **Structural Information:** SMILES strings from PubChem.


---

## 3. Structure Search
**Methodology:**
- **Input:** SMILES, SDF, MOL2, or drawing via the molecular editor(JSME tool).
- **Fingerprint Generation:** Morgan fingerprints (1024 bits, radius 2).
- **Similarity Calculation:** Tanimoto coefficient (threshold ≥ 0.8).
- **Output:** Ranked hits with Tanimoto score and pathogen context.

---

## Scripts Used

1. **Known Antibiotics**
   - `curate_antibiotics.R` — Compile MIC data and AWaRe categories then adding SMILES from pubchem.  
2. **Structure Search**
   - `structure_search.py` — Convert input to fingerprints, compute Tanimoto, return ranked results.

