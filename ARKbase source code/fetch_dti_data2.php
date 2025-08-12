<?php
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";
$table = "dti_new";

// --- Database Connection ---
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    // If connection fails, log error and exit for both download and API requests
    header('Content-Type: application/json');
    error_log("Database Connection failed: " . $conn->connect_error);
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

// --- Parameter Handling ---
$pathogenName = isset($_GET['pathogen']) ? $_GET['pathogen'] : 'Escherichia_coli';

// --- MODIFICATION START: Handle Download Request ---
// Check if the 'download' parameter is set to 'true'
if (isset($_GET['download']) && $_GET['download'] === 'true') {

    // Set HTTP headers for a CSV file download
    $pathogenDisplayName = str_replace('_', '-', $pathogenName);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="DTI-data-' . $pathogenDisplayName . '.csv"');

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Define and write the CSV header row
    $header = [
        'Target ID', 'DrugBank ID', 'Drug Name', 'Score', 'Protein Names',
        'Drug Degree', 'Target Degree', 'Reference Protein ID', 'Pathway', 'Toxicity Safe'
    ];
    fputcsv($output, $header);

    // SQL query to fetch ALL data with the new sorting logic
    $download_sql = "SELECT
                        Target,
                        Drugbank_ID,
                        Drug_Name,
                        Score,
                        Protein_names,
                        Drug_Degree,
                        Target_Degree,
                        Ref_locus_tag,
                        Pathway,
                        DeepPK_toxicity_Safe
                    FROM `" . $table . "`
                    WHERE Pathogen_name = ?
                    ORDER BY
                        `Score` DESC,
                        CASE WHEN `DeepPK_toxicity_Safe` = 'Yes' THEN 0 ELSE 1 END ASC,
                        `Target` ASC";

    $stmt = $conn->prepare($download_sql);
    if ($stmt === false) {
        error_log("Download Prepare failed: " . $conn->error);
        fclose($output);
        $conn->close();
        exit(); // Exit silently on failure
    }
    
    $stmt->bind_param("s", $pathogenName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Loop through the results and write each row to the CSV
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    // Clean up and exit the script
    $stmt->close();
    fclose($output);
    $conn->close();
    exit;
}
// --- MODIFICATION END: Handle Download Request ---


// --- JSON API LOGIC (for the table) ---
// This part only runs if it's not a download request

header('Content-Type: application/json');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;

if ($limit <= 0) $limit = 20;
$offset = ($page - 1) * $limit;
if ($offset < 0) $offset = 0;

// Get Total Number of Records for the selected pathogen
$total_records = 0;
$count_sql = "SELECT COUNT(*) AS total FROM `" . $table . "` WHERE Pathogen_name = ?";
$count_stmt = $conn->prepare($count_sql);
if ($count_stmt === false) {
    error_log("Count Prepare failed: " . $conn->error);
    $conn->close();
    echo json_encode(['error' => 'Failed to prepare total count query.']);
    exit();
}
$count_stmt->bind_param("s", $pathogenName);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
if ($count_row = $count_result->fetch_assoc()) {
    $total_records = $count_row['total'];
}
$count_stmt->close();

// Get Paginated Data
$dti_data = [];

// MODIFICATION: Changed the ORDER BY clause for the paginated data
$data_sql = "SELECT
                interaction_id,
                Drugbank_ID,
                Target,
                Score,
                Drug_Name,
                Drug_Type,
                Protein_names,
                Drug_Degree,
                Target_Degree,
                Ref_locus_tag,
                Pathway,
                GO_Biological_Process,
                GO_molecular_function,
                GO_cellular_component,
                DeepPK_toxicity_Safe
            FROM `" . $table . "`
            WHERE Pathogen_name = ?
            ORDER BY
                `Score` DESC,
                CASE WHEN `DeepPK_toxicity_Safe` = 'Yes' THEN 0 ELSE 1 END ASC,
                `Target` ASC
            LIMIT ? OFFSET ?";

$data_stmt = $conn->prepare($data_sql);
if ($data_stmt === false) {
     error_log("Data Prepare failed: " . $conn->error);
     $conn->close();
     echo json_encode(['error' => 'Failed to prepare data query.']);
     exit();
}
$data_stmt->bind_param("sii", $pathogenName, $limit, $offset);
$data_stmt->execute();
$data_result = $data_stmt->get_result();

if ($data_result) {
    while ($row = $data_result->fetch_assoc()) {
        $processed_row = [
            "interaction_id" => $row['interaction_id'],
            "Drugbank.ID" => $row['Drugbank_ID'],
            "Target" => $row['Target'],
            "Score" => (float)$row['Score'],
            "Drug_Name" => $row['Drug_Name'],
            "Drug_Type" => $row['Drug_Type'],
            "Protein.names" => $row['Protein_names'],
            "Drug_Degree" => $row['Drug_Degree'],
            "Target_Degree" => $row['Target_Degree'],
            "Ref_locus_tag" => $row['Ref_locus_tag'],
            "Pathway" => $row['Pathway'],
            "Gene.Ontology.(biological.process)" => $row['GO_Biological_Process'],
            "Gene.Ontology.(molecular.function)" => $row['GO_molecular_function'],
            "Gene.Ontology.(cellular.component)" => $row['GO_cellular_component'],
            "DeepPK_toxicity_Safe" => $row['DeepPK_toxicity_Safe'],
            "drug_image_url" => $row['Drug_Name'] ? "https://placehold.co/150x150/FFF/000?text=" . urlencode($row['Drug_Name']) : null
        ];
        $dti_data[] = $processed_row;
    }
    $data_stmt->close();
}

$conn->close();

echo json_encode([
    'total_records' => $total_records,
    'data' => $dti_data
]);
?>