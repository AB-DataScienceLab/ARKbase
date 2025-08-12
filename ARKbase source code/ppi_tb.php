<?php
include 'conn.php'; // Make sure this path is correct

// API Endpoint for dynamic dropdowns
if (isset($_GET['action']) && $_GET['action'] === 'get_proteins_for_pathogen') {
    header('Content-Type: application/json');
    $pathogen = $_GET['pathogen'] ?? '';
    $table = $_GET['table'] ?? 'ppi_central';
    $response = ['protein1' => [], 'protein2' => []];

    if (!$conn || empty($pathogen) || !in_array($table, ['ppi_central', 'ppi_2'])) {
        echo json_encode($response);
        exit();
    }

    $whereClause = "WHERE pathogen = ?";
    
    if ($table === 'ppi_central') {
        $sql = "SELECT DISTINCT protien1_id FROM ppi_central $whereClause ORDER BY 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $pathogen);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $response['protein1'][] = $row['protien1_id'];
        }
    } 
    elseif ($table === 'ppi_2') {
        $sql1 = "SELECT DISTINCT protien1_id FROM ppi_2 $whereClause ORDER BY 1";
        $stmt1 = mysqli_prepare($conn, $sql1);
        mysqli_stmt_bind_param($stmt1, 's', $pathogen);
        mysqli_stmt_execute($stmt1);
        $result1 = mysqli_stmt_get_result($stmt1);
        while ($row = mysqli_fetch_assoc($result1)) {
            $response['protein1'][] = $row['protien1_id'];
        }

        $sql2 = "SELECT DISTINCT protien2_id FROM ppi_2 $whereClause ORDER BY 1";
        $stmt2 = mysqli_prepare($conn, $sql2);
        mysqli_stmt_bind_param($stmt2, 's', $pathogen);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);
        while ($row = mysqli_fetch_assoc($result2)) {
            $response['protein2'][] = $row['protien2_id'];
        }
    }

    mysqli_close($conn);
    echo json_encode($response);
    exit();
}

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- Configuration ---
$recordsPerPageOptions = [10, 25, 50, 100];
$defaultRecordsPerPage = 25;

// --- Get and Sanitize Parameters ---
$table = isset($_GET['table']) && in_array($_GET['table'], ['ppi_central', 'ppi_2']) ? $_GET['table'] : 'ppi_central';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$recordsPerPage = isset($_GET['records']) && in_array(intval($_GET['records']), $recordsPerPageOptions)
    ? intval($_GET['records']) : $defaultRecordsPerPage;
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : '';
$sortDirection = isset($_GET['dir']) && strtolower($_GET['dir']) === 'desc' ? 'desc' : 'asc';
$isDownloadRequest = isset($_GET['download']) && $_GET['download'] === 'csv';

// --- Table Configurations ---
$tableConfigs = [
    'ppi_central' => [
        'name' => 'Centrality Metrics',
        'description' => 'This table displays key centrality metrics for individual proteins, helping to identify influential nodes within the interactome.',
        'columns' => [
            'pathogen' => 'Pathogen',
            'protein1_id' => 'Protein ID',
           
            'bcq' => 'Betweenness Centrality Quartile',
             'dcq' => 'Degree Centrality Quartile',
            'ccq' => 'Closeness Centrality Quartile',
            'COG_category'=>'COG Category'
        ]
    ],
    'ppi_2' => [
        'name' => 'Co-Target Identification',
        'description' => 'This selection shows interacting protein pairs where both proteins belong to <strong>Betweenness Centrality Quartile 1 (Q1)</strong>, indicating strong co-target potential.',
        'columns' => [
            'pathogen' => 'Pathogen',
            'protien1_id' => 'Protein ID 1',
            'protien2_id' => 'Protein ID 2',
            'bcq1' => 'Protein 1 Betweenness Centrality',
            'bcq2' => 'Protein 2 Betweenness Centrality',
        ]
    ]
];

$currentTableConfig = $tableConfigs[$table];
$columns = array_keys($currentTableConfig['columns']);

if (!empty($sortColumn) && !in_array($sortColumn, $columns)) {
    $sortColumn = '';
}

