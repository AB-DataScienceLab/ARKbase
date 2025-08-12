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

// Initialize an empty variable for results
$results = null;

// Function to escape special characters for LIKE queries
function escapeLike($keyword) {
    return str_replace(array('%', '_'), array('\\%', '\\_'), $keyword);
}

// Get unique values for dropdown fields
function getUniqueValues($conn, $column) {
    $query = "SELECT DISTINCT $column FROM ProteinTable5 WHERE $column IS NOT NULL AND $column != '' ORDER BY $column ASC";
    $result = mysql_query($query, $conn);
    $values = array();
    
    if ($result) {
        while ($row = mysql_fetch_assoc($result)) {
            $values[] = $row[$column];
        }
    }
    
    return $values;
}

// Fetch unique values for dropdown fields
$organismValues = getUniqueValues($conn, 'Organism');
$ptmValues = getUniqueValues($conn, 'PTM');
$domainValues = getUniqueValues($conn, 'Domain');
$sourceValues = getUniqueValues($conn, 'Source');
$modifier1Values = getUniqueValues($conn, 'Modifier_1');
$memberValues = getUniqueValues($conn, 'Member');

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $queryParts = [];
    $fieldnames = $_POST['fieldname'];
    $operators = $_POST['operator'];
    $keywords = $_POST['keyword-dropdown']; // Change to handle dropdown values
    $logicalOperators = isset($_POST['logical_operator']) ? $_POST['logical_operator'] : [];

    for ($i = 0; $i < count($fieldnames); $i++) {
        $field = $fieldnames[$i];
        $operator = $operators[$i];
        $keyword = escapeLike(mysql_real_escape_string($keywords[$i], $conn));

        // Construct the query part
        $queryPart = "$field $operator '%$keyword%'";

        // Append logical operator if it's not the last condition
        if ($i < count($fieldnames) - 1 && isset($logicalOperators[$i])) {
            $queryPart .= " " . $logicalOperators[$i];
        }

        $queryParts[] = $queryPart;
    }

    // Combine all parts into a single query
    $finalQuery = implode(' ', $queryParts);

    // Execute the query
    $sql = "SELECT * FROM ProteinTable5 WHERE $finalQuery";
    $results = mysql_query($sql, $conn);

    // Check if the query was successful
    if (!$results) {
        echo "<div style='color:red;'>Query failed: " . mysql_error() . "</div>";
        echo "<div>SQL: $sql</div>";
    }
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Query Builder</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <style>
        /* Your existing CSS styles */
        .container {
            text-align: center; /* Center the content inside */
            background-color: #fff;
            padding: 50px; /* Add some padding */
            border-radius: 20px; /* Rounded corners */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            width: 95%; /* Increase width to 95% of the parent */
            max-width: 1400px; /* Set a maximum width */
            min-height: 450px; /* Set a minimum height to ensure enough space */
            margin: 20px auto; /* Center the container with margin */
        }

        h2 {
            text-align: center;
            color: #10428d;
        }

        h3 {
            text-align: center;
            color: #10428d;
        }

        .condition {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #10428d;
        }

        select {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1;
            color: #10428d;
        }

        input[type="submit"], button {
            background-color: #10428d; /* Default button color */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #0d3a7a; /* Darker shade for hover effect */
        }

        /* New button style for "Add More Fields" */
        .add-more-button {
            background-color: #2cab6c; /* Button color */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top:  10px; /* Add some space above the button */
        }

        .add-more-button:hover {
            background-color: #0d3a7a; /* Darker shade for hover effect */
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px; /* Add some space above the table */
            background-color: #fff;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            table-layout: auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            font-size: 14px;
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

        /* Style for the dropdown */
        .keyword-dropdown {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1;
            color: #10428d;
        }

        .hidden {
            display: none;
        }

        @media (max-width: 768px) {
            .condition {
                flex-direction: column;
            }

            select {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
<br>
    <div class="container">
        <h2>Hypothesis Generator (Query Builder)</h2>
        <h3>Users may build complex queries using the logical operators 'AND' and 'OR'. Each sub-query can be built using other operators such as LIKE, NOT LIKE, while dealing with strings like words or letters. The Query builder aids to the flexibility of performing search on a number of fields simultaneously.</h3>
        <form method="POST" action="" id="searchForm">
            <div id="conditions">
                <div class="condition">
                    <select name="fieldname[]" onchange="toggleInputType(this)">
                        <option value="Organism">Organism</option>
                        <option value="PTM">PTM</option>
                        <option value="Domain">Domain</option>
                        <option value="Source">Source</option>
                        <option value="Modifier_1">Modifier</option>
                        <option value="Member">Nuclear Receptor</option>
                    </select>
                    <select name="operator[]">
                        <option value="LIKE">LIKE</option>
                        <option value="NOT LIKE">NOT LIKE</option>
                    </select>
                    
                    <!-- Organism dropdown (default) -->
                    <select name="keyword-dropdown[]" class="keyword-dropdown">
                        <option value="">Select Organism</option>
                        <?php foreach ($organismValues as $value): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- PTM dropdown (default) -->
                    <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                        <option value="">Select PTM</option>
                        <?php foreach ($ptmValues as $value): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- Domain dropdown (default) -->
                    <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                        <option value="">Select Domain</option>
                        <?php foreach ($domainValues as $value): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- Source dropdown (default) -->
                    <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                        <option value="">Select Source</option>
                        <?php foreach ($sourceValues as $value): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- Modifier_1 dropdown (default) -->
                    <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                        <option value="">Select Modifier 1</option>
                        <?php foreach ($modifier1Values as $value): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <!-- Member dropdown (default) -->
                    <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                        <option value="">Select Nuclear Receptor</option>
                        <?php foreach ($memberValues as $value): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="logical_operator[]">
                        <option value="OR">OR</option>
                        <option value="AND">AND</option>
                    </select>
                    <button type="button" onclick="removeCondition(this)">Remove</button>
                </div>
            </div>
            <button type="button" class="custom-button" onclick="addCondition()">Add More Fields</button> <br><br>
            <button type="submit" class="add-more-button" onclick="prepareForm()">Search</button>
        </form>
        <br>
        <?php if ($results): ?>
            <table>
                <tr>
                    <th>Sr.No.</th>
                    <th>Uniprot ID</th>
                    <th>Organism</th>
                    <th>Uniprot ID Isoforms</th>
                    <th>PTM</th>
                    <th>Domain</th>
                    <th>Source</th>
                    <th>Modifier</th>
                    <th>Nuclear Receptor</th>
                </tr>
                <?php 
                $serialNo = 1; // Initialize serial number
                
                // Check if results contain any rows
                if (mysql_num_rows($results) > 0) {
                    while ($row = mysql_fetch_assoc($results)): ?>
                        <tr>
                            <td><?php echo $serialNo++; ?></td>
                            <td><?php echo (!empty($row['Uniprot_ID']) ? htmlspecialchars($row['Uniprot_ID']) : "NA"); ?></td>
                            <td><?php echo (!empty($row['Organism']) ? htmlspecialchars($row['Organism']) : "NA"); ?></td>
                            <td><?php echo (!empty($row['Uniprot_ID_Isoforms']) ? htmlspecialchars($row['Uniprot_ID_Isoforms']) : "NA"); ?></td>
                            <td><?php echo (!empty($row['PTM']) ? htmlspecialchars($row['PTM']) : "NA"); ?></td>
                            <td><?php echo (!empty($row['Domain']) ? htmlspecialchars($row['Domain']) : "NA"); ?></td>
                            <td><?php echo (!empty($row['Source']) ? htmlspecialchars($row['Source']) : "NA"); ?></td>
                            <td><?php echo (!empty($row['Modifier_1']) ? htmlspecialchars($row['Modifier_1']) : "NA"); ?></td>
                            <td><?php echo (!empty($row['Member']) ? htmlspecialchars($row['Member']) : "NA"); ?></td>
                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='9' style='text-align:center;'>No results found</td></tr>";
                }
                ?>
            </table>
        <?php endif; ?>
    </div>
    <script>
        // Function to prepare form before submission
        function prepareForm() {
            // Loop through all conditions and ensure the right input is being submitted
            const conditions = document.querySelectorAll('.condition');
            
            conditions.forEach(condition => {
                const fieldSelect = condition.querySelector('select[name="fieldname[]"]');
                const selectedField = fieldSelect.value;
                const dropdowns = condition.querySelectorAll('.keyword-dropdown');
                
                dropdowns.forEach(dropdown => {
                    if (!dropdown.classList.contains('hidden')) {
                        // Set the value of the dropdown to the keyword
                        condition.querySelector('input[name="keyword-dropdown[]"]').value = dropdown.value;
                    }
                });
            });
        }
        
        // Function to toggle between dropdown based on selected field
        function toggleInputType(selectElement) {
            const container = selectElement.parentElement;
            const selectedField = selectElement.value;
            
            // Hide all dropdowns first
            const allDropdowns = container.querySelectorAll('.keyword-dropdown');
            allDropdowns.forEach(dropdown => {
                dropdown.classList.add('hidden');
            });
            
            // Show the appropriate dropdown based on selected field
            const dropdown = container.querySelector(`.keyword-dropdown[name="keyword-dropdown[]"]`);
            dropdown.classList.remove('hidden');
        }

        function addCondition() {
            const conditionsDiv = document.getElementById('conditions');
            const newCondition = document.createElement('div');
            newCondition.className = 'condition';
            
            // Create the HTML for the new condition
            newCondition.innerHTML = `
                <select name="fieldname[]" onchange="toggleInputType(this)">
                    <option value="Organism">Organism</option>
                    <option value="PTM">PTM</option>
                    <option value="Domain">Domain</option>
                    <option value="Source">Source</option>
                    <option value="Modifier_1">Modifier</option>
                    <option value="Member">Nuclear Receptor</option>
                </select>
                <select name="operator[]">
                    <option value="LIKE">LIKE</option>
                    <option value="NOT LIKE">NOT LIKE</option>
                </select>
                
                <!-- Organism dropdown (default) -->
                <select name="keyword-dropdown[]" class="keyword-dropdown">
                    <option value="">Select Organism</option>
                    <?php foreach ($organismValues as $value): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- PTM dropdown (default) -->
                <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                    <option value="">Select PTM</option>
                    <?php foreach ($ptmValues as $value): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Domain dropdown (default) -->
                <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                    <option value="">Select Domain</option>
                    <?php foreach ($domainValues as $value): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Source dropdown (default) -->
                <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                    <option value="">Select Source</option>
                    <?php foreach ($sourceValues as $value): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Modifier_1 dropdown (default) -->
                <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                    <option value="">Select Modifier</option>
                    <?php foreach ($modifier1Values as $value): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <!-- Member dropdown (default) -->
                <select name="keyword-dropdown[]" class="keyword-dropdown hidden">
                    <option value="">Select Nuclear Receptor</option>
                    <?php foreach ($memberValues as $value): ?>
                        <option value="<?php echo htmlspecialchars($value); ?>"><?php echo htmlspecialchars($value); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select name="logical_operator[]">
                    <option value="OR">OR</option>
                    <option value="AND">AND</option>
                </select>
                <button type="button" onclick="removeCondition(this)">Remove</button>`;
                
            conditionsDiv.appendChild(newCondition);
        }
        

        function removeCondition(button) {
            const conditionDiv = button.parentElement;
            conditionDiv.remove();
        }

        // Initialize the input type for the first condition
        document.addEventListener('DOMContentLoaded', function() {
            const firstFieldSelect = document.querySelector('.condition select[name="fieldname[]"]');
            if (firstFieldSelect) {
                toggleInputType(firstFieldSelect);
            }
            
            // Add submit event listener to the form
            document.getElementById('searchForm').addEventListener('submit', function(event) {
                prepareForm();
            });
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>