<?php 
// It's good practice to include connection files at the very top.
include('conn3.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pathogen Genome Data</title>

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
    <!-- Google Fonts for a cleaner look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        /* General Body and Page Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .main-container {
            width: 90%;
            max-width: 1400px;
            margin: 40px auto;
        }

        /* Card-based layout for each Antibiotic Class */
        .antibiotic-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 40px;
            padding: 25px 30px;
            border-top: 5px solid #007bff; /* Default blue border */
        }
        
        .filter-card {
            border-top: 5px solid #6c757d; /* A neutral color for the filter */
        }

        .antibiotic-card h2 {
            margin-top: 0;
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 700;
            border-bottom: 2px solid #ecf0f1;
            padding-bottom: 15px;
        }

        .phenotype-title {
            font-size: 1.3em;
            font-weight: 500;
            margin-top: 20px;
            margin-bottom: 15px;
        }

        /* Wrapper for each table to apply styles and responsive behavior */
        .table-wrapper {
            overflow-x: auto;
            margin-bottom: 30px;
            border-radius: 6px;
            border: 1px solid #ddd; /* Base border */
        }

        /* --- Phenotype Specific Colors --- */
        .phenotype-susceptible .phenotype-title { color: #28a745; }
        .phenotype-susceptible { border-left: 5px solid #28a745; }
        .phenotype-susceptible table thead { background-color: #28a745; }

        .phenotype-resistant .phenotype-title { color: #dc3545; }
        .phenotype-resistant { border-left: 5px solid #dc3545; }
        .phenotype-resistant table thead { background-color: #dc3545; }

        .phenotype-intermediate .phenotype-title { color: #ffc107; }
        .phenotype-intermediate { border-left: 5px solid #ffc107; }
        .phenotype-intermediate table thead { background-color: #ffc107; }
        .phenotype-intermediate table thead th { color: #333; } /* Darker text for yellow bg */

        /* --- General and DataTables Styling --- */
        table.dataTable {
            width: 100% !important;
            border-collapse: collapse;
            border: none;
        }

        table.dataTable thead th {
            color: white;
            font-weight: 500;
            padding: 12px 18px;
            border-bottom: none;
        }

        table.dataTable tbody td {
            padding: 12px 18px;
            border-top: 1px solid #e9ecef;
        }

        table.dataTable tbody tr:hover {
            background-color: #f1f1f1;
        }
        
        /* DataTables controls styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 10px 0;
        }

        /* Style for the pathogen filter */
        #pathogenFilter {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
    </style>
</head>
<body>

<?php include('header.php'); ?>

<div class="main-container">

<?php
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div class='antibiotic-card' style='border-color: #dc3545;'><h2>Error</h2><p>Database connection failed. Please try again later.</p></div>");
}

// --- Fetch distinct pathogens for the filter dropdown ---
$pathogen_query = "SELECT DISTINCT Pathogen FROM Genome_section WHERE Pathogen IS NOT NULL AND Pathogen != '' ORDER BY Pathogen ASC";
$pathogen_result = $conn->query($pathogen_query);
$pathogens = [];
if ($pathogen_result && $pathogen_result->num_rows > 0) {
    while($row = $pathogen_result->fetch_assoc()) {
        $pathogens[] = $row['Pathogen'];
    }
}

// --- Set the selected pathogen ---
$selected_pathogen = '';
if (isset($_GET['pathogen'])) {
    $selected_pathogen = $_GET['pathogen'];
} 
elseif (!empty($pathogens)) {
    $selected_pathogen = $pathogens[0];
}
?>

<!-- Pathogen Filter Form -->
<div class="antibiotic-card filter-card">
    <h2>Filter by Pathogen</h2>
    <form action="" method="GET">
        <select name="pathogen" id="pathogenFilter" onchange="this.form.submit()">
            <option value="">-- Select a Pathogen --</option>
            <?php
            foreach ($pathogens as $p) {
                $isSelected = ($p === $selected_pathogen) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($p) . '" ' . $isSelected . '>' . htmlspecialchars($p) . '</option>';
            }
            ?>
        </select>
    </form>
</div>

<?php
if (!empty($selected_pathogen)) {
    // The SQL query correctly brings non-empty accessions first. This is good for the initial HTML render.
    $sql = "SELECT Antibiotic_Class, Phenotype, Assembly_Accession, Isolate_acession, Antibiotic, Aware_Category 
            FROM Genome_section 
            WHERE Pathogen = ? 
            AND Antibiotic_Class IS NOT NULL AND Antibiotic_Class != ''
            ORDER BY 
                Antibiotic_Class, 
                FIELD(Phenotype, 'Susceptible', 'Resistant', 'Intermediate'),
                CASE 
                    WHEN Assembly_Accession IS NULL OR Assembly_Accession = '' THEN 1 
                    ELSE 0 
                END,
                Assembly_Accession ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $selected_pathogen);
    $stmt->execute();
    $result = $stmt->get_result();

    $dataByClass = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $dataByClass[$row['Antibiotic_Class']][$row['Phenotype']][] = $row;
        }
    }
    $stmt->close();
    
    if (empty($dataByClass)) {
        echo "<div class='antibiotic-card'><p>No data found for the pathogen: <strong>" . htmlspecialchars($selected_pathogen) . "</strong></p></div>";
    } else {
        foreach ($dataByClass as $class => $phenotypes) {
            echo "<div class='antibiotic-card'>";
            echo "<h2>Antibiotic Class: " . htmlspecialchars($class) . "</h2>";

            foreach ($phenotypes as $phenotype => $rows) {
                if (empty($rows)) continue; 

                $phenotypeClass = 'phenotype-' . strtolower($phenotype);
                $tableId = 'table_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $class . '_' . $phenotype);

                echo "<div class='table-wrapper $phenotypeClass'>";
                echo "<h4 class='phenotype-title'>" . htmlspecialchars($phenotype) . "</h4>";
                
                echo "<table id='$tableId' class='display' style='width:100%'>";
                echo "<thead><tr>
                        <th>Assembly Accession</th>
                        <th>Isolate Accession</th>
                        <th>Antibiotic</th>
                        <th>Aware Category</th>
                      </tr></thead><tbody>";

                foreach ($rows as $row) {
                    // ***CHANGE 1: PHP MODIFICATION***
                    // Prepare the value and a sort key.
                    $assembly_val = htmlspecialchars($row['Assembly_Accession'] ?? '');
                    // If the value is empty, use a sort key that comes last (~). Otherwise, use the value itself.
                    $sort_key = !empty($assembly_val) ? $assembly_val : '~~~~~~~~~~';
                    
                    // Add the data-sort attribute to the <td>
                    echo "<tr>
                            <td data-sort='" . $sort_key . "'>" . $assembly_val . "</td>
                            <td>" . htmlspecialchars($row['Isolate_acession'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['Antibiotic'] ?? '') . "</td>
                            <td>" . htmlspecialchars($row['Aware_Category'] ?? '') . "</td>
                          </tr>";
                }

                echo "</tbody></table>";
                echo "</div>";
            }
            
            echo "</div>";
        }
    }
} else {
    echo "<div class='antibiotic-card'><p>No pathogens found in the database. Please add data to begin.</p></div>";
}
$conn->close();
?>

</div> <!-- end .main-container -->

<!-- JavaScript libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Initialize all DataTables -->
<script>
    $(document).ready(function () {
        $('table.display').DataTable({
            "pageLength": 5,
            "lengthMenu": [ [5, 10, 25, -1], [5, 10, 25, "All"] ],
            // ***CHANGE 2: JAVASCRIPT MODIFICATION***
            // Set the initial sort order to be the first column (index 0), ascending.
            // DataTables will automatically use the `data-sort` attribute we added.
            "order": [[ 0, 'asc' ]]
        });
    });
</script>

<?php include('footer.php'); ?>

</body>
</html>