<?php
include 'conn.php';

$pathogenLinks = [
    'Acinetobacter baumannii' => 'https://datascience.imtech.res.in/anshu/arkbase/ab1.php',
    'Klebsiella pneumoniae' => 'https://datascience.imtech.res.in/anshu/arkbase/kp.php',
    'Escherichia coli' => 'https://datascience.imtech.res.in/anshu/arkbase/Ecoli.php',
    'Shigella flexneri' => 'https://datascience.imtech.res.in/anshu/arkbase/Shigella_flexneri.php',
    'Shigella sonnei' => 'https://datascience.imtech.res.in/anshu/arkbase/Shigella_sonnei.php',
    'Enterococcus faecium' => 'https://datascience.imtech.res.in/anshu/arkbase/ef.php',
    'Pseudomonas aeruginosa' => 'https://datascience.imtech.res.in/anshu/arkbase/pa.php',
    'Salmonella enterica' => 'https://datascience.imtech.res.in/anshu/arkbase/se.php',
    'Neisseria gonorrhoeae' => 'https://datascience.imtech.res.in/anshu/arkbase/ng.php',
    'Staphylococcus aureus' => 'https://datascience.imtech.res.in/anshu/arkbase/Staphylococcus_aureus.php',
    'Streptococcus agalactiae' => 'https://datascience.imtech.res.in/anshu/arkbase/sa.php',
    'Streptococcus pneumoniae' => 'https://datascience.imtech.res.in/anshu/arkbase/Streptococcus_pneumoniae.php',
    'Streptococcus pyogenes' => 'https://datascience.imtech.res.in/anshu/arkbase/Streptococcus_pyogenes.php',
    'Haemophilus influenzae' => 'https://datascience.imtech.res.in/anshu/arkbase/hi.php',
];

$records_per_page = 25;
$page = isset($_POST['page']) && is_numeric($_POST['page']) ? (int)$_POST['page'] : 1;
$offset = ($page - 1) * $records_per_page;
$pathogen = isset($_POST['pathogen']) ? $_POST['pathogen'] : '';
$table = isset($_POST['table']) ? $_POST['table'] : 'Pan_Genome_Curated';
$antibiotic = isset($_POST['antibiotic']) ? $_POST['antibiotic'] : '';
$phenotype = isset($_POST['phenotype']) ? $_POST['phenotype'] : '';

if (empty($pathogen) || empty($table)) {
    die(json_encode(['error' => 'Pathogen or table not specified.']));
}

$sql_from_where = "FROM " . $conn->real_escape_string($table) . " WHERE Pathogen = ?";
$param_types = 's';
$param_values = [$pathogen];

if (!empty($antibiotic) && !empty($phenotype)) {
    $id_sql = "SELECT DISTINCT Assembly_Accession FROM Genome_section WHERE Pathogen = ? AND Antibiotic = ? AND Phenotype = ?";
    $stmt_ids = $conn->prepare($id_sql);
    $stmt_ids->bind_param("sss", $pathogen, $antibiotic, $phenotype);
    $stmt_ids->execute();
    $result_ids = $stmt_ids->get_result();
    $assembly_ids = [];
    while ($row = $result_ids->fetch_assoc()) { $assembly_ids[] = $row['Assembly_Accession']; }
    $stmt_ids->close();

    if (empty($assembly_ids)) {
        echo json_encode(['table' => '<p>No matching genome data found for the selected filters.</p>', 'pagination' => '', 'hits' => 0]);
        $conn->close();
        exit;
    }
    
    $placeholders = implode(',', array_fill(0, count($assembly_ids), '?'));
    $sql_from_where .= " AND Assembly_Accession IN ($placeholders)";
    $param_types .= str_repeat('s', count($assembly_ids));
    $param_values = array_merge($param_values, $assembly_ids);
}

$count_sql = "SELECT COUNT(*) as total " . $sql_from_where;
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param($param_types, ...$param_values);
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
$count_stmt->close();