// --- Helper Functions ---
function getUniqueValues($conn, $table, $column, $pathogenFilter = null, $limit = 1000) {
    $whereClauses = ["`" . mysqli_real_escape_string($conn, $column) . "` IS NOT NULL", "`" . mysqli_real_escape_string($conn, $column) . "` != ''"];
    $params = [];
    $types = '';

    if ($pathogenFilter) {
        $whereClauses[] = "`pathogen` = ?";
        $params[] = $pathogenFilter;
        $types .= 's';
    }

    $sql = "SELECT DISTINCT `" . mysqli_real_escape_string($conn, $column) . "` FROM `" . mysqli_real_escape_string($conn, $table) . "` WHERE " . implode(' AND ', $whereClauses) . " ORDER BY 1 LIMIT " . intval($limit);
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $values = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $values[] = $row[$column];
        }
    }
    return $values;
}

function buildUrl($newParams = []) {
    $params = array_merge($_GET, $newParams);
    return '?' . http_build_query($params);
}

// --- Build Filter Conditions ---
$whereConditions = [];
$filterParams = [];
$types = '';

foreach ($columns as $column) {
    if (isset($_GET['filter_' . $column]) && !empty(trim($_GET['filter_' . $column]))) {
        $filterValue = trim($_GET['filter_' . $column]);
        $whereConditions[] = "`$column` = ?";
        $filterParams[] = $filterValue;
        $types .= 's';
    }
}
$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// --- Handle CSV Download Request ---
if ($isDownloadRequest) {
    $downloadSql = "SELECT * FROM `$table` $whereClause";
    if (!empty($sortColumn)) {
        $downloadSql .= " ORDER BY `$sortColumn` " . strtoupper($sortDirection);
    }
    
    $stmt = mysqli_prepare($conn, $downloadSql);
    if (!empty($filterParams)) {
        mysqli_stmt_bind_param($stmt, $types, ...$filterParams);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $table . '_export_' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, $currentTableConfig['columns']);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $orderedRow = [];
        foreach($columns as $colKey) {
            $orderedRow[] = $row[$colKey] ?? 'N/A';
        }
        fputcsv($output, $orderedRow);
    }
    
    fclose($output);
    mysqli_close($conn);
    exit();
}

// --- Database Query for Display ---
$countSql = "SELECT COUNT(*) as total FROM `$table` $whereClause";
$countStmt = mysqli_prepare($conn, $countSql);
if (!empty($filterParams)) {
    mysqli_stmt_bind_param($countStmt, $types, ...$filterParams);
}
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = $recordsPerPage > 0 ? ceil($totalRecords / $recordsPerPage) : 1;

$offset = ($page - 1) * $recordsPerPage;
$limitClause = "LIMIT $recordsPerPage OFFSET $offset";
$orderClause = !empty($sortColumn) ? "ORDER BY `$sortColumn` " . strtoupper($sortDirection) : '';

