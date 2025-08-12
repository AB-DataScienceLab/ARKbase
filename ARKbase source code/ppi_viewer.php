<?php
// --- SETUP AND CONFIGURATION ---

// We assume 'conn.php' is correctly configured and produces NO output.
include 'conn.php'; 

// --- Pathogen Links & Name Mapping ---
$pathogenLinks = [
    'Acinetobacter_baumannii' => 'https://datascience.imtech.res.in/anshu/arkbase/ab1.php', 'Klebsiella_pneumoniae' => 'https://datascience.imtech.res.in/anshu/arkbase/kp.php', 'Escherichia_coli' => 'https://datascience.imtech.res.in/anshu/arkbase/Ecoli.php', 'Shigella_flexneri' => 'https://datascience.imtech.res.in/anshu/arkbase/Shigella_flexneri.php', 'Shigella_sonnei' => 'https://datascience.imtech.res.in/anshu/arkbase/Shigella_sonnei.php', 'Enterococcus_faecium' => 'https://datascience.imtech.res.in/anshu/arkbase/ef.php', 'Pseudomonas_aeruginosa' => 'https://datascience.imtech.res.in/anshu/arkbase/pa.php', 'Salmonella_enterica' => 'https://datascience.imtech.res.in/anshu/arkbase/se.php', 'Neisseria_gonorrhoeae' => 'https://datascience.imtech.res.in/anshu/arkbase/ng.php', 'Staphylococcus_aureus' => 'https://datascience.imtech.res.in/anshu/arkbase/Staphylococcus_aureus.php', 'Streptococcus_agalactiae' => '#', 'Streptococcus_pneumoniae' => '#', 'Streptococcus_pyogenes' => '#', 'Haemophilus_influenzae' => '#',
];
$pathogenNameToAliasMap = [
    'Acinetobacter_baumannii' => 'a_baumannii', 'Neisseria_gonorrhoeae' => 'n_gonorrhoeae', 'Shigella_sonnei' => 's_sonnei', 'Streptococcus_pyogenes' => 's_pyogenes', 'Streptococcus_pneumoniae' => 's_pneumoniae', 'Shigella_flexneri' => 's_flexneri', 'Salmonella_enterica' => 's_enterica', 'Staphylococcus_aureus' => 's_aureus', 'Streptococcus_agalactiae' => 's_agalactiae', 'Pseudomonas_aeruginosa' => 'p_aeruginosa', 'Klebsiella_pneumoniae' => 'k_pneumoniae', 'Haemophilus_influenzae' => 'h_influenzae', 'Enterococcus_faecium' => 'e_faecium', 'Escherichia_coli' => 'e_coli'
];

if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

// --- Configuration & Parameters ---
$recordsPerPageOptions = [10, 25, 50, 100];
$defaultRecordsPerPage = 25;
$table = isset($_GET['table']) && in_array($_GET['table'], ['ppi_central', 'ppi_2']) ? $_GET['table'] : 'ppi_central';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$recordsPerPage = isset($_GET['records']) && in_array(intval($_GET['records']), $recordsPerPageOptions) ? intval($_GET['records']) : $defaultRecordsPerPage;
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : '';
$sortDirection = isset($_GET['dir']) && strtolower($_GET['dir']) === 'desc' ? 'desc' : 'asc';
$isDownloadRequest = isset($_GET['download']) && $_GET['download'] === 'csv';

// --- TABLE CONFIGURATIONS ---
$tableConfigs = [
    'ppi_central' => [ 
        'name' => 'Centrality Metrics', 'description' => 'This table displays key centrality metrics for individual proteins. Select a pathogen to begin.', 
        'columns' => ['pathogen' => 'Pathogen', 'genome_accession'=>'Assembly Accession','protein1_id' => 'Protein ID','prot_desc'=>'Protein Description', 'bcq' => 'Protein Quartile (BW)', 'ccq' => 'Protein Quartile (Closeness)', 'dcq' => 'Degree Centrality Quartile', 'COG_category'=>'COG','cog_desc'=>'COG Description']
    ],
    'ppi_2' => [ 
        'name' => 'Co-Target Identification', 'description' => 'This table shows interacting protein pairs. Select a pathogen, then click "View Details" to explore potential drug targets.', 
        'columns' => ['pathogen' => 'Pathogen', 'protien1_id' => 'Protein ID 1','protien2_id' => 'Protein ID 2','prot_desc1'=>'Protein Description 1','prot_desc2'=>'Protein Description 2', 'bcq1' => 'Protein 1 Quartile (BW)', 'bcq2' => 'Protein 2 Quartile (BW)','COG_category1'=>'COG 1','COG_category2'=>'COG2','cog_desc1'=>'COG Description 1','cog_desc2'=>'COG Description 2', 'action' => 'More Information']
    ]
];
$currentTableConfig = $tableConfigs[$table];
$columns = array_keys($currentTableConfig['columns']);
if (!empty($sortColumn) && !in_array($sortColumn, $columns)) { $sortColumn = ''; }

