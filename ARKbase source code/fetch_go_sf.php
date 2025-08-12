<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "arkbase"; // Replace with your actual username
$password = "data@2025"; // Replace with your actual password
$dbname = "arkbase";
$port = "3307"; // Your MySQL port
$table = "dti_ab";
$pathogenName = "Shigella_flexneri"; // Corrected name with underscore

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    error_log("Database Connection failed for GO data: " . $conn->connect_error);
    echo json_encode(['error' => 'Database connection failed for GO data.']);
    exit();
}

// SQL statement to fetch all relevant GO columns for the pathogen
// We fetch all rows here as we need counts across the entire dataset for GO charts
$sql = "SELECT
            GO_Biological_Process,
            GO_molecular_function,
            GO_cellular_component
        FROM `" . $table . "`
        WHERE Pathogen_name = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
     error_log("GO Data Prepare failed: (" . $conn->errno . ") " . $conn->error);
     $conn->close();
     echo json_encode(['error' => 'Failed to prepare GO data query.']);
     exit();
}

// Bind the pathogen name parameter
$stmt->bind_param("s", $pathogenName);

// Execute the statement
if (!$stmt->execute()) {
    error_log("GO Data Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    $stmt->close();
    $conn->close();
    echo json_encode(['error' => 'Failed to execute GO data query.']);
    exit();
}

$result = $stmt->get_result();

$go_terms_all = [
    'biological' => [],
    'molecular' => [],
    'cellular' => []
];

if ($result->num_rows > 0) {
    // Fetch all rows to aggregate GO terms
    while ($row = $result->fetch_assoc()) {
        if (!empty($row['GO_Biological_Process'])) {
             $go_terms_all['biological'][] = $row['GO_Biological_Process'];
        }
        if (!empty($row['GO_molecular_function'])) {
            $go_terms_all['molecular'][] = $row['GO_molecular_function'];
        }
         if (!empty($row['GO_cellular_component'])) {
            $go_terms_all['cellular'][] = $row['GO_cellular_component'];
        }
    }
}

$stmt->close();
$conn->close();

// Process the fetched GO terms to get counts
function countGoTerms($termList) {
    $counts = [];
    foreach ($termList as $termString) {
        $terms = explode(';', $termString);
        foreach ($terms as $term) {
            $cleanTerm = trim($term);
             // Extract term name before [GO:...] if present
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


// Return the aggregated counts as JSON
echo json_encode($go_counts_aggregated);

?>