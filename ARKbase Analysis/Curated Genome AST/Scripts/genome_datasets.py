import pandas as pd
import subprocess

file = "/home/dell/ark_base/Enterococcus_faecium/assembly_accession.csv"
df = pd.read_csv (file)
accessions = df["Assembly accession"].tolist()
for accession in accessions:
    filename = f"{accession}.zip"
    
    subprocess.run(["datasets",  "download", "genome", "accession" , accession,"--include", "gff3,cds,protein,genome", "--filename" , filename])
    