// --- HELPER FUNCTIONS FOR FILTERS ---
function getUniqueValues($conn, $table, $column, $pathogenFilter = null, $limit = 5000) { $whereClauses = ["`".mysqli_real_escape_string($conn, $column)."` IS NOT NULL", "`".mysqli_real_escape_string($conn, $column)."` != ''"]; $params = []; $types = ''; if ($pathogenFilter && $column !== 'pathogen') { $whereClauses[] = "`pathogen` = ?"; $params[] = $pathogenFilter; $types .= 's'; } $sql = "SELECT DISTINCT `".mysqli_real_escape_string($conn, $column)."` FROM `".mysqli_real_escape_string($conn, $table)."` WHERE ".implode(' AND ', $whereClauses)." ORDER BY 1 LIMIT ".intval($limit); $stmt = mysqli_prepare($conn, $sql); if (!empty($params)) { mysqli_stmt_bind_param($stmt, $types, ...$params); } mysqli_stmt_execute($stmt); $result = mysqli_stmt_get_result($stmt); $values = []; if ($result) { while ($row = mysqli_fetch_assoc($result)) { $values[] = $row[$column]; } } return $values; }
function getUniqueFirstLetters($conn, $table, $column, $pathogenFilter = null) { $whereClauses = ["`".mysqli_real_escape_string($conn, $column)."` IS NOT NULL", "`".mysqli_real_escape_string($conn, $column)."` != ''"]; $params = []; $types = ''; if ($pathogenFilter) { $whereClauses[] = "`pathogen` = ?"; $params[] = $pathogenFilter; $types .= 's'; } $sql = "SELECT DISTINCT LEFT(`".mysqli_real_escape_string($conn, $column)."`, 1) as first_letter FROM `".mysqli_real_escape_string($conn, $table)."` WHERE ".implode(' AND ', $whereClauses)." ORDER BY first_letter"; $stmt = mysqli_prepare($conn, $sql); if (!empty($params)) { mysqli_stmt_bind_param($stmt, $types, ...$params); } mysqli_stmt_execute($stmt); $result = mysqli_stmt_get_result($stmt); $values = []; if ($result) { while ($row = mysqli_fetch_assoc($result)) { if(!empty(trim($row['first_letter']))) { $values[] = $row['first_letter']; } } } return $values; }
function buildUrl($newParams = []) { return '?' . http_build_query(array_merge($_GET, $newParams)); }

// --- Build Filter & SQL Query Conditions (used by both HTML and CSV) ---
$whereConditions = []; $filterParams = []; $types = '';
$selectedPathogen = $_GET['filter_pathogen'] ?? null;
$likeFilterColumns = ['COG_category', 'COG_category1', 'COG_category2']; // Columns for "starts with" filtering

foreach (array_keys($currentTableConfig['columns']) as $column) {
    if ($column === 'action') continue;
    if (isset($_GET['filter_' . $column]) && trim($_GET['filter_' . $column]) !== '') {
        $filterValue = trim($_GET['filter_' . $column]);
        if (in_array($column, $likeFilterColumns)) {
            $whereConditions[] = "`$column` LIKE ?";
            $filterParams[] = $filterValue . '%'; // Append wildcard for LIKE search
        } else {
            $whereConditions[] = "`$column` = ?";
            $filterParams[] = $filterValue;
        }
        $types .= 's';
    }
}
$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
if (!empty($sortColumn)) { $orderClause = "ORDER BY `$sortColumn` ".strtoupper($sortDirection); } else { if ($table === 'ppi_2') { $orderClause = "ORDER BY `bcq1` ASC, `bcq2` ASC"; } else { $orderClause = "ORDER BY `bcq` ASC, `ccq` ASC"; } }

