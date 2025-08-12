<?php
// Include the header and database connection
include 'header.php';
include 'conn.php'; // This file should establish the $conn mysqli object

// --- CONFIGURATION & SETUP ---

// Master list of pathogen short keys and their full display names.
$pathogen_aliases = [
    'a_baumannii' => 'Acinetobacter baumannii', 'e_faecium' => 'Enterococcus faecium', 'e_coli' => 'Escherichia coli', 'h_influenzae' => 'Haemophilus influenzae', 'k_pneumoniae' => 'Klebsiella pneumoniae', 'n_gonorrhoeae' => 'Neisseria gonorrhoeae', 'p_aeruginosa' => 'Pseudomonas aeruginosa', 's_enterica' => 'Salmonella enterica', 's_flexneri' => 'Shigella flexneri', 's_sonnei' => 'Shigella sonnei', 's_aureus' => 'Staphylococcus aureus', 's_agalactiae' => 'Streptococcus agalactiae', 's_pneumoniae' => 'Streptococcus pneumoniae', 's_pyogenes' => 'Streptococcus pyogenes'
];
asort($pathogen_aliases); // Sort them alphabetically for the dropdown

// Defines the exact pathogen name format for each database table.
$table_pathogen_mapping = [
    'Pan_virulence' => [ 'a_baumannii' => 'Acinetobacter baumannii', 'n_gonorrhoeae' => 'Neisseria gonorrhoeae', 's_sonnei' => 'Shigella sonnei', 's_pyogenes' => 'Streptococcus pyogenes', 's_pneumoniae' => 'Streptococcus pneumoniae', 's_flexneri' => 'Shigella flexneri', 's_enterica' => 'Salmonella enterica', 's_aureus' => 'Staphylococcus aureus', 's_agalactiae' => 'Streptococcus agalactiae', 'p_aeruginosa' => 'Pseudomonas aeruginosa', 'k_pneumoniae' => 'Klebsiella pneumoniae', 'h_influenzae' => 'Haemophilus influenzae', 'e_faecium' => 'Enterococcus faecium', 'e_coli' => 'Escherichia coli' ],
    'ppi_2' => [ 'a_baumannii' => 'Acinetobacter_baumannii', 'n_gonorrhoeae' => 'Neisseria_gonorrhoeae', 's_sonnei' => 'Shigella_sonnei', 's_pyogenes' => 'Streptococcus_pyogenes', 's_pneumoniae' => 'Streptococcus_pneumoniae', 's_flexneri' => 'Shigella_flexneri', 's_enterica' => 'Salmonella_enterica', 's_aureus' => 'Staphylococcus_aureus', 's_agalactiae' => 'Streptococcus_agalactiae', 'p_aeruginosa' => 'Pseudomonas_aeruginosa', 'k_pneumoniae' => 'Klebsiella_pneumoniae', 'h_influenzae' => 'Haemophilus_influenzae', 'e_faecium' => 'Enterococcus_faecium', 'e_coli' => 'Escherichia_coli' ],
    'ppi_central' => [ 'a_baumannii' => 'Acinetobacter_baumannii', 'n_gonorrhoeae' => 'Neisseria_gonorrhoeae', 's_sonnei' => 'Shigella_sonnei', 's_pyogenes' => 'Streptococcus_pyogenes', 's_pneumoniae' => 'Streptococcus_pneumoniae', 's_flexneri' => 'Shigella_flexneri', 's_enterica' => 'Salmonella_enterica', 's_aureus' => 'Staphylococcus_aureus', 's_agalactiae' => 'Streptococcus_agalactiae', 'p_aeruginosa' => 'Pseudomonas_aeruginosa', 'k_pneumoniae' => 'Klebsiella_pneumoniae', 'h_influenzae' => 'Haemophilus_influenzae', 'e_faecium' => 'Enterococcus_faecium', 'e_coli' => 'Escherichia_coli' ],
    'HPI_data' => [ 'e_coli' => 'Escherichia coli', 'k_pneumoniae' => 'Klebsiella_pneumoniae', 's_flexneri' => 'Shigella_flexneri', 'p_aeruginosa' => 'Pseudomonas_aeruginosa', 's_enterica' => 'Salmonella', 'n_gonorrhoeae' => 'Neisseria_gonorrhoeae', 's_aureus' => 'Staphylococcus_aureus', 's_pyogenes' => 'Streptococcus_pyogenes', 'h_influenzae' => 'Haemophilus_influenzae', 's_agalactiae' => 'Streptococcus_agalactiae' ],
    'drug_target' => [ 'a_baumannii' => 'a_baumannii', 'n_gonorrhoeae' => 'n_gonorrhoeae', 's_sonnei' => 's_sonnei', 's_pyogenes' => 's_pyogenes', 's_pneumoniae' => 's_pneumoniae', 's_flexneri' => 's_flexneri', 's_enterica' => 's_enterica', 's_aureus' => 's_aureus', 's_agalactiae' => 's_agalactiae', 'p_aeruginosa' => 'p_aeruginosa', 'k_pneumoniae' => 'k_pneumoniae', 'h_influenzae' => 'h_influenzae', 'e_faecium' => 'e_faecium', 'e_coli' => 'e_coli' ],
    'dti_ab' => [ 'e_coli' => 'Escherichia_coli', 's_flexneri' => 'Shigella_flexneri', 's_pneumoniae' => 'Streptococcus_pneumoniae', 's_aureus' => 'Staphylococcus_aureus', 's_pyogenes' => 'Streptococcus_pyogenes', 'p_aeruginosa' => 'Pseudomonas_aeruginosa', 'h_influenzae' => 'Haemophilus_influenzae', 's_enterica' => 'Salmonella' ],
    'operon_AD' => [ 's_agalactiae' => 's_agalactiae', 's_pneumoniae' => 's_pneumonia', 's_sonnei' => 's_sonnei', 's_flexneri' => 's_flexneri', 's_pyogenes' => 's_pyogenes', 'e_coli' => 'e_coli', 'e_faecium' => 'e_faecium', 'a_baumannii' => 'a_baumannii', 'n_gonorrhoeae' => 'n_gonorrhoeae', 'k_pneumoniae' => 'k_pneumoniae', 'h_influenzae' => 'h_influenza', 'p_aeruginosa' => 'p_aeruginosa', 's_enterica' => 's_enterica', 's_aureus' => 's_aureus' ],
    'RNA_seq' => [ 'p_aeruginosa' => 'Pseudomonas aeruginosa', 'e_coli' => 'Escherichia coli', 's_enterica' => 'Salmonella enterica', 's_pneumoniae' => 'Streptococcus pneumoniae', 'k_pneumoniae' => 'Klebsiella pneumoniae', 'a_baumannii' => 'Acinetobacter baumannii', 's_aureus' => 'Staphylococcus aureus', 'n_gonorrhoeae' => 'Neisseria gonorrhoeae' ]
];

