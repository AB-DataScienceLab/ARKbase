<?php
header('Content-Type: application/json');
include 'conn.php'; // Make sure this path is correct

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// IMPORTANT: For optimal performance, ensure your database table has indexes on the columns used in the WHERE clause.
// Example: ALTER TABLE `ppi_2` ADD INDEX `idx_pathogen` (`pathogen`);

$table = 'ppi_2'; 

// --- Build Filter Conditions from GET parameters ---
$whereConditions = [];
$filterParams = [];
$types = '';

$filterableColumns = ['pathogen', 'protien1_id', 'protien2_id', 'bcq1', 'bcq2'];

foreach ($filterableColumns as $column) {
    if (isset($_GET['filter_' . $column]) && !empty(trim($_GET['filter_' . $column]))) {
        $filterValue = trim($_GET['filter_' . $column]);
        $whereConditions[] = "`$column` = ?";
        $filterParams[] = $filterValue;
        $types .= 's';
    }
}
$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// --- Database Query ---
$sql = "SELECT protien1_id, protien2_id FROM `$table` $whereClause";
$stmt = mysqli_prepare($conn, $sql);

if (!empty($filterParams)) {
    mysqli_stmt_bind_param($stmt, $types, ...$filterParams);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$edges = [];
$protein1_ids = [];
$protein2_ids = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $p1 = $row['protien1_id'];
        $p2 = $row['protien2_id'];

        // Use associative arrays for efficient O(1) storage and lookup.
        $protein1_ids[$p1] = true;
        $protein2_ids[$p2] = true;

        // Add the interaction as an edge for Cytoscape.
        $edges[] = ['data' => ['source' => $p1, 'target' => $p2]];
    }
}

mysqli_close($conn);

// --- Efficiently Create Nodes with Type Information ---
$nodes = [];
// Combine all unique protein IDs from both columns.
$uniqueProteinIDs = array_keys(array_merge($protein1_ids, $protein2_ids));

foreach ($uniqueProteinIDs as $proteinID) {
    $is_p1 = isset($protein1_ids[$proteinID]);
    $is_p2 = isset($protein2_ids[$proteinID]);
    
    $node_type = 'default';
    if ($is_p1 && !$is_p2) {
        $node_type = 'protein_1_only'; // Only appears in protein1_id column
    } elseif (!$is_p1 && $is_p2) {
        $node_type = 'protein_2_only'; // Only appears in protein2_id column
    } elseif ($is_p1 && $is_p2) {
        $node_type = 'protein_both'; // Appears in both columns
    }

    // Add node with its ID and type for coloring.
    $nodes[] = ['data' => ['id' => $proteinID, 'type' => $node_type]];
}

// Return the final data structure in JSON format.
echo json_encode(['nodes' => $nodes, 'edges' => $edges]);
?>