// --- [CRITICAL CHANGE] HANDLE CSV DOWNLOAD REQUEST *BEFORE* ANY HTML OUTPUT ---
if ($isDownloadRequest) {
    // 1. Build the SQL query WITHOUT pagination (LIMIT/OFFSET)
    $downloadSql = "SELECT * FROM `$table` $whereClause $orderClause";
    $downloadStmt = mysqli_prepare($conn, $downloadSql);
    if (!empty($filterParams)) {
        mysqli_stmt_bind_param($downloadStmt, $types, ...$filterParams);
    }
    mysqli_stmt_execute($downloadStmt);
    $result = mysqli_stmt_get_result($downloadStmt);

    // 2. Set HTTP headers to trigger a file download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . htmlspecialchars($table) . '_export_' . date('Y-m-d') . '.csv"');

    // 3. Open the output stream and write the CSV data
    $output = fopen('php://output', 'w');

    // Get header columns from config, but exclude the 'action' column
    $headerColumns = $currentTableConfig['columns'];
    if (isset($headerColumns['action'])) {
        unset($headerColumns['action']);
    }
    fputcsv($output, array_values($headerColumns)); // Write the header row

    // Write the data rows
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Ensure only the required columns are written to the CSV
            $filteredRow = array_intersect_key($row, $headerColumns);
            fputcsv($output, $filteredRow);
        }
    }
    
    fclose($output);
    mysqli_close($conn); // Close the connection
    
    // 4. Terminate the script to prevent rendering the HTML page
    exit();
}

// --- If we reach here, it's a normal HTML page request. Now we can include headers and content. ---

// --- SQL Execution for HTML Display (with pagination) ---
$countSql = "SELECT COUNT(*) as total FROM `$table` $whereClause"; $countStmt = mysqli_prepare($conn, $countSql); if (!empty($filterParams)) { mysqli_stmt_bind_param($countStmt, $types, ...$filterParams); } mysqli_stmt_execute($countStmt); $totalRecords = mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))['total'];
$totalPages = $recordsPerPage > 0 ? ceil($totalRecords / $recordsPerPage) : 1; $offset = ($page - 1) * $recordsPerPage; $limitClause = "LIMIT $recordsPerPage OFFSET $offset";
$sql = "SELECT * FROM `$table` $whereClause $orderClause $limitClause"; $stmt = mysqli_prepare($conn, $sql); if (!empty($filterParams)) { mysqli_stmt_bind_param($stmt, $types, ...$filterParams); } mysqli_stmt_execute($stmt); $result = mysqli_stmt_get_result($stmt);

