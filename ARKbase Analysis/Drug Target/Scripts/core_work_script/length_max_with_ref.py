import csv
import os
from glob import glob
from Bio import SeqIO


REFERENCE_FASTA = "/media/snp/drive/AB/GCF_009035845.1_protein.faa"

INPUT_PATTERN   = "/media/snp/drive/AB/first_seqtk/*.csv_process2.faa"


ref_records = {rec.id for rec in SeqIO.parse(REFERENCE_FASTA, 'fasta')}


for aa6_path in glob(INPUT_PATTERN):
    filename = os.path.basename(aa6_path)
    
    suffix = '.csv_process2.faa'
    if filename.endswith(suffix):
        base = filename[:-len(suffix)]
    else:
      
        base, _ = os.path.splitext(filename)
    output_file = f"{base}_ll.lst"
    log_file    = f"{base}_log.txt"

    
    entries = []
    with open(aa6_path) as f:
        for line in f:
            parts = line.strip().split()
            if len(parts) < 2:
                continue
            locus, length = parts[0], int(parts[1])
            entries.append((locus, length))

    if not entries:
        print(f"WARNING: no entries in {aa6_path}")
        continue

   
    max_len    = max(length for _, length in entries)
    candidates = [(locus, length) for locus, length in entries if length == max_len]

   
    chosen = None
    with open(log_file, 'w') as log:
        ref_candidates = [locus for locus, _ in candidates if locus in ref_records]
        if ref_candidates:
            chosen = ref_candidates[0]
        else:
            chosen = candidates[0][0]
            log.write(f"No reference hit at length {max_len}; chosen non-ref: {chosen}\n")

    
    with open(output_file, 'w') as out:
        out.write(f"{chosen}\n")

    print(f"Processed {filename}: selected {chosen}, wrote to {output_file}, log {log_file}")
