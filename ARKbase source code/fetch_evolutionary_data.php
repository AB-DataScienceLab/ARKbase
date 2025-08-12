<?php
// Set the content type to JSON for the response
header('Content-Type: application/json');

// 1. Include the database connection file.
require_once 'conn.php';

// 2. Check if the connection object exists and was successful.
if (!$conn || $conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database connection failed. Please contact the administrator.']);
    exit();
}

// 3. Get the pathogen parameter from the URL
if (!isset($_GET['pathogen'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Pathogen parameter is missing.']);
    exit();
}
$pathogen_short_name = $_GET['pathogen'];

// 4. Map the short name from the URL to the full organism name used in the database
$pathogenNamesMap = [
    's_aureus' => 'Staphylococcus aureus subsp. aureus NCTC 8325'
    // You can add other pathogens here if the evolutionary table expands in the future
];

$organism_name = $pathogenNamesMap[$pathogen_short_name] ?? null;

if (!$organism_name) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid or unsupported pathogen name provided.']);
    exit();
}

// 5. Prepare and execute the SQL query using a prepared statement
$sql = "SELECT protein_id, uniprot_id, gene_name, organism, fel, fubar, slac, meme, busted_pvalue, domain, literature FROM evolutionary_tb WHERE organism = ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    error_log('SQL prepare failed: ' . $conn->error); // Log error for admin
    echo json_encode(['error' => 'An internal server error occurred.']);
    exit();
}

// Bind the organism name parameter and execute
$stmt->bind_param("s", $organism_name);
$stmt->execute();
$result = $stmt->get_result();

// 6. Fetch all results into an array
$data = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// 7. Close the statement and the connection
$stmt->close();
$conn->close();

// 8. Return the final data as a JSON string
echo json_encode($data);
?>