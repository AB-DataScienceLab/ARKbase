<?php include 'header.php'; ?>

<main class="main-content">
    <div class="container">
        <h3 class="mb-4 mt-2" style="font-weight: 600;">
            <i class="bi bi-body-text" style="margin-right: 8px;"></i>BLAST Analysis Results
        </h3>

<?php
// --- Configuration ---
$blast_path = '/usr/bin/'; 
$blast_db_base_path = '/var/www/html/anshu/arkbase/blastdbs/';
$tmp_dir = sys_get_temp_dir();
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";

// --- Input Validation ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST' 
    || empty($_POST['query_proteins']) 
    || empty($_POST['target_pathogen']) 
    || !is_array($_POST['target_pathogen'])
    || empty($_POST['query_category'])) {
    
    echo '<div class="alert alert-danger">Error: Invalid input. Please go back and select at least one query protein and one target organism.</div>';
    include 'footer.php';
    exit();
}

$query_protein_ids = $_POST['query_proteins'];
$target_pathogen_slugs = $_POST['target_pathogen']; 
$query_category = $_POST['query_category'];

// --- STEP 1: FETCH QUERY PROTEIN SEQUENCES & CREATE ID MAP ---
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    echo '<div class="alert alert-danger">Error: Could not connect to the database.</div>';
    include 'footer.php';
    exit();
}

$fasta_query_string = "";
$id_map = [];
$placeholders = implode(',', array_fill(0, count($query_protein_ids), '?'));
$sql = "SELECT unique_id, prot_id, protein_seq FROM protein_search WHERE unique_id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$types = str_repeat('s', count($query_protein_ids)); 
$stmt->bind_param($types, ...$query_protein_ids);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $fasta_query_string .= ">" . htmlspecialchars($row['unique_id']) . "\n";
        $fasta_query_string .= wordwrap($row['protein_seq'], 60, "\n", true) . "\n";
        $id_map[$row['unique_id']] = $row['prot_id'];
    }
} else {
    echo '<div class="alert alert-warning">No query proteins found for the given IDs.</div>';
    $conn->close();
    include 'footer.php';
    exit();
}
$stmt->close();

// --- STEP 2: PREPARE AND RUN BLAST ---
$query_file = tempnam($tmp_dir, 'blast_query_');
file_put_contents($query_file, $fasta_query_string);

$db_map = ['amr' => 'arg/AMR_proteins_db', 'vf' => 'vf/VF_proteins_db', 'dt' => 'dt/DT_proteins_db'];
if (!isset($db_map[$query_category])) {
    echo '<div class="alert alert-danger">Invalid query category specified.</div>';
    unlink($query_file);
    $conn->close();
    include 'footer.php';
    exit();
}
$target_db = $blast_db_base_path . $db_map[$query_category];
if (!file_exists($target_db . '.pin')) {
    echo '<div class="alert alert-danger">BLAST database not found on the server. Please contact an administrator.</div>';
    unlink($query_file);
    $conn->close();
    include 'footer.php';
    exit();
}

$combined_seqidlist_file = tempnam($tmp_dir, 'blast_combined_ids_');
$found_any_id_file = false;
foreach ($target_pathogen_slugs as $slug) {
    $individual_seqidlist_file = $blast_db_base_path . $slug . '_master_ids.txt';
    if (file_exists($individual_seqidlist_file)) {
        $ids = file_get_contents($individual_seqidlist_file);
        file_put_contents($combined_seqidlist_file, $ids . PHP_EOL, FILE_APPEND);
        $found_any_id_file = true;
    }
}

if (!$found_any_id_file) {
    echo '<div class="alert alert-warning">Could not find the necessary ID list files for the selected target organisms.</div>';
    unlink($query_file);
    unlink($combined_seqidlist_file);
    $conn->close();
    include 'footer.php';
    exit();
}

// Using default E-value (10) by not specifying the -evalue flag.
$blast_command = $blast_path . 'blastp' .
                 ' -query ' . escapeshellarg($query_file) .
                 ' -db ' . escapeshellarg($target_db) .
                 ' -seqidlist ' . escapeshellarg($combined_seqidlist_file) .
                 ' -outfmt "6 qseqid sseqid pident length mismatch gapopen qstart qend sstart send evalue bitscore"';

