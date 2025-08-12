<?php
header('Content-Type: application/json');
// Allow cross-origin requests if your HTML is on a different origin than the PHP script
// Consider restricting this to specific origins in a production environment for security
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307"; // Explicitly specifying the port

// Get the pathogen name from the GET request
$pathogenName = $_GET['pathogen'] ?? ''; // Use null coalescing operator for safety

// Basic validation/sanitization
// You should implement more robust validation based on expected pathogen names
// For simplicity, we'll just check if it's empty for now
if (empty($pathogenName)) {
     http_response_code(400); // Bad Request
     echo json_encode(["error" => "Pathogen name parameter is missing."]);
     exit();
}

// Prevent SQL injection by using prepared statements
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Use mysqli_report for better error reporting during development
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    // Check connection
    if ($conn->connect_error) {
        throw new mysqli_sql_exception("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4"); // Set charset

    // SQL query with a placeholder for the pathogen name
    $sql = "SELECT
                pathogen_name,
                prot_id,
                prot_desc,
                non_paralog,
                virulence,
                essential,
                ttd_novel,
                drugbank_novel,
                human_NH,
                anti_target,
                betweenness
            FROM drug_target
            WHERE pathogen_name = ?"; // Use placeholder

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    // Bind the pathogen name parameter (s = string)
    $stmt->bind_param("s", $pathogenName);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    $data = [];

    if ($result) { // result will be false only if get_result fails somehow after successful execute
        if ($result->num_rows > 0) {
            // Fetch all rows as associative arrays
            while($row = $result->fetch_assoc()) {
                 // Trim values
                 foreach($row as $key => $value) {
                     if (is_string($value)) {
                          $row[$key] = trim($value);
                     }
                 }
                $data[] = $row;
            }
        }
         // No need to free result set explicitly with get_result and fetch_assoc loop in PHP 7.x+
    } else {
         // This case is less likely with prepared statements but kept for safety
         error_log("SQL Error after execute: " . $stmt->error);
         http_response_code(500);
         echo json_encode(["error" => "Error executing query."]);
         $stmt->close();
         $conn->close();
         exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Output JSON data
    echo json_encode($data);

} catch (mysqli_sql_exception $e) {
     // Log the database errors
     error_log("Database operation failed: " . $e->getMessage());
     http_response_code(500); // Internal Server Error
     echo json_encode(["error" => "Database operation failed: " . $e->getMessage()]); // Include error message for debugging
} catch (Exception $e) {
    // Log other general errors
    error_log("Server error: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Server error: " . $e->getMessage()]); // Include error message
}

?>