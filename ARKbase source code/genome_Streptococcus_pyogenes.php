<?php include('header.php'); ?>
<?php include('conn3.php'); ?>

<div style="width:85%; margin:auto;">

<?php
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

// Set the pathogen to filter by (change this for different pages)
$pathogen = 'Streptococcus pyogenes';

// Get unique antibiotic classes for this pathogen
$sql = "SELECT DISTINCT Antibiotic_Class FROM Genome_section WHERE Pathogen = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $pathogen);
$stmt->execute();
$result = $stmt->get_result();
$classes = [];

while ($row = $result->fetch_assoc()) {
    if (!empty($row['Antibiotic_Class'])) {
        $classes[] = $row['Antibiotic_Class'];
    }
}

foreach ($classes as $class) {
    $hasData = false; // to track if any phenotype has data for this class

    // First, loop through phenotypes and check if any data exists
    foreach (['Susceptible', 'Resistant', 'Intermediate'] as $phenotype) {
        $sql = "SELECT 1 FROM Genome_section WHERE Pathogen = ? AND Antibiotic_Class = ? AND Phenotype = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $pathogen, $class, $phenotype);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $hasData = true;
            break;
        }
    }

    if (!$hasData) continue; // Skip this Antibiotic Class if no data at all

    echo "<h2 style='margin-top:40px;'>Antibiotic Class: <u>$class</u></h2>";

    foreach (['Susceptible', 'Resistant', 'Intermediate'] as $phenotype) {
        // Table ID must be unique
        $tableId = strtolower(str_replace([' ', '/', '-'], '_', $class . '_' . $phenotype));

        // Get actual data rows
        $sql = "SELECT Assembly_Accession, Isolate_acession, Antibiotic, Aware_Category 
                FROM Genome_section 
                WHERE Pathogen = ? AND Antibiotic_Class = ? AND Phenotype = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $pathogen, $class, $phenotype);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows == 0) continue; // Skip if no data for this phenotype

        // Set color class
        if ($phenotype == 'Susceptible') {
            $color = '#d4edda'; // green
        } elseif ($phenotype == 'Resistant') {
            $color = '#f8d7da'; // red
        } elseif ($phenotype == 'Intermediate') {
            $color = '#fff3cd'; // yellow
        } else {
            $color = '#ffffff'; // default white
        }

        echo "<h4 style='color: #333;'>$phenotype</h4>";
        echo "<div style='overflow-x:auto;'><table id='$tableId' class='display' style='background-color:$color; width:100%; border: 1px solid #ccc;'>";
        echo "<thead><tr>
                <th>Assembly Accession</th>
                <th>Isolate Accession</th>
                <th>Antibiotic</th>
                <th>Aware Category</th>
              </tr></thead><tbody>";

        while ($row = $res->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Assembly_Accession']}</td>
                    <td>{$row['Isolate_acession']}</td>
                    <td>{$row['Antibiotic']}</td>
                    <td>{$row['Aware_Category']}</td>
                  </tr>";
        }

        echo "</tbody></table></div><br><br>";
    }
}

$conn->close();
?>

</div> <!-- end container -->

<!-- Include DataTables JS & CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Initialize all tables -->
<script>
    $(document).ready(function () {
        $('table.display').DataTable({
            "pageLength": 5
        });
    });
</script>

<?php include('footer.php'); ?>
