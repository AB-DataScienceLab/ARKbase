#!usr/bin/perl -w
#Converting restrict result files to gff3 format


#create an array of files ending with extension .restrict
@files = glob("*.restrict");
print "fasta files found (glob):\n";
$sortedgff="sortedgff";
mkdir $sortedgff;
$resultdirectory="gff3";
mkdir $resultdirectory;
#for each file 
foreach my $filename (@files) {
	print $filename,"\n";
	
	#output file name
	@spl = split('\.', $filename);
	$res=uc($spl[0].".".$spl[1]);
	$res1=$res.".gff3";
    

open(FH,$filename) || die "Cannot open file";

open(OUT,">$resultdirectory/$res1") || die "Cannot open file" ;
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
$sortedgiff="sortedgiff";


 `sort -n -k4 $resultdirectory/$res1 >$resultdirectory/$sortedgiff/$res1`;
close FH;
close OUT;
}