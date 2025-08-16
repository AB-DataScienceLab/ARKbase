#!usr/bin/perl -w

#Before running the prgram -
#Install isescan via conda in a conda environment using the commands below 
# conda install -c bioconda isescan
#run the program by typing perl isescan.pl 
#note that minor alterations may be required depending on pattern of filenames 


#collect all fasta/fna files 
@fasta_files = glob("*.fna");
$directory="results";
mkdir $directory
print "fasta files found (glob):\n";

#Repeat for all files
foreach my $filename (@fasta_files) 
{
	@spl = split('_', $filename);
	#output filename 
	$res=$spl[0].".".$spl[1];
	print $res,"\n";
#Call restrict from EMBOSS with the given parameters
`isescan.py --seqfile $filename --output $directory/$res --nthread 2`
}