// Defines display names and other configs for each table.
$table_configs = [
    'Pan_virulence' => ['display_name' => 'Pan-genome Virulence', 'columns' => ['Pathogen', 'prot_id', 'Subject_Protein_Name', 'VF_Category', 'essential', 'core', 'amr'], 'key_column' => 'Pathogen'],
    'ppi_2' => ['display_name' => 'Protein-Protein Interactions (Pairs)', 'columns' => ['pathogen', 'protien1', 'protien2', 'cs', 'pa_ana_p1', 'pa_ana_p2'], 'key_column' => 'pathogen'],
    'ppi_central' => ['display_name' => 'Protein-Protein Interactions (Centrality)', 'columns' => ['pathogen', 'protien', 'dc', 'bc', 'cc', 'pangenome'], 'key_column' => 'pathogen'],
    'HPI_data' => ['display_name' => 'Host-Pathogen Interactions', 'columns' => ['Pathogen_Name', 'Host_protein', 'Pathogen_protein', 'Interaction_Types', 'Confidence_Scores'], 'key_column' => 'Pathogen_Name'],
    'drug_target' => ['display_name' => 'Drug Targets', 'columns' => ['pathogen_name', 'prot_id', 'prot_desc', 'virulence', 'essential', 'core'], 'key_column' => 'pathogen_name'],
    'dti_ab' => ['display_name' => 'Drug-Target Interactions', 'columns' => ['Pathogen_name', 'Drug_Name', 'Target', 'Score', 'Drug_Type'], 'key_column' => 'Pathogen_name'],
    'operon_AD' => ['display_name' => 'Operon Analysis', 'columns' => ['Pathogen', 'Locus_Tag', 'Gene_Name', 'Product', 'Operon_ID'], 'key_column' => 'Pathogen'],
    'RNA_seq' => ['display_name' => 'RNA Sequencing Data', 'columns' => ['Organism', 'Project_Title', 'Project_Accession', 'Antibiotic_Name'], 'key_column' => 'Organism']
];

