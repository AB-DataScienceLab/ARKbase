<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "arkbase"; // Replace with your actual username
$password = "data@2025"; // Replace with your actual password
$dbname = "arkbase";
$port = "3307"; // Your MySQL port
$table = "dti_ab";
$pathogenName = "Salmonella"; // Corrected name with underscore

// Get pagination parameters from GET request
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Default to page 1
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20; // Default to 20 items per page

// Ensure limit is positive
if ($limit <= 0) {
    $limit = 20;
}

// Calculate offset
$offset = ($page - 1) * $limit;
if ($offset < 0) {
    $offset = 0;
}


// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    error_log("Database Connection failed: " . $conn->connect_error);
    // Return an error response to the client
    echo json_encode(['error' => 'Database connection failed. Please try again later.']);
    exit();
}

// --- 1. Get Total Number of Records ---
$total_records = 0;
$count_sql = "SELECT COUNT(*) AS total FROM `" . $table . "` WHERE Pathogen_name = ?";
$count_stmt = $conn->prepare($count_sql);

if ($count_stmt === false) {
    error_log("Count Prepare failed: (" . $conn->errno . ") " . $conn->error);
    $conn->close();
    echo json_encode(['error' => 'Failed to prepare total count query.']);
    exit();
}

$count_stmt->bind_param("s", $pathogenName);

if (!$count_stmt->execute()) {
    error_log("Count Execute failed: (" . $count_stmt->errno . ") " . $count_stmt->error);
    $count_stmt->close();
    $conn->close();
    echo json_encode(['error' => 'Failed to execute total count query.']);
    exit();
}

$count_result = $count_stmt->get_result();
if ($count_row = $count_result->fetch_assoc()) {
    $total_records = $count_row['total'];
}
$count_stmt->close();

// --- 2. Get Paginated Data ---
$dti_data = []; // Initialize as an empty array

// Prepare SQL statement for fetching paginated data
// ADDED DeepPK_toxicity_Safe to SELECT list
$data_sql = "SELECT
                interaction_id,
                Drugbank_ID,
                Target,
                Score,
                Drug_Name,
                Drug_Type,
                Protein_names,
                Pathway,
                GO_Biological_Process,
                GO_molecular_function,
                GO_cellular_component,
                DeepPK_toxicity_Safe -- ADDED NEW COLUMN
            FROM `" . $table . "`
            WHERE Pathogen_name = ?
            ORDER BY interaction_id -- Or another suitable column for consistent ordering
            LIMIT ? OFFSET ?";

$data_stmt = $conn->prepare($data_sql);

if ($data_stmt === false) {
     error_log("Data Prepare failed: (" . $conn->errno . ") " . $conn->error);
     $conn->close();
     echo json_encode(['error' => 'Failed to prepare data query.']);
     exit();
}

// Bind parameters: s for string ($pathogenName), i for integer ($limit), i for integer ($offset)
$data_stmt->bind_param("sii", $pathogenName, $limit, $offset);


if (!$data_stmt->execute()) {
    error_log("Data Execute failed: (" . $data_stmt->errno . ") " . $data_stmt->error);
    $data_stmt->close();
    $conn->close();
    echo json_encode(['error' => 'Failed to execute data query.']);
    exit();
}

$data_result = $data_stmt->get_result();


if ($data_result->num_rows > 0) {
    // Fetch data row by row and populate $dti_data array
    while ($row = $data_result->fetch_assoc()) {
        $processed_row = [
            "interaction_id" => $row['interaction_id'],
            "Drugbank.ID" => $row['Drugbank_ID'],
            "Target" => $row['Target'],
            "Score" => (float)$row['Score'], // Ensure score is a number
            "Drug_Name" => $row['Drug_Name'],
            "Drug_Type" => $row['Drug_Type'],
            "Protein.names" => $row['Protein_names'],
            "Pathway" => $row['Pathway'],
            "Gene.Ontology.(biological.process)" => $row['GO_Biological_Process'],
            "Gene.Ontology.(molecular.function)" => $row['GO_molecular_function'],
            "Gene.Ontology.(cellular.component)" => $row['GO_cellular_component'],
             "DeepPK_toxicity_Safe" => $row['DeepPK_toxicity_Safe'], // ADDED NEW COLUMN DATA
            // Generate a placeholder image URL based on the drug name
            "drug_image_url" => $row['Drug_Name'] ? "https://placehold.co/150x150/FFF/000?text=" . urlencode($row['Drug_Name']) : null
        ];
        $dti_data[] = $processed_row;
    }
}

$data_stmt->close();
$conn->close();

// --- 3. Return JSON Response ---
// Always return an object containing total_records and the data array (which might be empty)
echo json_encode([
    'total_records' => $total_records,
    'data' => $dti_data
]);

?>