<?php
// FILE: datatable_handler.php
include 'conn3.php';
header('Content-Type: application/json');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    echo json_encode(["draw" => 0, "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => "DB Error"]);
    exit;
}

$draw = $_POST['draw'] ?? 0;
$start = $_POST['start'] ?? 0;

// --- FIX: START ---
// Get the requested length from DataTables.
$requestedLength = $_POST['length'] ?? 10;

// If DataTables asks for -1 (meaning "all records" because paging is off),
// we will override it with a safe, hard limit (e.g., 1000).
// This prevents server timeouts and memory errors.
$length = ($requestedLength == -1) ? 1000 : $requestedLength;
// --- FIX: END ---


$searchValue = $_POST['search']['value'] ?? '';
$category = $_POST['category'] ?? 'Access';
$pathogen = 'Acinetobacter baumannii';
$phenotypeFilter = $_POST['phenotypeFilter'] ?? 'All';

$columns = ['Assembly_Accession', 'Isolate_acession', 'Antibiotic', 'Antibiotic_Class', 'Phenotype'];
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderColumn = $columns[$orderColumnIndex] ?? $columns[0];
$orderDir = strtolower($_POST['order'][0]['dir'] ?? 'asc') === 'asc' ? 'ASC' : 'DESC';

$baseQuery = "FROM Genome_section";
$whereClauses = [];
$params = [];
$paramTypes = '';

// Pathogen filter
$whereClauses[] = "Pathogen = ?";
$paramTypes .= 's';
$params[] = $pathogen;

// Category filter
$categoryMap = [
    'Access' => ['Access', 'Access/Watch'],
    'Watch' => ['Watch', 'Watch '],
    'Access/Watch' => ['Access/Watch'],
    'Watch/Reserve'=> [ 'Watch/Reserve'],
    'Reserve' => ['Reserve'],
    'Unclassified' => ['Unclassified', '', null]
];

if (isset($categoryMap[$category])) {
    $placeholders = implode(',', array_fill(0, count($categoryMap[$category]), '?'));
    $whereClauses[] = "Aware_Category IN ($placeholders)";
    $paramTypes .= str_repeat('s', count($categoryMap[$category]));
    $params = array_merge($params, $categoryMap[$category]);
}

// Phenotype filter
if ($phenotypeFilter !== 'All') {
    $whereClauses[] = "Phenotype = ?";
    $paramTypes .= 's';
    $params[] = $phenotypeFilter;
}

// This section is to get the total record counts
$countParams = $params;
$countParamTypes = $paramTypes;

// Total count (before search)
$whereSqlForTotal = "WHERE " . implode(" AND ", $whereClauses);
$totalQuery = "SELECT COUNT(*) as total $baseQuery $whereSqlForTotal";
$stmtTotal = $conn->prepare($totalQuery);
if (!empty($countParamTypes)) {
    $stmtTotal->bind_param($countParamTypes, ...$countParams);
}
$stmtTotal->execute();
$recordsTotal = $stmtTotal->get_result()->fetch_assoc()['total'];
$stmtTotal->close();

// Add search filter for data query and filtered count
if (!empty($searchValue)) {
    $whereClauses[] = "(Assembly_Accession LIKE ? OR Isolate_acession LIKE ? OR Antibiotic LIKE ? OR Antibiotic_Class LIKE ? OR Phenotype LIKE ?)";
    $searchTerm = "%" . $searchValue . "%";
    $paramTypes .= 'sssss';
    array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
}

// Filtered count (after search)
$whereSqlForFiltered = "WHERE " . implode(" AND ", $whereClauses);
$filteredQuery = "SELECT COUNT(*) as total $baseQuery $whereSqlForFiltered";
$stmtFiltered = $conn->prepare($filteredQuery);
if (!empty($paramTypes)) {
    $stmtFiltered->bind_param($paramTypes, ...$params);
}
$stmtFiltered->execute();
$recordsFiltered = $stmtFiltered->get_result()->fetch_assoc()['total'];
$stmtFiltered->close();

// Get actual data with a LIMIT
$orderBySql = " ORDER BY CASE WHEN Assembly_Accession IS NOT NULL AND Assembly_Accession != '' THEN 0 ELSE 1 END, $orderColumn $orderDir";
$limitSql = " LIMIT ? OFFSET ?";
$paramTypes .= 'ii';
array_push($params, $length, $start); // Use our safe $length variable here

$dataQuery = "SELECT Assembly_Accession, Isolate_acession, Antibiotic, Antibiotic_Class, Phenotype $baseQuery $whereSqlForFiltered $orderBySql $limitSql";
$stmtData = $conn->prepare($dataQuery);
$stmtData->bind_param($paramTypes, ...$params);
$stmtData->execute();
$data = $stmtData->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtData->close();
$conn->close();

echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => intval($recordsTotal),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $data
], JSON_INVALID_UTF8_SUBSTITUTE);