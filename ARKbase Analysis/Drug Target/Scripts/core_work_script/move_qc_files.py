import os
import shutil
import pandas as pd


csv_file = "pgaped_accessions.csv"
source_folder = "/media/snp/drive/AB/faa_files"
destination_folder = "/media/snp/drive/AB/pgaped_faa_files"


df = pd.read_csv(csv_file)
accessions = df.iloc[:, 0].astype(str).tolist()

os.makedirs(destination_folder, exist_ok=True)

for file in os.listdir(source_folder):
    if file.endswith(".faa"):
        for accession in accessions:
            if accession in file:
                src = os.path.join(source_folder, file)
                dst = os.path.join(destination_folder, file)
                shutil.move(src, dst)
                print(f"Moved: {file}")
                break 

