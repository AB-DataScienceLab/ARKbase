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
    <title>Keyword Search</title>
 	<link rel="stylesheet" href="css/style.css" type="text/css">
    
    <script>
        function selectOrganism(organism) {
            document.getElementById('organism').value = organism;
            document.getElementById('searchForm').submit();
        }


    </script>
        <style>
/* General body styling */
/*body {*/
/*    font-family: Arial, sans-serif;*/
/*    margin: 0;*/
/*    padding: 0;*/
/*    background-color: #f9f9f9;*/
/*    color: #333;*/
/*    text-align: center;*/
/*}*/

/* Center the form */
form {
    margin: 20px auto;
    display: flex;
    justify-content: center;
    gap: 10px;
    align-items: center;
}

/* Search input styling */
input[type="text"] {
    width: 300px;
    padding: 8px; /* Reduced padding */
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px; /* Slightly smaller font */
    transition: border-color 0.3s;
}

input[type="text"]:focus {
    border-color: #007bff;
    outline: none;
}

/* Search button styling */
input[type="submit"] {
    padding: 8px 15px; /* Reduced padding */
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 14px; /* Smaller font size */
    cursor: pointer;
    transition: background-color 0.3s;
}

input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Table styling */
.results {
    margin: 10px auto; /* Reduced margin */
    width: 95%;
}

table {
    width: 100%;
    border-collapse: collapse; /* Remove spacing between cells */
    background-color: #fff;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    table-layout: auto; /* Allow columns to adjust based on content */
}

