# Identification of Drug Targets in WHO BPPL

The reference proteomes of the considered WHO BPPL were retrieved from NCBI RefSeq database. For the identification of drug targets, a series of analyses were carried out.

## i) Non-homology analysis
The reference proteomes of the BPPL were searched against the human proteome sequences using easy-search in MMseq2 with greedy-best-hits flag. The obtained results were filtered based on the criteria:
- E-value > 10^-4
- Sequence identity < 35%
- Query coverage < 75%  

Proteins fulfilling these criteria were considered as **non-homologous proteins**.

## ii) Essentiality prediction
The reference proteomes of the BPPL were searched against the essential proteins from DEG database using easy-search in MMseq2 with greedy-best-hits flag.  
The obtained results were filtered based on the criteria:
- E-value < 10^-5  
If the query protein had a hit, it was considered as an **essential protein**.

## iii) Centrality analysis
The PPI data for all the BPPL were retrieved from STRING database with confidence ≥ 0.700.  
The network centralities were computed using NetworkX, and the proteins in the **top 10% of betweenness centrality** were considered as **central/hub proteins**.

## iv) Non-paralog prediction
The proteomes of the considered WHO BPPL were clustered individually using CD-hit with identity % ≥ 0.8.  
Proteins clustered as singletons were considered as **non-paralogous proteins**.

## v) Anti-target analysis
The reference proteome sequences of the BPPL were searched against the human proteome with a segment of eight amino acids to identify similar peptides that could match a specific fold or pocket in the host proteome.  
This was carried out using an in-house PERL script, identifying pathogen proteins that **do not have any matching 8-mers**.

## vi) Druggability analysis
The druggability analysis was carried out considering two databases: **DrugBank (DB)** & **TTD**.  
Approved/successful drug targets from DB & TTD were considered for similarity search against the reference proteome of the WHO BPPL.  
The similarity search was performed using MMseqs2 with greedy-best-hits flag, and the results were filtered using the criteria:
- E-value < 10^-5  

Pathogen proteins without a hit were considered as **novel targets** based on the absence of similar proteins in the database.

## vii) Comparison with core proteome
The pan-genome analysis for all the WHO BPPL was carried out using **panroo**, and the core proteins were identified.  
Proteins identified from panroo from the reference proteome were considered as **core proteins**.

## viii) Virulence prediction
The virulence proteins of the considered reference proteome of WHO BPPL were predicted using a similarity search against **VFDB (setA)**.  
The similarity search was carried out using MMseqs2 with greedy-best-hits flag, and the results were filtered based on:
- E-value < 10^-5

## ix) Involvement in AMR
The reference proteins involved in AMR were identified using **CARD RGI**.

---

## Commands

### For similarity search using MMseqs2
```bash
mmseqs easy-search query_proteins.fasta target_db.fasta result.m8 tmp --greedy-best-hits 1 ----format-mode 4 --format-output query,target,pident,qcov,tcov,qlen,nident,mismatch,ppos,evalue,bits
```

### Anti-target analysis
```bash
perl anti_target_search.pl query_proteins.fasta human_proteome.fasta 8 output.csv
```

---
## Requirements & Dependencies

### Software & Tools
- **MMseqs2** – for similarity searches (`easy-search` with `--greedy-best-hits`)
- **CD-HIT** – for non-paralog prediction
- **NetworkX** (Python) – for centrality analysis
- **panroo** – for pan-genome and core proteome identification
- **PERL** – for running the anti-target analysis script (`anti_target_search.pl`)
- **CARD RGI** – for antimicrobial resistance protein identification

### Databases
- **NCBI RefSeq** – reference proteomes of WHO BPPL
- **Human proteome** – for non-homology and anti-target analysis
- **DEG** – essential genes database
- **STRING** – protein-protein interaction data
- **DrugBank (DB)** – drug target information
- **TTD (Therapeutic Target Database)** – drug target information
- **VFDB (Virulence Factors Database)** – virulence factor prediction
- **CARD** – antimicrobial resistance genes
