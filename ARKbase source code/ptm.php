<?php
// Include necessary files
include 'header.php';
include 'conn.php';

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysql_error());
}

// Function to get distinct values for NRNC_symbol and PTM
function getDistinctValues($conn, $column) {
    $sql = "SELECT DISTINCT `$column` FROM ProteinData";
    $result = mysql_query($sql, $conn);

    if (!$result) {
        die("Query failed: " . mysql_error());
    }

    $values = [];
    while ($row = mysql_fetch_assoc($result)) {
        $values[] = $row[$column];
    }
    return $values;
}

// Function to escape special characters for LIKE queries
function escapeLike($keyword) {
    return str_replace(array('%', '_'), array('\\%', '\\_'), $keyword);
}

// Function to search the database
function searchDatabase($conn, $NRNCFilter = null, $PTMFilter = null) {
    $sql = "SELECT * FROM ProteinData WHERE 1";

    // Append filters if provided
    if ($NRNCFilter) {
        $sql .= " AND `NRNC_symbol` = '" . mysql_real_escape_string($NRNCFilter) . "'";
    }

    if ($PTMFilter) {
        $sql .= " AND `PTM` = '" . mysql_real_escape_string($PTMFilter) . "'";
    }

    $result = mysql_query($sql, $conn);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysql_error());
    }

    return $result;
}

// Get distinct values for NRNC_symbol and PTM
$NRNCValues = getDistinctValues($conn, 'NRNC_symbol');
$PTMValues = getDistinctValues($conn, 'PTM');

// Handle form submission
$NRNCFilter = isset($_POST['NRNC_symbol']) ? $_POST['NRNC_symbol'] : null;
$PTMFilter = isset($_POST['PTM']) ? $_POST['PTM'] : null;

// Display the dropdown form
echo '<form method="POST" action="">
        <label for="NRNC_symbol">Select NRNC Symbol:</label>
        <select name="NRNC_symbol">
            <option value="">--Select NRNC Symbol--</option>';
foreach ($NRNCValues as $value) {
    echo "<option value=\"$value\" " . ($value == $NRNCFilter ? 'selected' : '') . ">$value</option>";
}
echo '</select><br>';

echo '<label for="PTM">Select PTM:</label>
      <select name="PTM">
          <option value="">--Select PTM--</option>';
foreach ($PTMValues as $value) {
    echo "<option value=\"$value\" " . ($value == $PTMFilter ? 'selected' : '') . ">$value</option>";
}
echo '</select><br>';

echo '<input type="submit" value="Filter Results">
      </form>';

// Handle the filtered results
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $result = searchDatabase($conn, $NRNCFilter, $PTMFilter);

    if (mysql_num_rows($result) > 0) {
        echo "<table border='1'>";
        echo "<tr>
                <th>Group Name</th>
                <th>NRNC Symbol</th>
                <th>Gene</th>
                <th>Member</th>
                <th>Uniprot ID</th>
                <th>TrEMBL ID</th>
                <th>Organism</th>
                <th>Isoforms</th>
                <th>Uniprot ID Isoforms</th>
                <th>TrEMBL ID 1</th>
                <th>PTM</th>
                <th>Site of PTM</th>
                <th>Domain</th>
                <th>Cell Line</th>
                <th>Sequence</th>
                <th>Modifier 1</th>
                <th>Modifier 1 Uniprot</th>
                <th>Modifier 1 TrEMBL ID</th>
                <th>Modifier 2</th>
                <th>Modifier 2 Uniprot</th>
                <th>Modifier 2 TrEMBL ID</th>
                <th>Effect</th>
                <th>Reference 1</th>
                <th>Reference 2</th>
                <th>Source</th>
              </tr>";

        while ($row = mysql_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['Group_name']) . "</td>
                    <td>" . htmlspecialchars($row['NRNC_symbol']) . "</td>
                    <td>" . htmlspecialchars($row['Gene']) . "</td>
                    <td>" . htmlspecialchars($row['Member']) . "</td>
                    <td>" . htmlspecialchars($row['Uniprot_ID']) . "</td>
                    <td>" . htmlspecialchars($row['TrEMBL_ID']) . "</td>
                    <td>" . htmlspecialchars($row['Organism']) . "</td>
                    <td>" . htmlspecialchars($row['Isoforms']) . "</td>
                    <td>" . htmlspecialchars($row['Uniprot_ID_Isoforms']) . "</td>
                    <td>" . htmlspecialchars($row['TrEMBL_ID_1']) . "</td>
                    <td>" . htmlspecialchars($row['PTM']) . "</td>
                    <td>" . htmlspecialchars($row['SITE_OF_PTM']) . "</td>
                    <td>" . htmlspecialchars($row['Domain']) . "</td>
                    <td>" . htmlspecialchars($row['Cell_line']) . "</td>
                    <td>" . htmlspecialchars($row['Sequence']) . "</td>
                    <td>" . htmlspecialchars($row['Modifier_1']) . "</td>
                    <td>" . htmlspecialchars($row['Modifier_1_Uniprot']) . "</td>
                    <td>" . htmlspecialchars($row['Modifier_1_TrEMBL_ID']) . "</td>
                    <td>" . htmlspecialchars($row['Modifier_2']) . "</td>
                    <td>" . htmlspecialchars($row['Modifier_2_Uniprot']) . "</td>
                    <td>" . htmlspecialchars($row['Modifier_2_TrEMBL_ID']) . "</td>
                    <td>" . htmlspecialchars($row['Effect']) . "</td>
                    <td>" . htmlspecialchars($row['Reference_1']) . "</td>
                    <td>" . htmlspecialchars($row['Reference_2']) . "</td>
                    <td>" . htmlspecialchars($row['Source']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No results found.</p>";
    }
}
?>