$data_sql = "SELECT * " . $sql_from_where . " ORDER BY Assembly_Accession LIMIT ? OFFSET ?";
$data_param_types = $param_types . 'ii';
$data_param_values = $param_values;
$data_param_values[] = $records_per_page;
$data_param_values[] = $offset;

$data_stmt = $conn->prepare($data_sql);
$data_stmt->bind_param($data_param_types, ...$data_param_values);
$data_stmt->execute();
$result = $data_stmt->get_result();

$table_html = '';
if ($result->num_rows > 0) {
    $table_html .= '<table class="table table-striped table-bordered table-hover">';
    $table_html .= '<thead class="table-dark"><tr>';
    $table_html .= '<th>#</th>';
    $fields = $result->fetch_fields();
    foreach ($fields as $field) {
        $table_html .= '<th>' . htmlspecialchars($field->name) . '</th>';
    }
    $table_html .= '</tr></thead>';
    $table_html .= '<tbody>';

    $row_number = ($page - 1) * $records_per_page + 1;
    while ($row = $result->fetch_assoc()) {
        $table_html .= '<tr>';
        $table_html .= '<td>' . $row_number . '</td>';
        
        foreach ($row as $column_name => $cell_value) {
            if ($column_name == 'Pathogen' && isset($pathogenLinks[$cell_value])) {
                $link = $pathogenLinks[$cell_value];
                $table_html .= '<td><a href="' . htmlspecialchars($link) . '" target="_blank" title="View Reference Genome for ' . htmlspecialchars($cell_value) . '"><em>' . htmlspecialchars($cell_value) . '</em></a></td>';
            } else {
                $table_html .= '<td>' . htmlspecialchars($cell_value) . '</td>';
            }
        }
        $table_html .= '</tr>';
        $row_number++;
    }
    
    $table_html .= '</tbody></table>';
} else {
    $table_html = '<p>No data available for the selected criteria.</p>';
}
$data_stmt->close();

$pagination_html = '';
if ($total_pages > 1) {
    $pagination_html .= '<nav><ul class="pagination justify-content-center">';
    $prev_disabled = ($page <= 1) ? "disabled" : "";
    $pagination_html .= "<li class='page-item $prev_disabled'><a class='page-link' href='#' data-page='" . ($page - 1) . "'>Previous</a></li>";
    $window = 2;
    if ($total_pages > ($window * 2 + 3)) {
        $pagination_html .= "<li class='page-item " . ($page == 1 ? 'active' : '') . "'><a class='page-link' href='#' data-page='1'>1</a></li>";
        if ($page > $window + 2) { $pagination_html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>"; }
        $start = max(2, $page - $window);
        $end = min($total_pages - 1, $page + $window);
        for ($i = $start; $i <= $end; $i++) { $pagination_html .= "<li class='page-item " . ($page == $i ? 'active' : '') . "'><a class='page-link' href='#' data-page='$i'>$i</a></li>"; }
        if ($page < $total_pages - $window - 1) { $pagination_html .= "<li class='page-item disabled'><span class='page-link'>...</span></li>"; }
        $pagination_html .= "<li class='page-item " . ($page == $total_pages ? 'active' : '') . "'><a class='page-link' href='#' data-page='$total_pages'>$total_pages</a></li>";
    } else {
        for ($i = 1; $i <= $total_pages; $i++) { $pagination_html .= "<li class='page-item " . ($page == $i ? 'active' : '') . "'><a class='page-link' href='#' data-page='$i'>$i</a></li>"; }
    }
    $next_disabled = ($page >= $total_pages) ? "disabled" : "";
    $pagination_html .= "<li class='page-item $next_disabled'><a class='page-link' href='#' data-page='" . ($page + 1) . "'>Next</a></li>";
    $pagination_html .= '</ul></nav>';
}

$conn->close();

echo json_encode([
    'table' => $table_html, 
    'pagination' => $pagination_html, 
    'hits' => $total_records
]);
?>