$blast_output = shell_exec($blast_command . ' 2>&1');

// --- STEP 3: DISPLAY RESULTS ---
unlink($query_file);
unlink($combined_seqidlist_file);

if (strpos($blast_output, 'BLAST Database error:') !== false) {
    echo '<div class="alert alert-danger">An error occurred during the BLAST search. Details: <pre>' . htmlspecialchars($blast_output) . '</pre></div>';
} elseif (empty(trim($blast_output))) {
    echo '<div class="alert alert-info">No significant hits were found for your query proteins against the selected target organisms.</div>';
} else {
    $display_pathogen_names = array_map(function($slug) {
        return htmlspecialchars(ucfirst(str_replace('_', ' ', $slug)));
    }, $target_pathogen_slugs);
    
    echo '<h4>BLAST Hits (Tabular Format)</h4>';
    echo '<p>Showing results for selected query proteins against <strong>' . implode(', ', $display_pathogen_names) . '</strong> from the <strong>' . strtoupper($query_category) . '</strong> database.</p>';
    
    // --- MODIFICATION START: Inform user about the sorting ---
    echo '<p class="text-muted"><i class="bi bi-sort-down"></i> Results are sorted by <strong>Bit Score</strong> (highest first) to show the most significant hits at the top.</p>';
    // --- MODIFICATION END ---

    echo '<div class="my-3"><button id="download-btn" class="btn btn-success"><i class="bi bi-download" style="margin-right: 5px;"></i>Download Results (TSV)</button></div>';

    $headers = ['Query ID (prot_id)', 'Subject ID (prot_id)', '% Identity', 'Length', 'Mismatch', 'Gap Open', 'Q. start', 'Q. end', 'S. start', 'S. end', 'E-value', 'Bit Score'];
    
    // --- MODIFICATION START: Parse, Sort, and then Display the results ---

    // 1. Parse the raw output into an array
    $results_data = [];
    $lines = explode("\n", trim($blast_output));
    foreach ($lines as $line) {
        if(empty(trim($line))) continue;
        $fields = explode("\t", $line);
        
        // Before adding, swap the unique_id for the more readable prot_id
        $query_uid = $fields[0];
        $fields[0] = isset($id_map[$query_uid]) ? $id_map[$query_uid] : $query_uid;
        
        $results_data[] = $fields;
    }

    // 2. Sort the array by Bit Score (column index 11) in descending order
    // The Bit Score is the last column in our -outfmt string.
    usort($results_data, function($a, $b) {
        // Cast to float for accurate numeric comparison
        $bitScoreA = (float) $a[11];
        $bitScoreB = (float) $b[11];
        
        // For descending order, we want $b compared to $a
        return $bitScoreB <=> $bitScoreA;
    });

    // 3. Generate the downloadable content AND the table from the *sorted* array
    $downloadable_content = implode("\t", $headers) . "\n";

    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-bordered table-sm">';
    echo '<thead class="thead-dark"><tr>';
    foreach ($headers as $header) {
        echo '<th>' . $header . '</th>';
    }
    echo '</tr></thead><tbody>';

    foreach ($results_data as $fields) {
        // Add the sorted row to the downloadable file content
        $downloadable_content .= implode("\t", $fields) . "\n";

        // Display the sorted row in the HTML table
        echo '<tr>';
        foreach ($fields as $field) {
            echo '<td>' . htmlspecialchars($field) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table></div>';
    
    // --- MODIFICATION END ---
    
    echo '<div id="blast-tsv-data" style="display:none;">' . htmlspecialchars($downloadable_content) . '</div>';
}
$conn->close();
?>

        <div class="mt-4">
            <a href="comparative_analysis.php" class="btn btn-secondary">Back to Analysis Page</a>
        </div>
    </div>
</main>

<!-- JavaScript for download button (No changes needed here) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.getElementById('download-btn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            const tsvData = document.getElementById('blast-tsv-data').textContent;
            const blob = new Blob([tsvData], { type: 'text/tab-separated-values;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'blast_results_sorted.tsv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
});
</script>

<?php include 'footer.php'; ?>