th, td {
    border: 1px solid #ddd;
    padding: 6px 8px; /* Reduced padding for compactness */
    text-align: left;
    word-wrap: break-word; /* Enable word wrapping for long text */
    white-space: normal; /* Allow wrapping of content */
    font-size: 14px; /* Smaller font size for a compact look */
    max-width: 300px;
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

/* Responsive styling for smaller devices */
@media (max-width: 768px) {
    input[type="text"] {
        width: 100%;
    }

    table {
        font-size: 12px; /* Further reduce font size for small devices */
    }
}

 /* Container styling */
.container {
    text-align: center; /* Center the content inside */
    background-color: #fff;
    padding: 20px; /* Add some padding */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

/* Title styling */
.title {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
}

/* Search section styling */
.search-section {
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Organism container */
.organism-container {
    display: flex;
    gap: 20px; /* Space between the items */
    justify-content: center;
    flex-wrap: wrap; /* Allow wrapping for smaller screens */
}

/* Individual organism card */
.organism {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #333;
    transition: transform 0.3s, box-shadow 0.3s;
}

.organism img {
    width: 100px;
    height: 100px;
    object-fit: contain; /* Ensure images maintain aspect ratio */
    margin-bottom: 10px;
    border-radius: 50%;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

.organism label {
    font-size: 14px;
    margin-top: 5px;
}

/* Hover effect for organism links */
.organism:hover {
    transform: scale(1.05); /* Slight zoom */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

/* Responsive styling */
@media (max-width: 768px) {
    .organism-container {
        gap: 10px;
    }

    .organism img {
        width: 80px;
        height: 80px;
    }

    .title {
        font-size: 20px;
    }
}
</style>
    
</head>
<body>

<div class="container">
    <div class="title">Organism Based Search</div>
     <div class="organism-container">
            <a href="javascript:void(0);" onclick="selectOrganism('human')" class="organism">
                <img src="images/human.png" alt="Human">
                <label>Human</label>
            </a>
            <a href="javascript:void(0);" onclick="selectOrganism('mouse')" class="organism">
                <img src="images/mouse.png" alt="Mouse">
                <label>Mouse</label>
            </a>
            <a href="javascript:void(0);" onclick="selectOrganism('rat')" class="organism">
                <img src="images/rat.png" alt="Rat">
                <label>Rat</label>
            </a>
        </div>
    </div>
    <div class="search-section">
        <form id="searchForm" method="POST" action="">
            <input type="hidden" id="organism" name="organism" value="">
            <input type="hidden" name="keyword" placeholder="Enter keyword like Group_name, Organism" required>
<!--            <input type="submit" value="Search">-->
        </form>
       

    <?php 
    // Function to escape special characters for LIKE queries
    function escapeLike($keyword) {
        return str_replace(array('%', '_'), array('\\%', '\\_'), $keyword);
    }

    // Function to search the database
    function searchDatabase($conn, $keyword, $organism) {
        $sql = "SELECT * FROM ProteinTable2 WHERE 
                    (`GROUP` LIKE '%$keyword%' OR
                    `NRNC_symbol` LIKE '%$keyword%' OR
                    `Gene` LIKE '%$keyword%' OR
                    `Member` LIKE '%$keyword%' OR
                    `Uniprot_ID` LIKE '%$keyword%' OR
                    `TrEMBL_ID` LIKE '%$keyword%' OR
                    `Isoforms` LIKE '%$keyword%' OR
                    `Uniprot_ID_Isoforms` LIKE '%$keyword%' OR
                    `PTM` LIKE '%$keyword%' OR
                    `SITE_OF_PTM` LIKE '%$keyword%' OR
                    `SITE_OF_PTM_Uniprot` LIKE '%$keyword%' OR
                    `Domain` LIKE '%$keyword%' OR
                    `Cell_line` LIKE '%$keyword%' OR
                    `Sequence` LIKE '%$keyword%' OR
                    `Modifier_1` LIKE '%$keyword%' OR
                    `Modifier_1_Uniprot` LIKE '%$keyword%' OR
                    `Modifier_2` LIKE '%$keyword%' OR
                    `Modifier_2_Uniprot` LIKE '%$keyword%' OR
                    `Effect` LIKE '%$keyword%' OR
                    `Reference` LIKE '%$keyword%' OR
                    `Source` LIKE '%$keyword%') AND
                    `Organism` = '$organism'
                     ORDER BY `PTM` ASC";

        $result = mysql_query($sql, $conn);

        // Check if the query was successful
        if (!$result) {
            die("Query failed: " . mysql_error());
        }

        return $result;
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $keyword = escapeLike(mysql_real_escape_string($_POST['keyword'], $conn));
        $organism = mysql_real_escape_string($_POST['organism'], $conn);
      //  echo "<p>Searching for: " . htmlspecialchars($keyword) . " in " . htmlspecialchars($organism) . "</p>"; // Debugging line
        $result = searchDatabase($conn, $keyword, $organism);

        if (mysql_num_rows($result) > 0) {
            echo "<h3>Search Results:</h3>";
            echo "<div class='results' style='max-width: 90%; overflow-x: auto;'>";
            echo "<table border='1' cellpadding='10'>";
            echo "<tr>
                   <th>Sub-family</th>
                    <th>Group</th>
                    <th>NRNC Symbol</th>
                    <th>Gene</th>
                    <th>Member</th>
                    <th>Uniprot ID</th>
                    <th>TrEMBL ID</th>
                    <th>Organism</th>
                    <th>Isoforms</th>
                    <th>Uniprot ID Isoforms</th>
                    <th>PTM</th>
                    <th>Site of PTM</th>
                    <th>Site of PTM Uniprot</th>
                    <th>Domain</th>
                    <th>Cell Line</th>
                    <th>Sequence</th>
                    <th>Modifier 1</th>
                    <th>Modifier 1 Uniprot</th>
                    <th>Modifier 2</th>
                    <th>Modifier 2 Uniprot</th>
                    <th>Effect</th>
                    <th>Reference</th>
                    <th>Source</th>
                  </tr>";

            while ($row = mysql_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . (!empty($row['Sub_family']) ? htmlspecialchars($row['Sub_family']) : "NA") . "</td>
                        <td>" . (!empty($row['GROUP']) ? htmlspecialchars($row['GROUP']) : "NA") . "</td>
                        <td>" . (!empty($row['NRNC_symbol']) ? htmlspecialchars($row['NRNC_symbol']) : "NA") . "</td>
                        <td>" . (!empty($row['Gene']) ? htmlspecialchars($row['Gene']) : "NA") . "</td>
                        <td>" . (!empty($row['Member']) ? htmlspecialchars($row['Member']) : "NA") . "</td>
                        <td>" . (!empty($row['Uniprot_ID']) ? htmlspecialchars($row['Uniprot_ID']) : "NA") . "</td>
                        <td>" . (!empty($row['TrEMBL_ID']) ? htmlspecialchars($row['TrEMBL_ID']) : "NA") . "</td>
                        <td>" . (!empty($row['Organism']) ? htmlspecialchars($row['Organism']) : "NA") . "</td>
                        <td>" . (!empty($row['Isoforms']) ? htmlspecialchars($row['Isoforms']) : "NA") . "</td>
                        <td>" . (!empty($row['Uniprot_ID_Isoforms']) ? htmlspecialchars($row['Uniprot_ID_Isoforms']) : "NA") . "</td>
                        <td>" . (!empty($row['PTM']) ? htmlspecialchars($row['PTM']) : "NA") . "</td>
                        <td>" . (!empty($row['SITE_OF_PTM']) ? htmlspecialchars($row['SITE_OF_PTM']) : "NA") . "</td>
                        <td>" . (!empty($row['SITE_OF_PTM_Uniprot']) ? htmlspecialchars($row['SITE_OF_PTM_Uniprot']) : "NA") . "</td>
                        <td>" . (!empty($row['Domain']) ? htmlspecialchars($row['Domain']) : "NA") . "</td>
                        <td>" . (!empty($row['Cell_line']) ? htmlspecialchars($row['Cell_line']) : "NA") . "</td>
                        <td>" . (!empty($row['Sequence']) ? htmlspecialchars($row['Sequence']) : "NA") . "</td>
                        <td>" . (!empty($row['Modifier_1']) ? htmlspecialchars($row['Modifier_1']) : "NA") . "</td>
                        <td>" . (!empty($row['Modifier_1_Uniprot']) ? htmlspecialchars($row['Modifier_1_Uniprot']) : "NA") . "</td>
                        <td>" . (!empty($row['Modifier_2']) ? htmlspecialchars($row['Modifier_2']) : "NA") . "</td>
                        <td>" . (!empty($row['Modifier_2_Uniprot']) ? htmlspecialchars($row['Modifier_2_Uniprot']) : "NA") . "</td>
                        <td>" . (!empty($row['Effect']) ? htmlspecialchars($row['Effect']) : "NA") . "</td>
                        <td>" . (!empty($row['Reference']) ? htmlspecialchars($row['Reference']) : "NA") . "</td>
                        <td>" . (!empty($row['Source']) ? htmlspecialchars($row['Source']) : "NA") . "</td>
                      </tr>";
            }
            echo "</table>";
            echo "</div>";
        } else {
            echo "<p>No results found for: " . htmlspecialchars($keyword) . " in " . htmlspecialchars($organism) . "</p>";
        }
    }
    ?>

</body>
</html>