<?php
// =================================================================
// FILE: fetch_details.php (Create this new file)
// =================================================================

header('Content-Type: application/json');

// --- Your Database Connection Details ---
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";
$detailsTable = "ppi_2"; // <-- IMPORTANT: Change if your table name is different

// Check if the protein ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'No protein ID provided.']);
    exit();
}

$proteinId = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Prepare a query to get details for the specific protein
    $query = "SELECT * FROM `$detailsTable` WHERE `protein_id` = :proteinId LIMIT 1";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([':proteinId' => $proteinId]);
    $result = $stmt->fetch(); // Use fetch() since we expect only one row

    if ($result) {
        echo json_encode($result);
    } else {
        // Return an empty object if no details are found
        echo json_encode(new stdClass()); 
    }

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>