function getTableSpecificPathogenName($pathogen_key, $table_name, $table_pathogen_mapping) {
    return $table_pathogen_mapping[$table_name][$pathogen_key] ?? null;
}

function getAvailablePathogens($table_name, $table_pathogen_mapping, $pathogen_aliases) {
    $available = [];
    if (isset($table_pathogen_mapping[$table_name])) {
        foreach (array_keys($table_pathogen_mapping[$table_name]) as $key) {
            if (isset($pathogen_aliases[$key])) {
                $available[] = $pathogen_aliases[$key];
            }
        }
    }
    sort($available);
    return $available;
}

// AJAX HANDLER: Responds to JavaScript requests for available pathogens
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_pathogens_for_table' && isset($_GET['table'])) {
    header('Content-Type: application/json');
    $table_name = $_GET['table'];
    $available_pathogens = getAvailablePathogens($table_name, $table_pathogen_mapping, $pathogen_aliases);
    echo json_encode($available_pathogens);
    exit; // Stop script execution after sending JSON data
}

// --- Pagination & Input Processing ---
$allowed_rpp = [10, 20, 50, 100];
define('DEFAULT_RPP', 20);
$has_searched = !empty($_GET['pathogen_select']) && !empty($_GET['result_table']);
$search_pathogen_key = $_GET['pathogen_select'] ?? '';
$selected_table = $_GET['result_table'] ?? '';
$search_mode = $_GET['search_mode'] ?? 'exact';
$results_per_page = isset($_GET['rpp']) && in_array((int)$_GET['rpp'], $allowed_rpp) ? (int)$_GET['rpp'] : DEFAULT_RPP;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$results = [];
$total_results = 0;
$error_message = '';
$table_columns = [];

