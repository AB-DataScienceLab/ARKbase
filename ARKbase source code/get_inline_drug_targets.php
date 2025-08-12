<?php
header('Content-Type: application/json');
include 'conn.php'; // Make sure this path is correct

// Basic validation
if (!isset($_GET['pathogen']) || !isset($_GET['protein_ids'])) {
    echo json_encode(['error' => 'Missing required parameters.']);
    exit();
}

$pathogen_alias = $_GET['pathogen'];
$protein_ids_str = $_GET['protein_ids'];
$protein_ids = explode(',', $protein_ids_str);

if (empty($protein_ids) || empty($pathogen_alias)) {
    echo json_encode([]); // Return empty array if no IDs provided
    exit();
}

// Prepare the IN clause for the SQL query
$placeholders = implode(',', array_fill(0, count($protein_ids), '?'));
$sql = "SELECT prot_id, prot_desc, essential, human_NH, anti_target, non_paralog, virulence, betweenness, ttd_novel, drugbank_novel 
        FROM drug_targets 
        WHERE pathogen = ? AND prot_id IN ($placeholders)";

// Prepare the types string for bind_param (s for pathogen, then s for each protein ID)
$types = 's' . str_repeat('s', count($protein_ids));
$params = array_merge([$pathogen_alias], $protein_ids);

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

mysqli_close($conn);
echo json_encode($data);
?>