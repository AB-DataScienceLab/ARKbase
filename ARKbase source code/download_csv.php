<?php
include 'conn.php';

$pathogen = isset($_GET['pathogen']) ? $_GET['pathogen'] : '';
$table = isset($_GET['table']) ? $_GET['table'] : '';
$antibiotic = isset($_GET['antibiotic']) ? $_GET['antibiotic'] : '';
$phenotype = isset($_GET['phenotype']) ? $_GET['phenotype'] : '';

if (empty($pathogen) || empty($table)) {
    die("Error: Required parameters are missing.");
}

$sql_from_where = "FROM " . $conn->real_escape_string($table) . " WHERE Pathogen = ?";
$param_types = 's';
$param_values = [$pathogen];

if (!empty($antibiotic) && !empty($phenotype)) {
    $id_sql = "SELECT DISTINCT Assembly_Accession FROM Genome_section WHERE Pathogen = ? AND Antibiotic = ? AND Phenotype = ?";
    $stmt_ids = $conn->prepare($id_sql);
    $stmt_ids->bind_param("sss", $pathogen, $antibiotic, $phenotype);
    $stmt_ids->execute();
    $result_ids = $stmt_ids->get_result();
    $assembly_ids = [];
    while ($row = $result_ids->fetch_assoc()) {
        $assembly_ids[] = $row['Assembly_Accession'];
    }
    $stmt_ids->close();

    if (empty($assembly_ids)) {
        die("No data to download for the selected filters.");
    }
    
    $placeholders = implode(',', array_fill(0, count($assembly_ids), '?'));
    $sql_from_where .= " AND Assembly_Accession IN ($placeholders)";
    $param_types .= str_repeat('s', count($assembly_ids));
    $param_values = array_merge($param_values, $assembly_ids);
}

// Note: No LIMIT or OFFSET here, we want all the data for the CSV
$data_sql = "SELECT * " . $sql_from_where . " ORDER BY Assembly_Accession";
$data_stmt = $conn->prepare($data_sql);
$data_stmt->bind_param($param_types, ...$param_values);
$data_stmt->execute();
$result = $data_stmt->get_result();

$filename = "arkbase_data_" . str_replace(' ', '_', $pathogen) . "_" . date('Y-m-d') . ".csv";

// Set headers to trigger browser download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// Write the header row
$fields = $result->fetch_fields();
$header_row = [];
foreach($fields as $field) {
    $header_row[] = $field->name;
}
fputcsv($output, $header_row);

// Write the data rows
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
$data_stmt->close();
$conn->close();
exit();
?>