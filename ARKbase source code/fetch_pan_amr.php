<?php
// Set headers for JSON response and CORS
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Restrict in production
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle browser preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// --- DATABASE CONNECTION DETAILS ---
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";

// Get the pathogen name from the URL, e.g., ?pathogen=p_aeruginosa
$pathogenName = $_GET['pathogen'] ?? '';

// Validate input
if (empty($pathogenName)) {
     http_response_code(400); // Bad Request
     echo json_encode(["error" => "Pathogen name parameter is missing."]);
     exit();
}

// Enable better error reporting for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Establish database connection
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    if ($conn->connect_error) {
        throw new mysqli_sql_exception("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // --- SQL QUERY FOR THE pan_amr TABLE ---
    $sql = "SELECT
                prot_id,
                description,
                amr_gene_family,
                operon,
                virulence,
                essential,
                betweenness,
                core,
                structure,
                drug_target,
                resistance_mechanism,
                antibiotic
            FROM pan_amr
            WHERE pathogen_name = ?"; // Filter by the 'pathogen_name' column

    // Prepare and execute the statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $pathogenName);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    if ($result) {
        // Fetch all matching rows
        while($row = $result->fetch_assoc()) {
            // Trim whitespace from all string values for cleaner data
            foreach($row as $key => $value) {
                if (is_string($value)) {
                     $row[$key] = trim($value);
                }
            }
            $data[] = $row;
        }
    }

    // Close connections
    $stmt->close();
    $conn->close();

    // Output data as JSON
    echo json_encode($data);

} catch (mysqli_sql_exception $e) {
     error_log("Database Error: " . $e->getMessage());
     http_response_code(500);
     echo json_encode(["error" => "Database operation failed. " . $e->getMessage()]);
} catch (Exception $e) {
    error_log("Server Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "A server error occurred. " . $e->getMessage()]);
}
?>