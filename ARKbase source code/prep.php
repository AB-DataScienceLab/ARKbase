<!DOCTYPE html>
<?php include 'header.php'; ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>PTM Profiler</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <style>
/*        body {*/
/*            font-family: Arial, sans-serif;*/
/*            background-color: #f4f4f4;*/
/*            margin: 0;*/
/*            padding: 0;*/
/*        }*/
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
           .container1 {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
             max-width: 2200px; /* Set a maximum width */
            min-height: auto; /* Set a minimum height to ensure enough space */
            margin: 20px auto; /* Center the container with margin */
        }

        .tables-container {
            display: flex; /* Use flexbox for layout */
            gap: 20px; /* Space between columns */
        }

        .table-column {
            flex: 1; /* Allow both columns to grow equally */
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Form styles */
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        form label {
            font-weight: bold;
            margin-right: 10px;
        }

        form select, form input[type="submit"] {
            padding: 8px 12px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        form input[type="submit"]:hover {
            background-color: #45a049;
        }


    </style>
</head>
<body>

<?php
// Include necessary files
include 'conn.php';

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysql_error());
}

// Function to get distinct values for Uniprot_ID and PTM from all three tables
function getDistinctValues($conn, $table, $column) {
    $sql = "SELECT DISTINCT `$column` FROM `$table` WHERE `$column`<>''";
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

// Get distinct values for Uniprot_ID and PTM from all three tables
$uniprotValues = array_unique(array_merge(
    getDistinctValues($conn, 'ProteinTable5', 'Gene'),
    getDistinctValues($conn, 'Minds_tb', 'Gene'),
    getDistinctValues($conn, 'Musite_tb', 'Gene')
));

$ptmValues = array_unique(array_merge(
    getDistinctValues($conn, 'ProteinTable5', 'PTM'),
    getDistinctValues($conn, 'Minds_data', 'PTM'),
    getDistinctValues($conn, 'Musite_data', 'PTM')
));

// Function to search the database for Uniprot_ID and PTM
function searchDatabase($conn, $uniprotFilter = null , $ptmFilter = null) {
    $results = [];

    // Search in ProteinTable2
    $sql = "SELECT * FROM ProteinTable5 WHERE 1";
    if ($uniprotFilter) {
        $sql .= " AND `Gene` = '" . mysql_real_escape_string($uniprotFilter) . "'";
    }
    if ($ptmFilter) {
        $sql .= " AND `PTM` = '" . mysql_real_escape_string($ptmFilter) . "'";
    }
    $sql .= " ORDER BY `PTM` ASC"; 
    $result = mysql_query($sql, $conn);
    if (!$result) {
        die("Query failed: " . mysql_error());
    }
    $results['ProteinTable2'] = $result;

    // Search in Minds_data
    $sql = "SELECT * FROM Minds_tb WHERE 1";
    if ($uniprotFilter) {
        $sql .= " AND `Gene` = '" . mysql_real_escape_string($uniprotFilter) . "'";
    }
    if ($ptmFilter) {
        $sql .= " AND `PTM` = '" . mysql_real_escape_string($ptmFilter) . "'";
    }
    $result = mysql_query($sql, $conn);
    if (!$result) {
        die("Query failed: " . mysql_error());
    }
    $results['Minds_tb'] = $result;

    // Search in Musite_data
    $sql = "SELECT * FROM Musite_tb WHERE 1";
    if ($uniprotFilter) {
        $sql .= " AND `Gene` = '" . mysql_real_escape_string($uniprotFilter) . "'";
    }
    if ($ptmFilter) {
        $sql .= " AND `PTM` = '" . mysql_real_escape_string($ptmFilter) . "'";
    }
    $result = mysql_query($sql, $conn);
    if (!$result) {
        die("Query failed: " . mysql_error());
    }
    $results['Musite_tb'] = $result;

    return $results;
}

// Handle form submission
$uniprotFilter = isset($_POST['Gene']) ? $_POST['Gene'] : null;
$ptmFilter = isset($_POST['PTM']) ? $_POST['PTM'] : null;

// Display the static tabular data
echo '<div class="container">';
echo '<div class="tables-container">';
echo '<div class="table-column">';
echo '<h2><center>MUSITE-DEEP</center></h2>';
echo '<table>
        <thead>
            <tr>
                <th>Modification</th>
                <th>True Positive</th>
                <th>True Negative</th>
                <th>False Positive</th>
                <th>False Negative</th>
                <th>Precision%</th>
                <th>Accuracy%</th>
                <th>Sensitivity%</th>
                <th>Specificity%</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ACETYLATION</td>
                <td>91</td>
                <td>3034</td>
                <td>5583</td>
                <td>3</td>
                <td>1.6</td>
                <td>35.87</td>
                <td>96.81</td>
                <td>35.21</td>
            </tr>
            <tr>
                <td>PHOSPHORYLATION</td>
                <td>938</td>
                <td>19975</td>
                <td>15422</td>
                <td>5</td>
                <td>5.73</td>
                <td>57.55</td>
                <td>99.47</td>
                <td>56.43</td>
            </tr>
            <tr>
                <td>METHYLATION</td>
                <td>214</td>
                <td>6597</td>
                <td>10821</td>
                <td>1</td>
                <td>1.94</td>
                <td>38.63</td>
                <td>99.53</td>
                <td>37.87</td>
            </tr>
            <tr>
                <td>SUMOYLATION</td>
                <td>151</td>
                <td>23</td>
                <td>5501</td>
                <td>2</td>
                <td>2.67</td>
                <td>3.06</td>
                <td>98.69</td>
                <td>0.42</td>
 </tr>
            <tr>
                <td>UBIQUITINATION</td>
                <td>78</td>
                <td>23</td>
                <td>5607</td>
                <td>0</td>
                <td>1.37</td>
                <td>1.77</td>
                <td>100</td>
                <td>0.41</td>
            </tr>
            <tr>
                <td>PALMITOYLATION</td>
                <td>3</td>
                <td>14</td>
                <td>3309</td>
                <td>0</td>
                <td>0.09</td>
                <td>0.51</td>
                <td>100</td>
                <td>0.42</td>
            </tr>
            <tr>
                <td>GLYCOSYLATION</td>
                <td>19</td>
                <td>49</td>
                <td>13997</td>
                <td>0</td>
                <td>0.14</td>
                <td>0.48</td>
                <td>100</td>
                <td>0.35</td>
            </tr>
        </tbody>
    </table>';
echo '</div>'; // Close MUSITE-DEEP column

echo '<div class="table-column">';
echo '<h2><center>MIND-S</center></h2>';
echo '
    <table>
        <thead>
            <tr>
                <th>Modification</th>
                <th>True Positive</th>
                <th>True Negative</th>
                <th>False Positive</th>
                <th>False Negative</th>
                <th>Precision%</th>
                <th>Accuracy%</th>
                <th>Sensitivity%</th>
                <th>Specificity%</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ACETYLATION</td>
                <td>25</td>
                <td>8270</td>
                <td>242</td>
                <td>69</td>
                <td>9.36</td>
                <td>96.39</td>
                <td>26.6</td>
                <td>97.16</td>
            </tr>
            <tr>
                <td>PHOSPHORYLATION</td>
                <td>188</td>
                <td>34730</td>
                <td>667</td>
                <td>755</td>
                <td>21.99</td>
                <td>94.6</td>
                <td>19.94</td>
                <td>97.37</td>
            </tr>
            <tr>
                <td>METHYLATION</td>
                <td>9</td>
                <td>17361</td>
                <td>57</td>
                <td>206</td>
                <td>13.64</td>
                <td>98.51</td>
                <td>4.19</td>
                <td>99.67</td>
            </tr>
            <tr>
                <td>SUMOYLATION</td>
                <td>34</td>
                <td>5280</td>
                <td>175</td>
                <td>119</td>
                <td>16.27</td>
                <td>94.76</td>
                <td>22.22</td>
                <td>96.79</td>
            </tr>
            <tr>
                <td>UBIQUITINATION</td>
                <td>15</td>
                <td>5536</td>
                <td>94</td>
                <td>63</td>
                <td>13.76</td>
                <td>97.25</td>
                <td>19.23</td>
                <td>98.33</td>
            </tr>
            <tr>
                <td>PALMITOYLATION</td>
                <td>1</td>
                <td>3216</td>
                <td>65</td>
                <td>2</td>
                <td>1.52</td>
                <td>97.96</td>
                <td>33.33</td>
                <td>98.02</td>
            </tr>
            <tr>
                <td>GLYCOSYLATION</td>
                <td>2</td>

                <td>14024</td>
                <td>22</td>
                <td>17</td>
                <td>8.33</td>
                <td>99.72</td>
                <td>10.53</td>
                <td>99.84</td>
            </tr>
        </tbody>
    </table>';
echo '</div>'; // Close MIND-S column
echo '</div>'; // Close tables-container

// Display the search form
echo '<div class="right-column">';
echo '<form method="POST" action="">
        <label for="Gene">Select Gene:</label>
        <select name="Gene">
            <option value="">--Select Gene--</option>';
foreach ($uniprotValues as $value) {
    echo "<option value=\"$value\" " . ($value == $uniprotFilter ? 'selected' : '') . ">$value</option>";
}
echo '</select><br>';

echo '<label for="PTM">Select PTM:</label>
      <select name="PTM">
          <option value="">--Select PTM--</option>';
foreach ($ptmValues as $value) {
    echo "<option value=\"$value\" " . ($value == $ptmFilter ? 'selected' : '') . ">$value</option>";
}
echo '</select><br>';

echo '<input type="submit" value="Filter Results">
      </form>';

// Handle the filtered results
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $results = searchDatabase($conn, $uniprotFilter, $ptmFilter);

    // Display results from ProteinTable2
    if (mysql_num_rows($results['ProteinTable2']) > 0) {
        echo "<h2><center>Results from ProteinData</center></h2>";
        echo "<div class='results' style='max-width: 100%; overflow-x: auto;'>";
        echo "<table border='1'>";
        echo "<tr>
                <th>Sr. No.</th>
                <th>Sub-Family</th>
                <th>GROUP</th>
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
        while ($row = mysql_fetch_assoc($results['ProteinTable2'])) {
            echo "<tr>
                   <td>" . $serialNo++ . "</td>
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
        echo "<p><b>No results found in ProteinData.</b></p>";
    }

    // Display results from Minds_data
    if (mysql_num_rows($results['Minds_tb']) > 0) {
        echo "<h2><center>Predictions from Mind-S</center></h2>";
        echo "<table border='1'>";
        echo "<tr>
                <th>Sr. No.</th>
                <th>UniprotID</th>
                <th>NRNC_symbol</th>
                <th>Gene</th>
                <th>Member</th>
                <th>Isoforms</th>
                <th>Site</th>
                <th>PTM_Type</th>
                <th>PTM</th>
                <th>Pred_score</th>
              </tr>";

        $serialNo = 1; // Initialize serial number
        while ($row = mysql_fetch_assoc($results['Minds_tb'])) {
            echo "<tr>
                    <td>" . $serialNo++ . "</td>
                    <td>" . htmlspecialchars($row['UniprotID']) . "</td>
                    <td>" . htmlspecialchars($row['NRNC_symbol']) . "</td>
                    <td>" . htmlspecialchars($row['Gene']) . "</td>
                    <td>" . htmlspecialchars($row['Member']) . "</td>
                    <td>" . htmlspecialchars($row['Isoforms']) . "</td>
                    <td>" . htmlspecialchars($row['Site']) . "</td>
                    <td>" . htmlspecialchars($row['PTM_Type']) . "</td>
                    <td>" . htmlspecialchars($row['PTM']) . "</td>
                    <td>" . htmlspecialchars($row['Pred_score']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p><b>No results found in Mind-S.</b></p>";
    }

    // Display results from Musite_data
    if (mysql_num_rows($results['Musite_tb']) > 0) {
        echo "<h2><center>Predictions from MusiteDeep</center></h2>";
        echo "<table border='1'>";
        echo "<tr>
                <th>Sr. No.</th>
                <th>ID</th>
                <th>UniprotID</th>
                <th>NRNC_symbol</th>
                <th>Gene</th>
                <th>Member</th>
                <th>Isoforms</th>
                <th>Position</th>
                <th>Residue</th>
                <th>PTM_score</th>
                <th>PTM</th>
                <th>Cutoff = 0.8</th>
 <th>Site</th>
              </tr>";

        $serialNo = 1; // Initialize serial number
        while ($row = mysql_fetch_assoc($results['Musite_tb'])) {
            echo "<tr>
                    <td>" . $serialNo++ . "</td>
                    <td>" . htmlspecialchars($row['ID']) . "</td>
                    <td>" . htmlspecialchars($row['UniprotID']) . "</td>
                    <td>" . htmlspecialchars($row['NRNC_symbol']) . "</td>
                    <td>" . htmlspecialchars($row['Gene']) . "</td>
                    <td>" . htmlspecialchars($row['Member']) . "</td>
                    <td>" . htmlspecialchars($row['Isoforms']) . "</td>
                    <td>" . htmlspecialchars($row['Position']) . "</td>
                    <td>" . htmlspecialchars($row['Residue']) . "</td>
                    <td>" . htmlspecialchars($row['Ptm_score']) . "</td>
                    <td>" . htmlspecialchars($row['PTM']) . "</td>
                    <td>" . htmlspecialchars($row['Cutoff']) . "</td>
                    <td>" . htmlspecialchars($row['Site']) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p><b>No results found in MusiteDeep.</b></p>";
    }
}
echo '</div>'; // Close right-column div

echo '</div>'; // 
 include 'footer.php'; ?>