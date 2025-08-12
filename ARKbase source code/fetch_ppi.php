<?php
// =================================================================
// FILE: fetch_interactions.php (Create this new file)
// =================================================================

header('Content-Type: application/json');

$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";
$table2 = "ppi_central"; // <-- CONFIRMED: Using ppi_2

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([]);
    exit();
}

$proteinId = $_GET['id'];

try {
    $pdo = new PDO("mysql:host=$servername;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // This query finds all interactions where the clicked protein is either protein 1 or protein 2
    $query = "SELECT * FROM `$table2` 
              WHERE `protien1_id` = :proteinId OR `protien2_id` = :proteinId
              ORDER BY `confidence_score` DESC";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([':proteinId' => $proteinId]);
    $results = $stmt->fetchAll();

    echo json_encode($results);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>