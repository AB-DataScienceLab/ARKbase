<!DOCTYPE html>
<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);

include 'header.php'; // Include header
include 'conn.php'; // Ensure your connection file is properly included

if (!$conn) {
    die("Connection failed: " . mysql_error());
}

function escapeLike($keyword) {
    return str_replace(array('%', '_'), array('\\%', '\\_'), $keyword);
}

function fetchData($conn, $keyword) {
    $sql = "SELECT * FROM ProteinTable2 WHERE 
            `GROUP` LIKE '%$keyword%' OR
            `NRNC_symbol` LIKE '%$keyword%' OR
            `Gene` LIKE '%$keyword%' OR
            `Uniprot_ID` LIKE '%$keyword%' OR
            `Organism` LIKE '%$keyword%'";

    $result = mysql_query($sql, $conn);
    if (!$result) {
        die("Query failed: " . mysql_error());
    }
    return $result;
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keyword Search</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }

        table {
            width: 48%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            font-size: 14px;
        }

        table th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .search-section {
            margin-bottom: 20px;
            text-align: center;
        }

        input[type="text"], input[type="submit"] {
            padding: 8px;
            margin: 5px;
        }
    </style>
</head>
<body>
<div class="search-section">
    <form method="POST" action="">
        <input type="text" name="keyword" placeholder="Enter keyword" required>
        <input type="submit" value="Search">
    </form>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $keyword = escapeLike(mysql_real_escape_string($_POST['keyword'], $conn));
    $result = fetchData($conn, $keyword);

    if (mysql_num_rows($result) > 0) {
        echo "<div class='container'>";
        // Left Table
        echo "<table>";
        echo "<tr>
                <th>Uniprot ID</th>
                <th>Organism</th>
                <th>NRNC Symbol</th>
                <th>Gene</th>
                <th>Sub-family</th>
                <th>Group</th>
              </tr>";
        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['Uniprot_ID']) . "</td>
                    <td>" . htmlspecialchars($row['Organism']) . "</td>
                    <td>" . htmlspecialchars($row['NRNC_symbol']) . "</td>
                    <td>" . htmlspecialchars($row['Gene']) . "</td>
                    <td>" . htmlspecialchars($row['Sub_family']) . "</td>
                    <td>" . htmlspecialchars($row['GROUP']) . "</td>
                  </tr>";
        }
        echo "</table>";

        // Reset pointer for second table data
        mysql_data_seek($result, 0);

        // Right Table
        echo "<table>";
        echo "<tr>
                <th>PTM Type</th>
                <th>Cell Line</th>
                <th>PTM Site</th>
                <th>Domain</th>
                <th>Source</th>
                <th>Reference</th>
              </tr>";
        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['PTM']) . "</td>
                    <td>" . htmlspecialchars($row['Cell_line']) . "</td>
                    <td>" . htmlspecialchars($row['SITE_OF_PTM']) . "</td>
                    <td>" . htmlspecialchars($row['Domain']) . "</td>
                    <td>" . htmlspecialchars($row['Source']) . "</td>
                    <td>" . htmlspecialchars($row['Reference']) . "</td>
                  </tr>";
        }
        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>No results found for: " . htmlspecialchars($keyword) . "</p>";
    }
}
?>
</body>
</html>
