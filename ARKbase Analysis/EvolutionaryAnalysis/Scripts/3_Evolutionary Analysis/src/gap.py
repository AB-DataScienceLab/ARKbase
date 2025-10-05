from Bio import SeqIO
from Bio.Seq import Seq
import argparse

argparser = argparse.ArgumentParser()
argparser.add_argument("--input_file", type=str, required=True)
argparser.add_argument("--output_file", type=str, required=True)

args = argparser.parse_args()
input_file = args.input_file
output_file = args.output_file


def remove_codon_with_gaps(input_file, output_file):
    # Read the alignment file
    records = list(SeqIO.parse(input_file, "fasta"))
    seq_length = len(records[0].seq)
    
    if seq_length % 3 != 0:
        raise ValueError("The sequence length is not a multiple of 3. Check the alignment file.")
    
    # Identify codons with gaps
    codon_positions_to_keep = []
    for i in range(0, seq_length, 3):
        codon = [str(record.seq[i:i+3]) for record in records]
        if all('-' not in c for c in codon):
            codon_positions_to_keep.extend([i, i+1, i+2])  # Keep the entire codon if no gaps
    
    # Filter the sequences to remove codons with gaps
    filtered_records = []
    for record in records:
        filtered_seq = "".join([record.seq[i] for i in codon_positions_to_keep])
        
        # Check if filtered sequence length is a multiple of 3
        if len(filtered_seq) % 3 != 0:
            raise ValueError(f"Filtered sequence for {record.id} is not a multiple of 3.")
        
        record.seq = Seq(filtered_seq)  # Convert back to Seq object
        filtered_records.append(record)
    
    # Write the filtered alignment to the output file
    SeqIO.write(filtered_records, output_file, "fasta")
    print(f"Filtered alignment saved to {output_file}")


# Input and output file names
#input_file = "2_pal2nalOutput.fa"   # Replace with your input file name
#output_file = "filtered_2_pal2nalOutput.fa"  # Replace with desired output file name

# Run the function
remove_codon_with_gaps(input_file, output_file)

