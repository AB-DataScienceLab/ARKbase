<?php
// Set the content type to JSON
header('Content-Type: application/json');
// Allow cross-origin requests
header('Access-Control-Allow-Origin: *');

// --- DATABASE CONNECTION DETAILS ---
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";

// Use mysqli_report for better error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Establish a new MySQLi connection
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        throw new mysqli_sql_exception("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // --- SQL QUERY FOR THE bgc TABLE ---
    // Select all columns needed for display, filtering, and linking.
    // We are fetching the entire table, filtering will be done on the client-side.
    $sql = "SELECT
                pathogen_id,
                bpp_name,
                refseq_ac,
                bgc_region,
                bgc_genes_locus_tag,
                genes_count,
                bioactive_secondary_metabolite,
                metabolite_SMILES,
                MIBiG_accession,
                bgc_category,
                bigscape_gcf
            FROM bgc";

    $result = $conn->query($sql);

    $data = [];
    if ($result) {
        // Fetch all rows into an associative array
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Close the connection
    $conn->close();

    // Output the data as a JSON array
    echo json_encode($data);

} catch (mysqli_sql_exception $e) {
     // Handle and log database-specific errors
     http_response_code(500);
     echo json_encode(["error" => "Database operation failed: " . $e->getMessage()]);
} catch (Exception $e) {
    // Handle and log other general errors
    http_response_code(500);
    echo json_encode(["error" => "A server error occurred: " . $e->getMessage()]);
}
?>