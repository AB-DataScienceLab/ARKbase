# Operon Prediction and Summary Module

This section contains script used to predicts operons from GFF files using **UniOP** and creating summary file.

---
## **Step 1 — Operon Prediction**
**Task:**  
Predict operons from GFF files using the UniOP tool.

**Inputs:**
- **Files:** `{Pathogen_name}_ref_genome.gff` (PGAP GFF files)

**Code:**
```bash
python uniop_arkbase.py
```

**Outputs:**
* `{Assembly_accession}_uniop.operon`
* `{Assembly_accession}_uniop.pred`
---
## **Step 2 — Operon Summary File Generation**
**Task:**  
Create an operon summary file using PGAP GFF files and UniOP `.operon` files.

**Inputs:**

- `{Pathogen_name}_ref_genome.gff`
* `{Assembly_accession}_uniop.operon`

**Code:**
```bash
python generate_op_sum_final.py
```
