#!usr/bin/perl -w

#Before running the prgram -
#Install emboss via conda in a conda environment using the commands below 
# conda install -c bioconda emboss
#ru rebaseextract and provided downloaded withrefm file and protofile on prompt
#these files may be obtained/copy pasted from 
#withrefm - http://rebase.neb.com/rebase/link_withrefm
#proto - http://rebase.neb.com/rebase/link_proto


#run the program by typing perl restrict.pl
 
#The results file will be formed on the restrict directory within the current directory
#note that minor alterations may be required depending on pattern of filenames 

#collect all fasta files 
@fasta_files = glob("*.fasta");
print "fasta files found (glob):\n";
$resultdirectory="restrict";
mkdir $resultdirectory;

#Repeat for all files
foreach my $filename (@fasta_files) {
	@spl = split('\.', $filename);
	#output filename 
	$res=$spl[0].".".$spl[1];
	print $res,"\n";
#Call restrict from EMBOSS with the given parameters
`restrict -sequence $filename -sitelen 5 -ambiguity N -rdirectory $resultdirectory -rname $res -auto`
}