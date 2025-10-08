import os

directory = "/media/snp/drive/AB/core_genes/cleaned_files/processed_files"

for filename in os.listdir(directory):
    if filename.endswith("_processed.lst"):
        input_path = os.path.join(directory, filename)
        output_filename = filename.replace("_processed.lst", "_process2.lst")
        output_path = os.path.join(directory, output_filename)

        with open(input_path, 'r') as infile, open(output_path, 'w') as outfile:
            for line in infile:
                new_line = line.replace('cds-', '')
                outfile.write(new_line)
