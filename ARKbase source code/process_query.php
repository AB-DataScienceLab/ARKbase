<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 0);

include 'header.php'; // Include header
include 'conn.php'; // Ensure your connection file is properly included

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysql_error());
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Builder</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            text-align: center;
            color: #10428d;
        }

        .condition {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        select, input[type="text"] {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1;
        }

        input[type="submit"], button {
            background-color: #10428d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        @media (max-width: 768px) {
            .condition {
                flex-direction: column;
            }

            select, input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }
   
        
        }
        
    </style>

<?php
include 'conn.php'; // Ensure your connection file is properly included

// Function to escape special characters for LIKE queries
function escapeLike($keyword) {
    return str_replace(array('%', '_'), array('\\%', '\\_'), $keyword);
}

// Initialize an array to hold the query parts
$queryParts = [];

// Loop through the submitted conditions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fieldnames = $_POST['fieldname'];
    $operators = $_POST['operator'];
    $keywords = $_POST['keyword'];
    $logicalOperators = $_POST['logical_operator'];

    for ($i = 0; $i < count($fieldnames); $i++) {
        $field = $fieldnames[$i];
        $operator = $operators[$i];
        $keyword = escapeLike(mysql_real_escape_string($keywords[$i], $conn));

        // Construct the query part
        $queryPart = "$field $operator '$keyword'";

        // Append logical operator if it's not the last condition
        if ($i < count($fieldnames) - 1) {
            $queryPart .= " " . $logicalOperators[$i];
        }

        $queryParts[] = $queryPart;
    }

    // Combine all parts into a single query
    $finalQuery = implode(' ', $queryParts);

    // Execute the query
    $sql = "SELECT * FROM ProteinTable2 WHERE $finalQuery";
    $result = mysql_query($sql, $conn);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysql_error());
    }

    // Display results
    if (mysql_num_rows($result) > 0) {
        echo "<h3>Search Results: $finalQuery</h3>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr>
             
                
                
               
                
                <th>Uniprot ID</th>
                
                <th>Organism</th>
               
                <th>Uniprot ID Isoforms</th>
                <th>PTM</th>
                
                <th>Domain</th>
                
                <th>Source</th>
              </tr>";

        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr>
                   
                    
                  
                   
                   
                    <td>" . (!empty($row['Uniprot_ID']) ? htmlspecialchars($row['Uniprot_ID']) : "NA") . "</td>
                 
                    <td>" . (!empty($row['Organism']) ? htmlspecialchars($row['Organism']) : "NA") . "</td>
              
                    <td>" . (!empty($row['Uniprot_ID_Isoforms']) ? htmlspecialchars($row['Uniprot_ID_Isoforms']) : "NA") . "</td>
                    <td>" . (!empty($row['PTM']) ? htmlspecialchars($row['PTM']) : "NA") . "</td>
                   
                    <td>" . (!empty($row['Domain']) ? htmlspecialchars($row['Domain']) : "NA") . "</td>
                   
                    <td>" . (!empty($row['Source']) ? htmlspecialchars($row['Source']) : "NA") . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found for your query.</p>";
    }
}
?> 
