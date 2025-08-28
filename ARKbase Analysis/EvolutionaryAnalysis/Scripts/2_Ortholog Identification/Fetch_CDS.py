import os
import pandas as pd
from Bio import SeqIO
import traceback
from Bio.SeqRecord import SeqRecord
from threading import Thread
from queue import Queue

Q = Queue(maxsize = 10)

def fetch_NuclID(ID, tax_id):
    os.system(f'esearch -db protein -query "{ID}" | elink -target nuccore | efetch -format acc > accession-{ID}.txt')
    with open(f'accession-{ID}.txt', 'r') as f:
        accessions = list(map(lambda x : x.strip('\n'), f.readlines()))
    os.remove(f'accession-{ID}.txt')

    for accession in accessions:
        os.system(f'esearch -db nucleotide -query "{accession}" | efetch -format fasta_cds_na > cds_{accession}.fasta')
        records = list(SeqIO.parse(f'cds_{accession}.fasta', 'fasta'))
        os.remove(f'cds_{accession}.fasta')
        found_record = None
        for record in records:
            if ID in record.id:
                found_record = record
                break
        if found_record:
            print(f"✅ Found CDS for {ID} in {accession}")
            found_record.id = tax_id
            return SeqRecord(found_record.seq, id=tax_id, description="")

    return None

def main():
    while True:
        try:
            fn = Q.get(timeout = 10)
        except:
            break

        df = pd.read_csv(fn, header=None, names=['tax_id',  'id',   'source'], sep='\t')
        # df1 = df[df['source'].str.startswith('RefSeq')].copy(True).reset_index(drop=True)
        df1 = df[df['source'] == 'RefSeq'].copy(True).reset_index(drop=True)

        df1['outputID'] = df1['tax_id'].apply(lambda x: x.split('.')[1])

        m= []

        for i in range(len(df1)):
            try:   
                out = fetch_NuclID(df1['id'][i], df1['outputID'][i])
            except:
                traceback.print_exc()
                print(f"❌ Try Manually unexpected error {df1['id'][i]}")
                continue
            if out:
                m.append(out)
            else:
                print(f"❌ No CDS found for {df1['id'][i]}")
        SeqIO.write(m, f"{fn}_CDS.fasta", 'fasta')

threads = [Thread(target = main) for _ in range(3)] 
files = []
for f in os.listdir('.'):
    if f.endswith('.tsv'):
        files.append(f)

for t in threads:
    t.start()
for f in files:
    Q.put(f)
    
for t in threads:
    t.join()