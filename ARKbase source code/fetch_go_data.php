<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";
$table = "dti_new";

// STEP 1: Get pathogen name dynamically from the GET request
$pathogenName = isset($_GET['pathogen']) ? $_GET['pathogen'] : 'Escherichia_coli'; // Default if not provided

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    error_log("Database Connection failed for GO data: " . $conn->connect_error);
    echo json_encode(['error' => 'Database connection failed for GO data.']);
    exit();
}

$sql = "SELECT
            GO_Biological_Process,
            GO_molecular_function,
            GO_cellular_component
        FROM `" . $table . "`
        WHERE Pathogen_name = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
     error_log("GO Data Prepare failed: " . $conn->error);
     $conn->close();
     echo json_encode(['error' => 'Failed to prepare GO data query.']);
     exit();
}
// Bind the dynamic pathogen name
$stmt->bind_param("s", $pathogenName);
$stmt->execute();
$result = $stmt->get_result();

$go_terms_all = [
    'biological' => [],
    'molecular' => [],
    'cellular' => []
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['GO_Biological_Process'])) $go_terms_all['biological'][] = $row['GO_Biological_Process'];
        if (!empty($row['GO_molecular_function'])) $go_terms_all['molecular'][] = $row['GO_molecular_function'];
        if (!empty($row['GO_cellular_component'])) $go_terms_all['cellular'][] = $row['GO_cellular_component'];
    }
}

$stmt->close();
$conn->close();

function countGoTerms($termList) {
    $counts = [];
    foreach ($termList as $termString) {
        $terms = explode(';', $termString);
        foreach ($terms as $term) {
            $cleanTerm = trim($term);
            if (strpos($cleanTerm, ' [GO:') !== false) {
                $cleanTerm = trim(substr($cleanTerm, 0, strpos($cleanTerm, ' [GO:')));
            }
            if (!empty($cleanTerm)) {
                 $counts[$cleanTerm] = ($counts[$cleanTerm] ?? 0) + 1;
            }
        }
    }
    return $counts;
}

$go_counts_aggregated = [
    'biological' => countGoTerms($go_terms_all['biological']),
    'molecular' => countGoTerms($go_terms_all['molecular']),
    'cellular' => countGoTerms($go_terms_all['cellular'])
];

echo json_encode($go_counts_aggregated);
?>