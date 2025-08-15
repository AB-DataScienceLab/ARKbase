import pandas as pd
from Bio import SeqIO
import os
import re


with open('/home/anshu/upasana/Mitoinfect/Orthologs/eggnog-mapper/data/e5.proteomes.faa', 'r') as fp:
    faa_seqs = list(SeqIO.parse(fp, 'fasta'))            
    fp.close()

SEQs = {rec.id: str(rec.seq) for rec in faa_seqs}
SEQ_half= {k.split('.')[1]:v for k,v in SEQs.items()}

if not os.path.exists('fetched_seqs'):
    os.mkdir('fetched_seqs')

L = []
for file_ortho in os.listdir('filtered_output'):
    print(file_ortho)

    D = pd.read_csv(f"filtered_output/{file_ortho}")
    if D.shape[0] == 0:
        L.append({'file': file_ortho, 'total': 0, 'found': 0, 'not_found_ids': 0})
        continue
    D['ID'] = D.apply(lambda x: x['orthologs'].lstrip('*'), axis=1)
    D['seq'] = D['ID'].map(SEQ_half)

    dd = D[~D['seq'].isna()]
    with open('fetched_seqs/' + file_ortho + '.faa', 'w') as fp:
        for i, row in dd.iterrows():
            fp.write(f">{row['ID']}\n{row['seq']}\n")
        fp.close()

    L.append({'file': file_ortho, 'total': D.shape[0], 'found': dd.shape[0], 
              'not_found_ids': ';'.join(D[D['seq'].isna()]['ID'].tolist())})

pd.DataFrame(L).to_csv('Protein_Seqsummary.csv', index=False)
