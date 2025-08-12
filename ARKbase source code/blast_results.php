<?php include 'header.php'; ?>

<main class="main-content">
    <div class="container">
        <h3 class="mb-4 mt-2" style="font-weight: 600;">
            <i class="bi bi-body-text" style="margin-right: 8px;"></i>BLAST Results
        </h3>

<?php
// --- CONFIGURATION ---
$blast_path = '/usr/bin/'; 
$blast_db_base_path = '/var/www/html/anshu/arkbase/blastdbs/';
$tmp_dir = sys_get_temp_dir();

// --- 1. VALIDATE INPUT ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<div class="alert alert-danger">Error: Invalid request method.</div>';
    include 'footer.php';
    exit();
}

// Check for query sequence (either from file or textarea)
$query_file_path = '';
if (isset($_FILES['query_file']) && $_FILES['query_file']['error'] === UPLOAD_ERR_OK) {
    // A file was successfully uploaded
    $query_file_path = $_FILES['query_file']['tmp_name'];
} elseif (!empty(trim($_POST['query_sequence']))) {
    // Use the content from the textarea
    $query_file_path = tempnam($tmp_dir, 'blast_query_');
    file_put_contents($query_file_path, $_POST['query_sequence']);
} else {
    echo '<div class="alert alert-danger">Error: No query sequence provided. Please go back and enter a sequence or upload a file.</div>';
    include 'footer.php';
    exit();
}

// Validate database selection
$db_map = [
    'amr' => 'arg/AMR_proteins_db',
    'vf'  => 'vf/VF_proteins_db',
    'dt'  => 'dt/DT_proteins_db'
];
$selected_db_key = $_POST['blast_db'] ?? '';
if (!isset($db_map[$selected_db_key])) {
    echo '<div class="alert alert-danger">Error: Invalid database selected.</div>';
    unlink($query_file_path); // Clean up temp file
    include 'footer.php';
    exit();
}

// --- 2. CONSTRUCT THE BLAST COMMAND ---
$target_db_path = $blast_db_base_path . $db_map[$selected_db_key];

// Base command
$blast_command = $blast_path . 'blastp' .
                 ' -query ' . escapeshellarg($query_file_path) .
                 ' -db ' . escapeshellarg($target_db_path);

// Add user-selected parameters
$blast_command .= ' -evalue ' . escapeshellarg($_POST['expect_value'] ?? '1e-5');
$blast_command .= ' -matrix ' . escapeshellarg($_POST['matrix'] ?? 'BLOSUM62');

// Add filtering
if (isset($_POST['filter_low_complexity'])) {
    $blast_command .= ' -seg yes';
} else {
    $blast_command .= ' -seg no';
}

// We will use the default HTML output format (-outfmt 0 is default but we can add it for clarity)
$blast_command .= ' -html';

// --- 3. EXECUTE BLAST AND DISPLAY RESULTS ---
// Execute the command and capture both standard output and errors
$blast_output = shell_exec($blast_command . ' 2>&1');

// Clean up the query file (whether uploaded or created from textarea)
unlink($query_file_path);

echo '<div class="blast-results-container p-3" style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: .25rem;">';

// Check for common BLAST errors in the output
if (empty($blast_output) || strpos($blast_output, 'Error:') !== false || strpos($blast_output, 'BLAST Database error:') !== false) {
    echo '<h4>An Error Occurred During BLAST Execution</h4>';
    echo '<div class="alert alert-danger">Please check your input sequence format and parameters.</div>';
    echo '<strong>Error Details:</strong>';
    echo '<pre style="white-space: pre-wrap; word-wrap: break-word;">' . htmlspecialchars($blast_output) . '</pre>';
} else {
    // If successful, the output is already formatted HTML, so we can echo it directly.
    echo $blast_output;
}

echo '</div>'; // end .blast-results-container

?>
        <div class="mt-4">
            <a href="blast.php" class="btn btn-secondary">Start New BLAST Search</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>