// --- START HTML OUTPUT ---
include 'header.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPI Data Management System</title>
    
    <style>
        body {font-family: Arial, sans-serif; background-color: #f4f7f6;}
        .table-container {background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 15px rgba(0,0,0,0.08);}
        .table-description {background-color: #e9f5fe; border-left: 5px solid #0d6efd; padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: 8px;}
        .sort-indicator::after {font-family: 'bootstrap-icons'; font-size: 1.1em; vertical-align: middle; margin-left: 5px;}
        th[data-sort-dir="asc"] .sort-indicator::after { content: "\f1e1"; }
        th[data-sort-dir="desc"] .sort-indicator::after { content: "\f1df"; }
        th.sortable:hover {cursor: pointer; background-color: #e9ecef;}
        .table-tab-nav .btn.btn-primary {background-color: white; border-color: #dee2e6 #dee2e6 white; color: #343a40; font-weight: 600; margin-bottom: -1px;}
        .filter-group {min-width: 180px;}
    </style>
</head>
<body>

<header class="text-center py-4 bg-white shadow-sm">
    <h1>Interactome Centrality & Co-Target Discovery</h1>
    <p class="lead mb-0">Browse, filter, and analyze protein interactivity data.</p>
</header>
<main class="container-fluid px-4 py-4">
    <div class="table-container">
        <div class="table-tab-nav border-bottom" role="group">
            <a href="?table=ppi_central" class="btn <?php echo $table === 'ppi_central' ? 'btn-primary' : 'btn-light'; ?>"><i class="bi bi-diagram-3-fill me-2"></i>Centrality Metrics</a>
            <a href="?table=ppi_2" class="btn <?php echo $table === 'ppi_2' ? 'btn-primary' : 'btn-light'; ?>"><i class="bi bi-link-45deg me-2"></i>Co-Target Identification</a>
        </div>
        <div class="table-description mt-4">
            <h5 class="mb-1"><?php echo htmlspecialchars($currentTableConfig['name']); ?></h5>
            <p class="mb-0"><?php echo htmlspecialchars($currentTableConfig['description']); ?></p>
        </div>
        <form method="GET" id="filterForm" class="mb-4">
            <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
            <input type="hidden" name="records" value="<?php echo $recordsPerPage; ?>">
            <div class="d-flex flex-nowrap align-items-end gap-3" style="overflow-x: auto; padding-bottom: 15px;">
                <?php
                // Define columns that need special handling
                $autofillColumns = ['prot_desc', 'cog_desc', 'prot_desc1', 'prot_desc2', 'cog_desc1', 'cog_desc2'];
                $firstLetterCogColumns = ['COG_category', 'COG_category1', 'COG_category2'];

                foreach ($currentTableConfig['columns'] as $column => $label):
                    if ($column === 'action') continue;
                    $currentFilterValue = $_GET['filter_' . $column] ?? '';
                ?>
                <div class="filter-group">
                    <label for="filter_<?php echo $column; ?>" class="form-label mb-1 fw-bold small"><?php echo htmlspecialchars($label); ?></label>

                    <?php if (in_array($column, $autofillColumns)): ?>
                        <input list="datalist_<?php echo $column; ?>" id="filter_<?php echo $column; ?>" name="filter_<?php echo $column; ?>" class="form-control form-control-sm" value="<?php echo htmlspecialchars($currentFilterValue); ?>" onchange="this.form.submit()" placeholder="Type to search..." <?php if ($column !== 'pathogen' && !$selectedPathogen) echo ' disabled'; ?>>
                        <datalist id="datalist_<?php echo $column; ?>">
                            <?php
                            if ($column === 'pathogen' || $selectedPathogen) {
                                $uniqueValues = getUniqueValues($conn, $table, $column, ($column !== 'pathogen' ? $selectedPathogen : null));
                                foreach ($uniqueValues as $value):
                                    echo '<option value="' . htmlspecialchars($value) . '">';
                                endforeach;
                            }
                            ?>
                        </datalist>

                    <?php elseif (in_array($column, $firstLetterCogColumns)): ?>
                        <select id="filter_<?php echo $column; ?>" name="filter_<?php echo $column; ?>" class="form-select form-select-sm" onchange="this.form.submit()" <?php if (!$selectedPathogen) echo ' disabled'; ?>>
                            <option value="">All</option>
                            <?php
                            if ($selectedPathogen) {
                               $uniqueLetters = getUniqueFirstLetters($conn, $table, $column, $selectedPathogen);
                               foreach ($uniqueLetters as $letter):
                            ?>
                            <option value="<?php echo htmlspecialchars($letter); ?>" <?php echo $currentFilterValue === $letter ? 'selected' : ''; ?>><?php echo htmlspecialchars($letter); ?></option>
                            <?php endforeach; } ?>
                        </select>

                    <?php else: // Standard dropdown for other columns ?>
                        <select id="filter_<?php echo $column; ?>" name="filter_<?php echo $column; ?>" class="form-select form-select-sm" onchange="this.form.submit()" <?php if ($column !== 'pathogen' && !$selectedPathogen) echo ' disabled'; ?>>
                            <option value=""><?php echo ($column === 'pathogen') ? 'Select Pathogen First' : 'All'; ?></option>
                            <?php
                            if ($column === 'pathogen' || $selectedPathogen) {
                               $uniqueValues = getUniqueValues($conn, $table, $column, ($column !== 'pathogen' ? $selectedPathogen : null));
                               foreach ($uniqueValues as $value):
                            ?>
                            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $currentFilterValue === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars(str_replace('_', ' ',$value)); ?></option>
                            <?php endforeach; } ?>
                        </select>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                <div class="d-flex align-items-end"><a href="?table=<?php echo htmlspecialchars($table); ?>" class="btn btn-secondary btn-sm ms-2"><i class="bi bi-arrow-clockwise"></i> Clear</a></div>
            </div>
        </form>

        <div class="row mb-3 align-items-center">
             <div class="col-md-5"><p class="mb-0 text-muted">Showing <strong><?php echo min($totalRecords, ($page - 1) * $recordsPerPage + 1); ?> - <?php echo min($totalRecords, $page * $recordsPerPage); ?></strong> of <strong><?php echo number_format($totalRecords); ?></strong> records</p></div>
            <div class="col-md-7 d-flex justify-content-end align-items-center gap-2">
                <label for="itemsPerPageSelect" class="form-label mb-0">Per page:</label>
                <select class="form-select form-select-sm w-auto" id="itemsPerPageSelect"><?php foreach ($recordsPerPageOptions as $option): ?><option value="<?php echo $option; ?>" <?php echo $option == $recordsPerPage ? 'selected' : ''; ?>><?php echo $option; ?></option><?php endforeach; ?></select>
                <button class="btn btn-success btn-sm" id="downloadCsvBtn"><i class="bi bi-download"></i> Download </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No.</th>
                        <?php foreach ($currentTableConfig['columns'] as $colKey => $colValue): ?>
                        <th class="sortable" data-column="<?php echo $colKey; ?>" data-sort-dir="<?php if($sortColumn === $colKey) echo $sortDirection; ?>">
                            <?php echo htmlspecialchars($colValue); ?>
                            <?php if ($sortColumn === $colKey): ?><span class="sort-indicator"></span><?php endif; ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalRecords > 0): $serialNumber = ($page - 1) * $recordsPerPage + 1; while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $serialNumber++; ?></td>
                            <?php foreach ($columns as $column): ?>
                            <td>
                                <?php 
                                if ($column === 'action') {
                                    $pathogenFromDb = $row['pathogen'];
                                    $pathogenAlias = $pathogenNameToAliasMap[$pathogenFromDb] ?? $pathogenFromDb;
                                    $p1 = urlencode($row['protien1_id']);
                                    $p2 = urlencode($row['protien2_id']);
                                    $pathogen = urlencode($pathogenAlias);
                                    $url = "drug_target_1.php?pathogen={$pathogen}&protein_id1={$p1}&protein_id2={$p2}";
                                    echo "<a href='{$url}' class='btn btn-primary btn-sm' title='View drug targets for this pair'><i class='bi bi-eyedropper'></i> View Details</a>";
                                } elseif ($column === 'pathogen') {
                                    $pathogenName = $row[$column];
                                    $pathogenDisplay = str_replace('_', ' ', $pathogenName);
                                    $link = $pathogenLinks[$pathogenName] ?? '#';
                                    echo "<i>".($link !== '#' ? "<a href='{$link}' target='_blank' rel='noopener noreferrer'>".htmlspecialchars($pathogenDisplay)."</a>" : htmlspecialchars($pathogenDisplay))."</i>";
                                } elseif (in_array($column, ['protien1_id', 'protien2_id', 'protein1_id'])) {
                                    $proteinId = htmlspecialchars($row[$column] ?? '');
                                    echo !empty($proteinId) ? "<a href='https://www.ncbi.nlm.nih.gov/protein/{$proteinId}' target='_blank' rel='noopener noreferrer'>{$proteinId}</a>" : 'N/A';
                                } else { echo htmlspecialchars($row[$column] ?? 'N/A'); }
                                ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="<?php echo count($columns) + 1; ?>" class="text-center text-muted p-4"><h5><?php echo $selectedPathogen ? 'No records found' : 'Please select a pathogen to view data.'; ?></h5></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => 1]); ?>">First</a></li>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => $page - 1]); ?>">Previous</a></li>
                <?php endif; ?>
                <?php $startPage = max(1, $page - 2); $endPage = min($totalPages, $page + 2); for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="<?php echo buildUrl(['page' => $i]); ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => $page + 1]); ?>">Next</a></li>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => $totalPages]); ?>">Last</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</main>


<?php mysqli_close($conn); ?>

<script>
function setUrlParam(key, value, newDir = '') {
    const url = new URL(window.location);
    url.searchParams.set(key, value);
    if (key === 'sort') {
        url.searchParams.set('dir', newDir);
    }
    if (key !== 'page') {
        url.searchParams.set('page', '1');
    }
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('itemsPerPageSelect').addEventListener('change', (e) => setUrlParam('records', e.target.value));
    
    document.getElementById('downloadCsvBtn').addEventListener('click', () => {
        const url = new URL(window.location);
        url.searchParams.set('download', 'csv');
        // We remove pagination params as we want the full dataset
        url.searchParams.delete('page');
        url.searchParams.delete('records');
        window.location.href = url.toString(); // Navigate to the download URL
    });

    document.querySelectorAll('th.sortable').forEach(th => {
        th.addEventListener('click', () => {
            const column = th.dataset.column;
            const currentDir = th.dataset.sortDir;
            const newDir = (currentDir === 'asc') ? 'desc' : 'asc';
            setUrlParam('sort', column, newDir);
        });
    });
});
</script>

</body>
</html>
<?php  include('footer.php'); ?>