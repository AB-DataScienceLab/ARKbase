import pandas as pd
from Bio import SeqIO
import os
import re
from threading import Thread
from queue import Queue

Q = Queue(maxsize = 30)

def main():
    while True:
        try:
            fn = Q.get(timeout = 10)
        except:
            break


        D = pd.read_csv(fn)
        D['ID'] = D.apply(lambda x: re.search(r"\(([^\)]*)\)", x['species']).group(1) + '.' + x['orthologs'].lstrip('*'), axis=1)
        print(D['ID'])

        with open(f"{fn.replace('_filtered_orthologs.csv', '')}.tsv", 'wb') as outfile:
            with open(f"{fn.replace('_filtered_orthologs.csv', '')}_error.tsv", 'wb') as error_file:
                with open("/home/anshu/upasana/Mitoinfect/Orthologs/eggnog-mapper/ARKbase/Escherichia coli/early/eggnog_output/Sequence_aliases/e5.sequence_aliases.tsv", 'rb') as infile:
                    for line in infile:
                        try:
                            s = line.decode('utf-8').strip(' \n')
                            lc = s.split('\t')[-1]
                        except:
                            error_file.write(line)
                            continue
                        if lc != 'RefSeq':
                            continue

                        matched = False
                        for i in D["ID"].values:
                            if i in s:
                                matched = True
                                break
                        
                        if matched:
                            print(s)
                            outfile.write(line)

nthreads = min(15, len(list(filter(lambda x: x.endswith(".csv"),os.listdir(".")))))
threads = [Thread(target = main) for _ in range(nthreads)] 
files = []

for t in threads:
    t.start()


for f in os.listdir('.'):
    if f.endswith('.csv'):
        files.append(f)
        
for f in files:
    Q.put(f)
    
for t in threads:
    t.join()