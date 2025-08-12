<!DOCTYPE html>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

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
    <title>Organism Search</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <style>
        .search-container {
            display: none; /* Initially hide the search container */
        }

        /* Styles for messages */
        .message {
            margin: 20px auto; /* Center the message container */
            padding: 5px;
            border: 1px solid #10428d; /* Border color */
            border-radius: 2px; /* Rounded corners */
            background-color: #e7f3ff; /* Light blue background */
            color: #10428d; /* Text color */
            font-size: 16px; /* Font size */
            width: 200px; /* Fixed width */
            text-align: center; /* Center the text inside */
        }

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
            background-color: #10428d;
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
            background-color: #10428d;
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
            padding: 50px; /* Add some padding */
            border-radius: 20px; /* Rounded corners */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            width: 95%; /* Increase width to 95% of the parent */
            max-width: 1800px; /* Set a maximum width */
            min-height: 470px; /* Set a minimum height to ensure enough space */
            margin: 20px auto; /* Center the container with margin */
        }


        /* Title styling */
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #10428d;
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
    </style>
   
    <script>
       function selectOrganism(organism) {
            // Set the organism input value
            document.getElementById('organism').value = organism;
            
            // Reset the keyword input
            document.getElementById('keyword').value = '';
            
            // Hide previous results
            const resultsDiv = document.querySelector('.results');
            if (resultsDiv) {
                resultsDiv.style.display = 'none'; // Hide previous results
            }
            
            // Hide previous messages
            const messageDivs = document.querySelectorAll('.message');
            messageDivs.forEach(function(div) {
                div.style.display = 'none'; // Hide previous messages
            });
            
            // Hide the "Search Results:" heading
            const searchResultsHeading = document.querySelector('h3'); // Assuming the heading is an <h3> element
            if (searchResultsHeading) {
                searchResultsHeading.style.display = 'none'; // Hide the heading
            }
            
            // Show the search container
            document.getElementById('searchContainer').style.display = 'block';
        }
    </script>
</head>
<body>

<div class="container">
    <br><br>
    <div class="title">Select Organism</div>

    <div class="organism-container">
        <a href="javascript:void(0);" onclick="selectOrganism('human')" class="organism">
            <img src="images/human.png" alt="Human">
            <label><b>Human</b></label>
        </a>
        <a href="javascript:void(0);" onclick="selectOrganism('mouse')" class="organism">
            <img src="images/mouse.png" alt="Mouse">
            <label><b>Mouse</b></label>
        </a>
        <a href="javascript:void(0);" onclick="selectOrganism('rat')" class="organism">
            <img src="images/rat.png" alt="Rat">
            <label><b>Rat</b></label>
        </a>
    </div>
<br>
    <div id="searchContainer" class="search-container">
        <div class="title">Keyword Search</div>
        <form id="searchForm" method="POST" action="">
            <input type="hidden" id="organism" name="organism" value="">
            <input type="text" id="keyword" name="keyword" placeholder="Enter keyword " required>
            <input type="submit" value="Search">
        </form>
    </div>

    <?php 
    // Function to escape special characters for LIKE queries
    function escapeLike($keyword) {
        return str_replace(array('%', '_'), array('\\%', '\\_'), $keyword);
    }

    // Function to search the database
   function searchDatabase($conn, $keyword, $organism) {
    // Prepare the SQL query for exact matches
    $sql = "SELECT * FROM ProteinTable5 WHERE 
                (`Group_name` = '$keyword' OR
                `NRNC_symbol` = '$keyword' OR
                `Gene` = '$keyword' OR
                `Member` = '$keyword' OR
                `Uniprot_ID` = '$keyword' OR
                `TrEMBL_ID` = '$keyword' OR
                `Isoforms` = '$keyword' OR
                `Uniprot_ID_Isoforms` = '$keyword' OR
                `PTM` = '$keyword' OR
                `SITE_OF_PTM` = '$keyword' OR
                `SITE_OF_PTM_Uniprot` = '$keyword' OR
                `Domain` = '$keyword' OR
                `Cell_line` = '$keyword' OR
                `Sequence` = '$keyword' OR
                `Modifier_1` = '$keyword' OR
                `Modifier_1_Uniprot` = '$keyword' OR
                `Modifier_2` = '$keyword' OR
                `Pathway_name` = '$keyword' OR
                `Molecular` = '$keyword' OR
                `Biological` = '$keyword' OR
                `Cellular` = '$keyword' OR
                 `LBD` = '$keyword' OR
                  `MOTIF` = '$keyword' OR
                  `ZINC` = '$keyword' OR
                `Modifier_2_Uniprot` = '$keyword') AND
                `Organism` = '$organism'
                ORDER BY `PTM` ASC";
    
    $result = mysql_query($sql, $conn);
    if (!$result) {
        die("Query failed: " . mysql_error());
    }

    // Display results
    if (mysql_num_rows($result) > 0) {
        echo "<h3>Search Results:</h3>";
        echo "<div class='results' style='max-width: 100%; overflow-x: auto;'>";
        echo "<table border='1' cellpadding='1'>";
        echo "<tr>
               <th>Serial No</th>
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

        $serialNo = 1; // Initialize serial number
        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . $serialNo++ . "</td> <!-- Display serial number -->
                    <td>" . (!empty($row['Sub_family']) ? htmlspecialchars($row['Sub_family']) : "NA") . "</td>
                    <td>" . (!empty($row['Group_name']) ? htmlspecialchars($row['Group_name']) : "NA") . "</td>
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
        echo "<div class='message error-message'>No results found for: " . htmlspecialchars($keyword) . " in " . htmlspecialchars($organism) . "</div>";
    }
}

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $keyword = escapeLike(mysql_real_escape_string(trim($_POST['keyword'])));
        $organism = mysql_real_escape_string(trim($_POST['organism']));
        
        // Debugging output with CSS styling
        echo "<div class='message'>Organism : " . htmlspecialchars($organism) . "</div>"; // Debugging line
        echo "<div class='message'>Keyword Search: " . htmlspecialchars($keyword) . "</div>"; // Debugging line
        
        searchDatabase($conn, $keyword, $organism);
    }
    ?>

</div>
<?php include 'footer.php'; ?>
</body>
</html>