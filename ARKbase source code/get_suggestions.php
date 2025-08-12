<?php
// Include connection file
include 'conn.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysql_error());
}

// Get the search term from the request
$term = isset($_GET['term']) ? $_GET['term'] : '';

// Escape the term to prevent SQL injection
$term = mysql_real_escape_string($term, $conn);

// Only process if the term is at least 2 characters
if (strlen($term) >= 1) {
    // Query to get suggestions from multiple columns
    $sql = "SELECT DISTINCT `Uniprot_ID` as value FROM ProteinTable5 WHERE `Uniprot_ID` LIKE '%$term%'
            UNION
            SELECT DISTINCT `Gene` as value FROM ProteinTable5 WHERE `Gene` LIKE '%$term%'
            UNION
            SELECT DISTINCT `NRNC_symbol` as value FROM ProteinTable5 WHERE `NRNC_symbol` LIKE '%$term%'
            UNION
            SELECT DISTINCT `Member` as value FROM ProteinTable5 WHERE `Member` LIKE '%$term%'
            UNION
            SELECT DISTINCT `PTM` as value FROM ProteinTable5 WHERE `PTM` LIKE '%$term%'
            UNION
            SELECT DISTINCT `Modifier_1` as value FROM ProteinTable5 WHERE `Modifier_1` LIKE '%$term%'
            LIMIT 15"; // Limit results to prevent overwhelming response

    $result = mysql_query($sql, $conn);

    if ($result) {
        $suggestions = array();
        
        while ($row = mysql_fetch_assoc($result)) {
            if (!empty($row['value'])) {
                $suggestions[] = $row['value'];
            }
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($suggestions);
    } else {
        // Return empty array if query fails
        header('Content-Type: application/json');
        echo json_encode(array());
    }
} else {
    // Return empty array if term is too short
    header('Content-Type: application/json');
    echo json_encode(array());
}
?>