// --- SEARCH EXECUTION ---
if ($has_searched) {
    if (!array_key_exists($search_pathogen_key, $pathogen_aliases)) {
        $error_message = "Invalid pathogen selection.";
    } elseif (!array_key_exists($selected_table, $table_configs)) {
        $error_message = "Invalid table selection.";
    } else {
        $table_pathogen_name = getTableSpecificPathogenName($search_pathogen_key, $selected_table, $table_pathogen_mapping);
        if (!$table_pathogen_name) {
            $error_message = "The selected pathogen is not available in the chosen table. Please see the list of available pathogens below the form.";
        } else {
            $table_config = $table_configs[$selected_table];
            $table_columns = $table_config['columns'];
            $key_column = $table_config['key_column'];
            $where_condition = ($search_mode === 'partial') ? "`$key_column` LIKE ?" : "`$key_column` = ?";
            $search_value = ($search_mode === 'partial') ? "%" . $table_pathogen_name . "%" : $table_pathogen_name;

            // Count total results
            $count_sql = "SELECT COUNT(*) as total FROM `$selected_table` WHERE $where_condition";
            if ($stmt = mysqli_prepare($conn, $count_sql)) {
                mysqli_stmt_bind_param($stmt, 's', $search_value);
                mysqli_stmt_execute($stmt);
                $total_results = mysqli_stmt_get_result($stmt)->fetch_assoc()['total'] ?? 0;
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Error preparing count query: " . mysqli_error($conn);
            }

            // Get paginated results
            if ($total_results > 0 && empty($error_message)) {
                $offset = ($current_page - 1) * $results_per_page;
                $columns_str = '`' . implode('`, `', $table_columns) . '`';
                $data_sql = "SELECT $columns_str FROM `$selected_table` WHERE $where_condition LIMIT ? OFFSET ?";
                if ($stmt = mysqli_prepare($conn, $data_sql)) {
                    mysqli_stmt_bind_param($stmt, 'sii', $search_value, $results_per_page, $offset);
                    mysqli_stmt_execute($stmt);
                    $result_set = mysqli_stmt_get_result($stmt);
                    while ($row = mysqli_fetch_assoc($result_set)) { $results[] = $row; }
                    mysqli_stmt_close($stmt);
                } else {
                    $error_message = "Error preparing data query: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!-- Custom CSS Styles -->
<style>
    /* Your existing CSS styles go here. They are well-designed. */
    .search-container { background:#ffffff 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .search-container h1 { color: #10428d; font-weight: 700; margin-bottom: 10px; }
    .search-container p { color: rgba(255,255,255,0.9); font-size: 1.1em; }
    .form-group label { font-weight: 600; color: #10428d; margin-bottom: 8px; }
    .form-control, .form-check-input { border-radius: 8px; border: 2px solid rgba(255,255,255,0.3); background: rgba(255,255,255,0.9); }
    .form-control:focus { border-color: #fff; box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25); background: blue; }
    .btn-search { background: #10428d); border: none; border-radius: 25px; padding: 12px 30px; font-weight: 600; color: #10428d; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); }
    .btn-search:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4); color: #10428d; }
    .btn-reset { background: linear-gradient(45deg, #6c757d, #495057); border: none; border-radius: 25px; padding: 12px 30px; font-weight: 600; color: blue; transition: all 0.3s ease; }
    .btn-reset:hover { transform: translateY(-2px); color: blue; }
    .search-options { background: rgba(255,255,255,0.1); border-radius: 10px; padding: 20px; margin-top: 20px; }
    .radio-group { display: flex; gap: 20px; align-items: center; }
    .form-check-label { color: #10428d; font-weight: 500; margin-left: 8px; }
    .results-header { background: linear-gradient(135deg, #f8f9fa, #e9ecef); padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 5px solid #007bff; }
    .table-container { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .table thead th { background: linear-gradient(135deg, #343a40, #495057); color: white; border: none; font-weight: 600; text-transform: capitalize; padding: 15px 10px; }
    .table tbody tr:hover { background-color: #f8f9fa; transform: scale(1.01); transition: all 0.2s ease; }
    .pagination .page-item.active .page-link { background: linear-gradient(45deg, #007bff, #0056b3); border: none; font-weight: bold; }
    .alert-custom { border-radius: 10px; border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .pathogen-availability { background: rgba(255,255,255,0.1); border-radius: 8px; padding: 15px; margin-top: 15px; }
    .pathogen-availability h6 { color: white; margin-bottom: 10px; }
    .pathogen-availability small { color: rgba(255,255,255,0.8); display: block; margin-top: 5px; }
</style>

<div class="container-fluid mt-4">
    <!-- Search Form (HTML is unchanged from your code) -->
    <div class="search-container">
        <h1><i class="fas fa-search"></i> Browse Pathogen Database Search</h1>
        <p>Select a pathogen and data table to explore comprehensive genomic and proteomic information</p>
        
        <form action="" method="GET" class="mt-4">
             <div class="row">
                <div class="col-lg-4 col-md-6 form-group">
                    <label for="pathogen_select"><i class="fas fa-bacteria"></i> Filter by Pathogen</label>
                    <select name="pathogen_select" id="pathogen_select" class="form-control" required>
                        <option value="">-- Select a Pathogen --</option>
                        <?php foreach ($pathogen_aliases as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($key === $search_pathogen_key) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-6 form-group">
                    <label for="result_table"><i class="fas fa-table"></i> Filter by Result Type (Table)</label>
                    <select name="result_table" id="result_table" class="form-control" required>
                        <option value="">-- Select a Table --</option>
                        <?php foreach ($table_configs as $table_key => $config): ?>
                            <option value="<?php echo $table_key; ?>" <?php echo ($table_key === $selected_table) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($config['display_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-lg-4 col-md-12 form-group d-flex align-items-end">
                    <button class="btn btn-search mr-3" type="submit"><i class="fas fa-search"></i> Search</button>
                    <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn btn-reset"><i class="fas fa-redo"></i> Reset</a>
                </div>
            </div>
            
            <div class="search-options">
                <h6><i class="fas fa-cogs"></i> Search Options</h6>
                <div class="radio-group">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="search_mode" id="exact_match" value="exact" <?php echo ($search_mode === 'exact') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="exact_match">Exact Match</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="search_mode" id="partial_match" value="partial" <?php echo ($search_mode === 'partial') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="partial_match">Partial Match</label>
                    </div>
                </div>
                
                <div class="pathogen-availability" id="pathogen-availability" style="display: none;">
                    <h6><i class="fas fa-list-ul"></i> Pathogens in this Dataset</h6>
                    <div id="available-pathogens-list" class="text-light"></div>
                    <small><i class="fas fa-info-circle"></i> Only the pathogens listed here are available for the selected table.</small>
                </div>
            </div>
        </form>
    </div>

    <!-- Display Results -->
    <?php if ($has_searched): ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-custom"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error_message); ?></div>
        <?php elseif ($total_results > 0): 
            $start_num = (($current_page - 1) * $results_per_page) + 1;
            $end_num = $start_num + count($results) - 1;
        ?>
            <div class="results-header">
                <!-- Results Header HTML from your code -->
            </div>
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <?php foreach ($table_columns as $column): ?>
                                    <th><?php echo htmlspecialchars(str_replace('_', ' ', $column)); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $index => $row): ?>
                                <tr>
                                    <td class="font-weight-bold"><?php echo $start_num + $index; ?></td>
                                    <?php foreach ($table_columns as $column): ?>
                                        <td><?php echo htmlspecialchars($row[$column] ?? 'N/A'); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- **COMPLETED PAGINATION BLOCK** -->
            <?php
            $total_pages = ceil($total_results / $results_per_page);
            if ($total_pages > 1):
                $query_params = $_GET; // Preserve current filters
            ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                        <?php $query_params['page'] = $current_page - 1; ?>
                        <a class="page-link" href="?<?php echo http_build_query($query_params); ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                    </li>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <?php $query_params['page'] = $i; ?>
                            <a class="page-link" href="?<?php echo http_build_query($query_params); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                         <?php $query_params['page'] = $current_page + 1; ?>
                        <a class="page-link" href="?<?php echo http_build_query($query_params); ?>">Next <i class="fas fa-chevron-right"></i></a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        <?php else: ?>
             <div class="alert alert-warning alert-custom"><i class="fas fa-info-circle"></i> No results found matching your criteria.</div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- **COMPLETED JAVASCRIPT BLOCK** -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableSelect = document.getElementById('result_table');
    const availabilityDiv = document.getElementById('pathogen-availability');
    const pathogensListDiv = document.getElementById('available-pathogens-list');

    function updateAvailablePathogens() {
        const selectedTable = tableSelect.value;
        
        if (!selectedTable) {
            availabilityDiv.style.display = 'none';
            return;
        }
        
        // Show loading state
        pathogensListDiv.innerHTML = '<em>Loading...</em>';
        availabilityDiv.style.display = 'block';

        // Fetch available pathogens for the selected table
        fetch(`?ajax=get_pathogens_for_table&table=${selectedTable}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    pathogensListDiv.innerHTML = data.join(', ');
                } else {
                    pathogensListDiv.innerHTML = 'No specific pathogens found for this dataset.';
                }
            })
            .catch(error => {
                console.error('Error fetching pathogens:', error);
                pathogensListDiv.innerHTML = '<span class="text-danger">Could not load pathogen list.</span>';
            });
    }

    // Add event listener to trigger the update
    tableSelect.addEventListener('change', updateAvailablePathogens);
    
    // Also run on page load if a table is already selected (e.g., after form submission)
    if (tableSelect.value) {
        updateAvailablePathogens();
    }
});
</script>

<?php
// Include the footer
include 'footer.php';
?>