$sql = "SELECT * FROM `$table` $whereClause $orderClause $limitClause";
$stmt = mysqli_prepare($conn, $sql);
if (!empty($filterParams)) {
    mysqli_stmt_bind_param($stmt, $types, ...$filterParams);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPI Data Management System</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body { font-family: 'Arial'; background-color: #f4f7f6; }
        .header { background: #ffffff; color: black; padding: 1rem 2rem; margin-bottom: 2rem; }
        .header h1 { font-weight: 300; }
        .table-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 15px rgba(0,0,0,0.08); }
        .table-description { background-color: #e9f5fe; border-left: 5px solid #0d6efd; padding: 1rem 1.25rem; margin-bottom: 1.5rem; border-radius: 8px; font-size: 1rem; }
        .filter-group label { font-weight: 600; color: #333; font-size: 0.9em; margin-bottom: 0.5rem; }
        .sort-indicator::after { font-family: 'bootstrap-icons'; font-size: 1.1em; vertical-align: middle; margin-left: 5px; }
        th[data-sort-dir="asc"] .sort-indicator::after { content: "\f1e1"; }
        th[data-sort-dir="desc"] .sort-indicator::after { content: "\f1df"; }
        th.sortable:hover { cursor: pointer; background-color: #343a40; }
        select:disabled { background-color: #e9ecef; cursor: not-allowed; }

        /* Style for pathogen data in table cells */
        .pathogen-data { font-style: italic; }

        /* --- CSS for Tab-Style Navigation --- */
        .table-tab-nav {
            border-bottom: 2px solid #dee2e6; /* A standard light gray border */
            margin-bottom: 0; /* Align with the table description */
        }
        .table-tab-nav .btn {
            border: 2px solid transparent;
            border-bottom: none; /* No bottom border on any tab button */
            margin-bottom: -2px; /* Crucial for making the active tab overlap the container's border */
            border-radius: 0.5rem 0.5rem 0 0; /* Rounded top corners */
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            background-color: #f8f9fa; /* Light background for inactive tabs */
            color: #0d6efd; /* Blue text for inactive tabs */
            border-color: #dee2e6;
        }
        .table-tab-nav .btn:hover {
            background-color: #e9ecef; /* Slightly darker on hover */
        }
        /* Style for the ACTIVE tab */
        .table-tab-nav .btn.btn-primary {
            background-color: white; /* Match the table container background */
            border-color: #dee2e6 #dee2e6 white; /* Border on left, top, right, but NOT bottom */
            color: #343a40; /* Darker text for the active tab */
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<header class="header">
    <div class="container text-center">
        <h1>Interactome Centrality & Co-Target Discovery</h1>
        <p class="lead mb-0">Browse, filter, and analyze protein interactivity data.</p>
    </div>
</header>

<div class="container-fluid px-4">
    <div class="table-container">

        <div class="table-tab-nav" role="group">
            <a href="<?php echo buildUrl(['table' => 'ppi_central', 'page' => 1]); ?>" class="btn <?php echo $table === 'ppi_central' ? 'btn-primary' : ''; ?>">
                <i class="bi bi-diagram-3-fill me-2"></i>Centrality Metrics
            </a>
            <a href="<?php echo buildUrl(['table' => 'ppi_2', 'page' => 1]); ?>" class="btn <?php echo $table === 'ppi_2' ? 'btn-primary' : ''; ?>">
                <i class="bi bi-link-45deg me-2"></i>Co-Target Identification
            </a>
        </div>

        <div class="table-description mt-4">
            <h5 class="mb-1"><?php echo htmlspecialchars($currentTableConfig['name']); ?></h5>
            <p class="mb-0"><?php echo $currentTableConfig['description']; ?></p>
        </div>

        <form method="GET" id="filterForm">
            <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
            <input type="hidden" name="records" value="<?php echo $recordsPerPage; ?>">
            
            <div class="row g-3 mb-4">
                <?php $selectedPathogen = $_GET['filter_pathogen'] ?? null; ?>
                <?php foreach ($columns as $column): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="filter-group">
                        <label for="filter_<?php echo $column; ?>" class="form-label"><?php echo htmlspecialchars($currentTableConfig['columns'][$column]); ?></label>
                        <select id="filter_<?php echo $column; ?>" name="filter_<?php echo $column; ?>" class="form-select" onchange="this.form.submit()">
                            <option value="">All</option>
                            <?php 
                            $currentFilterValue = $_GET['filter_' . $column] ?? '';
                            $uniqueValues = [];
                            if ($column === 'pathogen') {
                                $uniqueValues = getUniqueValues($conn, $table, $column);
                            } elseif (in_array($column, ['protien1_id', 'protien2_id'])) {
                                $uniqueValues = getUniqueValues($conn, $table, $column, $selectedPathogen);
                            } else {
                                $uniqueValues = getUniqueValues($conn, $table, $column);
                            }
                            foreach ($uniqueValues as $value): 
                            ?>
                            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $currentFilterValue === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($value); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="d-flex justify-content-center gap-2 mb-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill"></i> Apply Filters</button>
                <a href="?table=<?php echo htmlspecialchars($table); ?>" class="btn btn-secondary"><i class="bi bi-arrow-clockwise"></i> Clear Filters</a>
            </div>
        </form>

        <div class="row mb-3 align-items-center">
            <div class="col-md-6"><p class="mb-0 text-muted">Showing <strong><?php echo min($totalRecords, ($page - 1) * $recordsPerPage + 1); ?> - <?php echo min($totalRecords, $page * $recordsPerPage); ?></strong> of <strong><?php echo number_format($totalRecords); ?></strong> records</p></div>
            <div class="col-md-6 d-flex justify-content-end align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label for="itemsPerPageSelect" class="form-label mb-0">Per page:</label>
                    <select class="form-select form-select-sm w-auto" id="itemsPerPageSelect" onchange="changeRecordsPerPage(this.value)">
                        <?php foreach ($recordsPerPageOptions as $option): ?><option value="<?php echo $option; ?>" <?php echo $option == $recordsPerPage ? 'selected' : ''; ?>><?php echo $option; ?></option><?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-success btn-sm" onclick="downloadCsv()"><i class="bi bi-download"></i> Download CSV</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No.</th>
                        <?php foreach ($columns as $column): ?>
                        <th class="sortable" onclick="sortTable('<?php echo $column; ?>')" data-sort-dir="<?php echo ($sortColumn === $column) ? $sortDirection : ''; ?>">
                            <?php echo htmlspecialchars($currentTableConfig['columns'][$column]); ?>
                            <?php if ($sortColumn === $column): ?><span class="sort-indicator"></span><?php endif; ?>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalRecords > 0): ?>
                        <?php $serialNumber = ($page - 1) * $recordsPerPage + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $serialNumber++; ?></td>
                            <?php foreach ($columns as $column): ?>
                            <td<?php echo $column === 'pathogen' ? ' class="pathogen-data"' : ''; ?>>
                                <?php echo htmlspecialchars($row[$column] ?? 'N/A'); ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?php echo count($columns) + 1; ?>" class="text-center text-muted p-4"><h5>No records found</h5><p>Try adjusting your filters or search criteria.</p></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => 1]); ?>">First</a></li>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => $page - 1]); ?>">Previous</a></li>
                <?php endif; ?>
                <?php
                $startPage = max(1, $page - 2); $endPage = min($totalPages, $page + 2);
                for ($i = $startPage; $i <= $endPage; $i++):
                ?><li class="page-item <?php echo $i === $page ? 'active' : ''; ?>"><a class="page-link" href="<?php echo buildUrl(['page' => $i]); ?>"><?php echo $i; ?></a></li><?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => $page + 1]); ?>">Next</a></li>
                    <li class="page-item"><a class="page-link" href="<?php echo buildUrl(['page' => $totalPages]); ?>">Last</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php 
mysqli_close($conn);
include 'footer.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pathogenSelect = document.getElementById('filter_pathogen');
    if (pathogenSelect) {
        pathogenSelect.setAttribute('onchange', 'handlePathogenChange(this)');
    }
});

