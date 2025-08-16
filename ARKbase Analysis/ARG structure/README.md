# ARG Structure Module

This readme provides instructions on how the data was generated for this module using foldseek.  
Official repository: [Foldseek GitHub](https://github.com/steineggerlab/foldseek)

---

## Installation

Install Foldseek via Bioconda:
```bash
conda install bioconda::foldseek
```

## Download Databases

Check available databases:
```bash
foldseek databases -h
```
### Download the PDB Database
```bash
foldseek databases PDB pdb tmp
```

## Query Protein Structures

- Accepted formats: `.pdb` or `.cif`  (from **PDB** or **AlphaFold DB**)

## Search Against Foldseek Database (PDB)

```bash
foldseek easy-search AFDB_Mab-4917 <query> \
	fs_db/PDB_foldseek <target> \
    Mab_AFDB_Vs_PDB_GBH.m8 <result> tmp \
    --greedy-best-hits 1 \
    --format-output query,target,pident,qcov,tcov,qstart,qend,qlen,tstart,tend,tlen,alnlen,evalue,bits,lddt,lddtfull,qtmscore,ttmscore,alntmscore,rmsd,prob \
    --format-mode 4 \
    --remove-tmp-files 0
```

## ProstT5 Integration

### Download ProstT5 Model

```bash
foldseek databases ProstT5 weights tmp
```
### Create a Foldseek Database with ProstT5
```bash
foldseek createdb db.fasta db --prostt5-model weights
```

### Search with Sequence Input Against PDB
```bash
foldseek databases ProstT5 weights tmp
foldseek databases PDB pdb tmp
foldseek easy-search QUERY.fasta pdb res.m8 tmp --prostt5-model weights
```
