#!/usr/bin/env perl
use strict;
use warnings;
use Text::CSV;

########################################################################################
# Efficient X-mer Finder with CSV output
########################################################################################

my ($inputfasta, $FASTADB, $Xmer, $outfile) = @ARGV;

unless (defined $inputfasta && defined $FASTADB && defined $Xmer) {
    print "Usage: $0 Query.faa HumanDB.faa XmerSize Output.csv\n";
    exit;
}

unless (defined $outfile) {
    print "Output file name=";
    chomp($outfile = <>);
}

# Read FASTAs
print "Reading FASTA files...\n";
my $inhashref = read_FASTA_hash($inputfasta);
my $DBref     = read_FASTA_hash($FASTADB);
print "Done reading.\n";

my %query = %$inhashref;
my %db    = %$DBref;

# Index human proteins
print "Indexing human protein DB...\n";
my %db_xmer_index;
foreach my $protein_id (keys %db) {
    my $seq = $db{$protein_id};
    for (my $i = 0; $i <= length($seq) - $Xmer; $i++) {
        my $xmer = substr($seq, $i, $Xmer);
        push @{ $db_xmer_index{$xmer} }, $protein_id;
    }
}
print "Index complete. Total indexed X-mers: ", scalar(keys %db_xmer_index), "\n";

# Open CSV
open my $MATCH, ">", $outfile or die "Cannot write to $outfile: $!";
my $csv = Text::CSV->new({ binary => 1, eol => "\n" });
$csv->print($MATCH, ["QueryProteinID", "HitCount", "MatchingPeptides", "MatchedProteinsByPeptide"]);

# Search query peptides
foreach my $q (keys %query) {
    my $pref = createXmer($query{$q}, $Xmer);
    my @peptides = @$pref;

    my %peptide_to_proteins;

    foreach my $pep (@peptides) {
        if (exists $db_xmer_index{$pep}) {
            my %proteins = map { $_ => 1 } @{ $db_xmer_index{$pep} };
            $peptide_to_proteins{$pep} = \%proteins;
        }
    }

    my @hit_peptides = keys %peptide_to_proteins;
    my $hit_count    = scalar @hit_peptides;
    my $peptides_str = join(",", @hit_peptides);

    my @grouped;
    foreach my $pep (@hit_peptides) {
        my @pids = sort keys %{ $peptide_to_proteins{$pep} };
        push @grouped, "$pep: " . join(";", @pids);
    }

    my $proteins_str = join(" | ", @grouped);
    $csv->print($MATCH, [$q, $hit_count, $peptides_str, $proteins_str]);
}

close $MATCH;
print "...Done writing $outfile\n";

########################################################################################
sub read_FASTA_hash {
    my ($fasta) = @_;
    open my $F, "<", $fasta or die "Could not read $fasta: $!";
    local $/ = "\n>";
    my %hash;
    while (my $entry = <$F>) {
        $entry =~ s/^>//;
        my ($header, @seq_lines) = split /\n/, $entry;
        my $seq = join("", @seq_lines);
        $hash{$header} = $seq;
    }
    close $F;
    return \%hash;
}

sub createXmer {
    my ($seq, $Xmer) = @_;
    my @out;
    for (my $i = 0; $i <= length($seq) - $Xmer; $i++) {
        push @out, substr($seq, $i, $Xmer);
    }
    return \@out;
}

