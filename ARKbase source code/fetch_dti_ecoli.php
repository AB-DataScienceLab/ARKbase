<?php
header('Content-Type: application/json');

// --- Configuration ---
$servername = "localhost";
$username = "arkbase"; // Replace with your actual username
$password = "data@2025"; // Replace with your actual password
$dbname = "arkbase";
$port = "3307"; // Your MySQL port
$table = "dti_ab";
$pathogenName = "Escherichia_coli";

// ... (pagination code is the same) ...
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
if ($limit <= 0) { $limit = 20; }
$offset = ($page - 1) * $limit;
if ($offset < 0) { $offset = 0; }

// --- Database Connection ---
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    error_log("Database Connection failed: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

// ... (total records code is the same) ...
$total_records = 0;
$count_sql = "SELECT COUNT(*) AS total FROM `" . $table . "` WHERE Pathogen_name = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("s", $pathogenName);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
if ($count_row = $count_result->fetch_assoc()) {
    $total_records = (int)$count_row['total'];
}
$count_stmt->close();


// --- 2. Get Paginated Data ---
$dti_data = [];

// Make sure the column name here EXACTLY matches what you found in Step 1
$data_sql = "SELECT
                interaction_id, Drugbank_ID, Target, Score, Drug_Name,
                Drug_Type, Protein_names, Pathway, GO_Biological_Process,
                GO_molecular_function, GO_cellular_component, DeepPK_toxicity_Safe
            FROM `" . $table . "`
            WHERE Pathogen_name = ?
            ORDER BY interaction_id
            LIMIT ? OFFSET ?";

$data_stmt = $conn->prepare($data_sql);
if ($data_stmt === false) {
     error_log("Data Prepare failed: (" . $conn->errno . ") " . $conn->error);
     $conn->close();
     http_response_code(500);
     echo json_encode(['error' => 'Failed to prepare data query.']);
     exit();
}

$data_stmt->bind_param("sii", $pathogenName, $limit, $offset);
$data_stmt->execute();
$data_result = $data_stmt->get_result();

if ($data_result->num_rows > 0) {
    while ($row = $data_result->fetch_assoc()) {
        
//        // =========================================================
//        // TEMPORARY DEBUGGING: This will show you the raw data
//        // for the first row and then stop the script.
//        header('Content-Type: text/plain'); // Set content type to plain text for readability
//        var_dump($row);
//        exit(); // Stop the script here
//        // =========================================================

        $processed_row = [
            "interaction_id" => $row['interaction_id'],
            "Drugbank.ID" => $row['Drugbank_ID'],
            "Target" => $row['Target'],
            "Score" => (float)$row['Score'],
            "Drug_Name" => $row['Drug_Name'],
            "Drug_Type" => $row['Drug_Type'],
            "Protein.names" => $row['Protein_names'],
            "Pathway" => $row['Pathway'],
            "Gene.Ontology.(biological.process)" => $row['GO_Biological_Process'],
            "Gene.Ontology.(molecular.function)" => $row['GO_molecular_function'],
            "Gene.Ontology.(cellular.component)" => $row['GO_cellular_component'],
            // Make sure the key here ('DeepPK_toxicity_Safe') matches the key from the var_dump
            "DeepPK_toxicity_Safe" => $row['DeepPK_toxicity_Safe'],
            "drug_image_url" => $row['Drug_Name'] ? "https://placehold.co/150x150/FFF/000?text=" . urlencode($row['Drug_Name']) : null
        ];
        $dti_data[] = $processed_row;
    }
}

$data_stmt->close();
$conn->close();

// --- 3. Return Final JSON Response ---
// This part will not be reached while debugging is active
echo json_encode([
    'total_records' => $total_records,
    'data' => $dti_data
]);
?>