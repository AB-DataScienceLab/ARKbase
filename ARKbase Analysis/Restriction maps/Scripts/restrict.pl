#!usr/bin/perl -w

#collect all fasta files 
@fasta_files = glob("*.fasta");
print "fasta files found (glob):\n";

#Repeat for all files
foreach my $filename (@fasta_files) {
	@spl = split('\.', $filename);
	#output filename 
	$res=$spl[0].".".$spl[1];
	print $res,"\n";
#Call restrict from EMBOSS with the given parameters
`restrict -sequence $filename -sitelen 5 -ambiguity N -rdirectory restrict2 -rname $res -auto`
}