function handlePathogenChange(selectElement) {
    const selectedPathogen = selectElement.value;
    const currentTable = '<?php echo $table; ?>';
    
    let proteinDropdowns = [];
    if (currentTable === 'ppi_central') {
        proteinDropdowns.push(document.getElementById('filter_protien1_id'));
    } else if (currentTable === 'ppi_2') {
        proteinDropdowns.push(document.getElementById('filter_protien1_id'));
        proteinDropdowns.push(document.getElementById('filter_protien2_id'));
    }

    if (!selectedPathogen) {
        document.getElementById('filterForm').submit();
        return;
    }

    proteinDropdowns.forEach(dropdown => { if (dropdown) dropdown.disabled = true; });

    fetch(`?action=get_proteins_for_pathogen&pathogen=${encodeURIComponent(selectedPathogen)}&table=${currentTable}`)
        .then(response => response.json())
        .then(data => {
            const protein1Select = document.getElementById('filter_protien1_id');
            if (protein1Select) { updateDropdown(protein1Select, data.protein1 || []); }
            
            const protein2Select = document.getElementById('filter_protien2_id');
            if (protein2Select) { updateDropdown(protein2Select, data.protein2 || []); }
        })
        .catch(error => console.error('Error fetching protein data:', error))
        .finally(() => {
            proteinDropdowns.forEach(dropdown => { if (dropdown) dropdown.disabled = false; });
        });
}

function updateDropdown(selectElement, options) {
    selectElement.innerHTML = '<option value="">All</option>';
    options.forEach(optionValue => {
        const option = document.createElement('option');
        option.value = optionValue;
        option.textContent = optionValue;
        selectElement.appendChild(option);
    });
}

function sortTable(column) {
    const currentUrl = new URL(window.location);
    const currentSort = currentUrl.searchParams.get('sort');
    const currentDir = currentUrl.searchParams.get('dir');
    let newDir = 'asc';
    if (currentSort === column && currentDir === 'asc') { newDir = 'desc'; }
    currentUrl.searchParams.set('sort', column);
    currentUrl.searchParams.set('dir', newDir);
    currentUrl.searchParams.set('page', '1');
    window.location.href = currentUrl.toString();
}

function changeRecordsPerPage(newSize) {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('records', newSize);
    currentUrl.searchParams.set('page', '1'); 
    window.location.href = currentUrl.toString();
}

function downloadCsv() {
    const currentUrl = new URL(window.location);
    currentUrl.searchParams.set('download', 'csv');
    window.open(currentUrl.toString(), '_blank');
}
</script>

</body>
</html>