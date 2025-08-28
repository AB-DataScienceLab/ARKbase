#!/usr/bin/env python3
def parse_fasta(fasta_file):
    """
    Parse a FASTA file and return a dictionary of headers to sequences.
    Headers are trimmed to exclude the '>' character.
    """
    sequences = {}
    current_header = None
    current_sequence = []
    
    with open(fasta_file, 'r') as f:
        for line in f:
            line = line.strip()
            if line.startswith('>'):
                # Save the previous sequence if there was one
                if current_header:
                    sequences[current_header] = ''.join(current_sequence)
                
                # Start a new sequence
                current_header = line[1:]  # Remove the '>' character
                current_sequence = []
            elif line:  # Skip empty lines
                current_sequence.append(line)
    
    # Save the last sequence
    if current_header:
        sequences[current_header] = ''.join(current_sequence)
    
    return sequences

def filter_proteins_by_cds(cds_fasta, protein_fasta, cds_output, protein_output):
    """
    Filter protein sequences to keep only those with corresponding CDS sequences.
    Output is written in single-line FASTA format.
    
    Args:
        cds_fasta: Path to the nucleotide CDS FASTA file
        protein_fasta: Path to the protein FASTA file
        cds_output: Path to write the filtered CDS FASTA
        protein_output: Path to write the filtered protein FASTA
    """
    # Parse both FASTA files
    cds_sequences = parse_fasta(cds_fasta)
    protein_sequences = parse_fasta(protein_fasta)
    
    # Count sequences for reporting
    total_proteins = len(protein_sequences)
    kept_proteins = 0
    
    # Open output file
    cds_f = open(cds_output, 'w')
    pro_f = open(protein_output, 'w')

    # Keep only protein sequences with matching CDS
    for header, sequence in protein_sequences.items():
        if header in cds_sequences:
            # Write header and complete sequence on the next line (single-line format)
            cds_f.write(f'>{header}\n{cds_sequences[header]}\n')
            pro_f.write(f'>{header}\n{sequence}\n')
            kept_proteins += 1
    
    cds_f.close()
    pro_f.close()
    
    print(f"Total protein sequences: {total_proteins}")
    print(f"Protein sequences with matching CDS: {kept_proteins}")
    print(f"Protein sequences without matching CDS (removed): {total_proteins - kept_proteins}")
    # print(f"Filtered sequences written to: {output_fasta}")

if __name__ == "__main__":
    import argparse
    
    # Parse command line arguments
    parser = argparse.ArgumentParser(description='Filter protein sequences to keep only those with corresponding CDS.')
    parser.add_argument('--cds', required=True, help='Path to the nucleotide CDS FASTA file')
    parser.add_argument('--protein', required=True, help='Path to the protein FASTA file')
    parser.add_argument('--proteinout', required=True, help='Path to write the filtered protein FASTA')
    parser.add_argument('--cdsout', required=True, help='Path to write the filtered cds FASTA')
    
    args = parser.parse_args()
    
    # Run the filter
    filter_proteins_by_cds(args.cds, args.protein, args.cdsout, args.proteinout)
