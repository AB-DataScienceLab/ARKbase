<?php
// Set the content type to JSON
header('Content-Type: application/json');
// Allow cross-origin requests for development. Restrict this in production.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests from browsers
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// --- DATABASE CONNECTION DETAILS (should be the same) ---
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";

// Get the pathogen name from the GET request (e.g., ?pathogen=escherichia_coli)
$pathogenName = $_GET['pathogen'] ?? '';

// Basic validation: ensure pathogen name is not empty
if (empty($pathogenName)) {
     http_response_code(400); // Bad Request
     echo json_encode(["error" => "Pathogen name parameter is missing."]);
     exit();
}

// Use mysqli_report for better error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Establish a new MySQLi connection
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset("utf8mb4");

    // --- SQL QUERY FOR THE Pan_virulence TABLE ---
    // Select all required columns from the Pan_virulence table
    $sql = "SELECT
                prot_id,
                sseqid,
                Subject_Gene_Symbol,
                Subject_Protein_Name,
                VF_ID,
                VFC_ID,
                VF_Category,
                Organism_vfdbhit,
                Operon_ID,
                essential,
                betweenness,
                core,
                amr,
                card_description,
                amr_gene_family,
                resistance_mechanism,
                antibiotics,
                drug_target
            FROM Pan_virulence
            WHERE Pathogen = ?"; // Filter by the 'Pathogen' column

    // Prepare the statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind the pathogen name parameter
    $stmt->bind_param("s", $pathogenName);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

    $data = [];
    if ($result) {
        // Fetch all rows into an associative array
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();

    // Output the data as a JSON array
    echo json_encode($data);

} catch (mysqli_sql_exception $e) {
     // Handle and log database-specific errors
     error_log("Database operation failed: " . $e->getMessage());
     http_response_code(500); // Internal Server Error
     echo json_encode(["error" => "A database error occurred. " . $e->getMessage()]);
} catch (Exception $e) {
    // Handle and log other general errors
    error_log("Server error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "A server error occurred. " . $e->getMessage()]);
}
?>