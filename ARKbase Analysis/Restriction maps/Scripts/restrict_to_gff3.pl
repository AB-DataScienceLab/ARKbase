#!usr/bin/perl -w
#create an array of files ending with extension .restrict
@files = glob("*.restrict");
print "fasta files found (glob):\n";

#for each file 
foreach my $filename (@files) {
	print $filename,"\n";
	
	#output file name
	@spl = split('\.', $filename);
	$res=uc($spl[0].".".$spl[1]);
	$res1=$res.".gff3";


open(FH,$filename) || die "Cannot open file";

open(OUT,">split4/$res1") || die "Cannot open file" ;
print OUT "##gff-version 3\n";
while ($line=<FH>)
{
#regex to match line in restrict file from emboss restrict
	if ($line=~/\s+(\d+)\s+(\d+)\s+([+-])\s+(\w+)\s+(\w+)\s+.*/)
	{
		#writing results on gff3 format	
		print OUT $res,"\t","EMBOSS-RESTRICT","\t","RE","\t",$1,"\t",$2,"\t",".","\t",$3,"\t",".","\t","RS=",$5,";","Name=",$4,"\n";
	}

}

#sort the file as per genomic coordinates

 `sort -n -k4 split4/$res1 >split4/sorted/$res1`;
close FH;
close OUT;
}