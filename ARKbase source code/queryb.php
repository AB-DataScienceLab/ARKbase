<?php
// --- START OF PHP SCRIPT ---

// --- CONFIGURATION ---
$tableName = 'drug_target';
$cogTableName = 'pathogen_cog_tb'; // COG table name
$binary_fields = ['non_paralog', 'virulence', 'essential', 'ttd_novel', 'drugbank_novel', 'human_NH', 'anti_target', 'betweenness', 'core', 'not_amr'];
$text_fields = ['prot_id', 'prot_desc']; // Kept for backend safety
$multi_select_fields = ['pathogen_name'];

$protein_feature_names = [
    'non_paralog' => 'Non-Paralog', 'virulence' => 'Virulence Factor', 'essential' => 'Essential Gene',
    'ttd_novel' => 'TTD Novel Target', 'drugbank_novel' => 'DrugBank Novel Target', 'human_NH' => 'Human Non-Homolog',
    'anti_target' => 'Anti-Target', 'betweenness' => 'Betweenness', 'core' => 'Core Gene', 'not_amr' => 'Not AMR Related'
];

$pathogen_full_names = [
    'a_baumannii' => 'Acinetobacter baumannii', 'e_coli' => 'Escherichia coli', 'e_faecium' => 'Enterococcus faecium',
    'h_influenzae' => 'Haemophilus influenzae', 'k_pneumoniae' => 'Klebsiella pneumoniae', 'n_gonorrhoeae' => 'Neisseria gonorrhoeae',
    'p_aeruginosa' => 'Pseudomonas aeruginosa', 's_agalactiae' => 'Streptococcus agalactiae', 's_aureus' => 'Staphylococcus aureus',
    's_enterica' => 'Salmonella enterica', 's_flexneri' => 'Shigella flexneri', 's_pneumoniae' => 'Streptococcus pneumoniae',
    's_pyogenes' => 'Streptococcus pyogenes', 's_sonnei' => 'Shigella sonnei'
];

$cog_category_descriptions = [
    'J' => 'Translation, ribosomal structure and biogenesis', 'A' => 'RNA processing and modification', 'K' => 'Transcription',
    'L' => 'Replication, recombination and repair', 'B' => 'Chromatin structure and dynamics', 'D' => 'Cell cycle control, cell division, chromosome partitioning',
    'Y' => 'Nuclear structure', 'V' => 'Defense mechanisms', 'T' => 'Signal transduction mechanisms',
    'M' => 'Cell wall/membrane/envelope biogenesis', 'N' => 'Cell motility', 'Z' => 'Cytoskeleton',
    'W' => 'Extracellular structures', 'U' => 'Intracellular trafficking, secretion, and vesicular transport',
    'O' => 'Posttranslational modification, protein turnover, chaperones', 'C' => 'Energy production and conversion',
    'G' => 'Carbohydrate transport and metabolism', 'E' => 'Amino acid transport and metabolism', 'F' => 'Nucleotide transport and metabolism',
    'H' => 'Coenzyme transport and metabolism', 'I' => 'Lipid transport and metabolism', 'P' => 'Inorganic ion transport and metabolism',
    'Q' => 'Secondary metabolites biosynthesis, transport and catabolism', 'R' => 'General function prediction only', 'S' => 'Function unknown'
];

$field_display_names = [
    'pathogen_name' => 'Pathogen Name',
    'protein_feature' => 'Protein Feature',
    'pathogen_cog' => 'Pathogen COG'
];

// Error reporting setup
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 0);

include 'header.php';
include 'conn.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// --- PAGINATION SETUP ---
$results = null;
$totalResults = 0;
$totalPages = 0;
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
if ($page < 1) $page = 1;
if (!in_array($limit, [10, 20, 50])) $limit = 10;
$offset = ($page - 1) * $limit;

// --- HELPER FUNCTIONS ---
function escapeLike($keyword) {
    return str_replace(array('%', '_'), array('\\%', '\\_'), $keyword);
}

function getUniqueValuesFromTable($conn, $column, $table) {
    $query = "SELECT DISTINCT `$column` FROM `$table` WHERE `$column` IS NOT NULL AND `$column` != '' ORDER BY `$column` ASC";
    $result = mysqli_query($conn, $query);
    $values = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) { $values[] = $row[$column]; }
        mysqli_free_result($result);
    }
    return $values;
}

function getUniqueCogCategories($conn, $table) {
    $unionParts = [];
    for ($i = 1; $i <= 5; $i++) {
        $unionParts[] = "SELECT SUBSTRING(COG_category, $i, 1) AS COG_Category_New FROM `$table` WHERE COG_category IS NOT NULL AND LENGTH(COG_category) >= $i";
    }
    $unionQuery = implode(" UNION ", $unionParts);
    $query = "SELECT DISTINCT COG_Category_New FROM ($unionQuery) AS unpivoted_cogs WHERE COG_Category_New IS NOT NULL AND COG_Category_New != '' ORDER BY COG_Category_New ASC";
    
    $result = mysqli_query($conn, $query);
    $values = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $values[] = $row['COG_Category_New'];
        }
        mysqli_free_result($result);
    }
    return $values;
}

// --- FETCH DATA FOR DROPDOWNS ---
$pathogenNameValues = getUniqueValuesFromTable($conn, 'pathogen_name', $tableName);
$cogCategoryValues = getUniqueCogCategories($conn, $cogTableName);

// --- Initialize summary message variable ---
$searchSummaryHtml = '';

// --- PROCESS FORM SUBMISSION ---
if (isset($_POST['submitted'])) {
    $allowed_fields = array_keys($field_display_names);
    $allowed_operators = ['LIKE', 'NOT LIKE'];
    $allowed_logicals = ['AND', 'OR'];
    
    $fieldnames = isset($_POST['fieldname']) ? $_POST['fieldname'] : [];
    $operators = isset($_POST['operator']) ? $_POST['operator'] : [];
    $keywords = isset($_POST['keyword']) ? $_POST['keyword'] : [];
    $logicalOperators = isset($_POST['logical_operator']) ? $_POST['logical_operator'] : [];
    
    $multiSelectDelimiter = '|||';
    $validConditions = []; 
    $summaryParts = [];

    for ($i = 0; $i < count($fieldnames); $i++) {
        if (empty(trim($keywords[$i])) && $keywords[$i] !== '0') continue;
        
        $field = $fieldnames[$i];
        $operator = $operators[$i];
        $logicalOp = ($i > 0) ? (isset($logicalOperators[$i - 1]) ? $logicalOperators[$i - 1] : 'AND') : '';

        if (!in_array($field, $allowed_fields) || !in_array($operator, $allowed_operators)) {
            continue; 
        }

        $keyword_raw = $keywords[$i];
        $currentCondition = '';
        $readableKeyword = '';
        
        if ($field === 'pathogen_name') {
            $columnName = "dt.`" . mysqli_real_escape_string($conn, $field) . "`"; // Use alias
            $pathogenNames = explode($multiSelectDelimiter, $keyword_raw);
            $subQueryParts = [];
            $readablePathogens = [];
            $innerLogicalOp = ($operator === 'LIKE') ? 'OR' : 'AND';

            foreach ($pathogenNames as $name) {
                if (empty(trim($name))) continue;
                $escapedKeyword = escapeLike(mysqli_real_escape_string($conn, trim($name)));
                $subQueryParts[] = "$columnName $operator '%$escapedKeyword%'";
                $readablePathogens[] = htmlspecialchars($pathogen_full_names[$name] ?? $name);
            }
            if (!empty($subQueryParts)) {
                $currentCondition = '(' . implode(" $innerLogicalOp ", $subQueryParts) . ')';
                $readableKeyword = implode(', ', $readablePathogens);
            }
        
        } else if ($field === 'protein_feature') {
            if (strpos($keyword_raw, $multiSelectDelimiter) === false) continue;
            list($featureKey, $value) = explode($multiSelectDelimiter, $keyword_raw, 2);
            if (empty($featureKey) || !in_array($value, ['0', '1'])) continue;
            $sqlOperator = ($operator === 'LIKE') ? '=' : '!=';
            if (in_array($featureKey, $binary_fields)) {
                $currentCondition = "dt.`" . mysqli_real_escape_string($conn, $featureKey) . "` $sqlOperator " . intval($value); // Use alias
                $readableFeature = htmlspecialchars($protein_feature_names[$featureKey] ?? $featureKey);
                $readableValue = ($value == 1) ? 'Yes' : 'No';
                $readableKeyword = "$readableFeature is $readableValue";
            }
        } else if ($field === 'pathogen_cog') {
            $selectedCategories = explode($multiSelectDelimiter, $keyword_raw);
            $cleanCategories = [];
            $readableCategories = [];
            foreach ($selectedCategories as $cat) {
                if (preg_match('/^[A-Z]$/', trim($cat))) {
                    $cleanCategories[] = "'" . mysqli_real_escape_string($conn, trim($cat)) . "'";
                    $readableCategories[] = htmlspecialchars(trim($cat));
                }
            }

            if (!empty($cleanCategories)) {
                $unionParts = [];
                for ($k = 1; $k <= 5; $k++) {
                    $unionParts[] = "SELECT Protein_ID, SUBSTRING(COG_category, $k, 1) AS COG_Category_New FROM `$cogTableName` WHERE COG_category IS NOT NULL AND LENGTH(COG_category) >= $k";
                }
                $unionQuery = implode(" UNION ", $unionParts);
                $categories_list = implode(',', $cleanCategories);
                $subQuery = "SELECT Protein_ID FROM ($unionQuery) AS unpivoted_cogs WHERE COG_Category_New IN ($categories_list)";
                
                $sqlOperator = ($operator === 'LIKE') ? 'IN' : 'NOT IN';
                $currentCondition = "dt.`prot_id` $sqlOperator ($subQuery)"; // Use alias
                $readableKeyword = implode(', ', $readableCategories);
            }
        }
        
        if (!empty($currentCondition)) {
            $validConditions[] = [ 'condition' => $currentCondition, 'logical' => $logicalOp ];
            $readableField = "<strong>" . htmlspecialchars($field_display_names[$field]) . "</strong>";
            $readableOperator = ($operator === 'LIKE') ? 'EQUALS' : 'DOES NOT EQUAL';
            $finalReadableKeyword = "<em>" . $readableKeyword . "</em>";
            
            $summaryPiece = ($i > 0) ? " $logicalOp " : "";
            if ($field === 'protein_feature') {
                $summaryPiece .= "$readableField $finalReadableKeyword";
            } else {
                $summaryPiece .= "$readableField $readableOperator $finalReadableKeyword";
            }
            $summaryParts[] = $summaryPiece;
        }
    }

    $fromAndJoinClause = "`$tableName` AS dt LEFT JOIN `$cogTableName` AS pc ON dt.prot_id = pc.Protein_ID";

    if (!empty($validConditions)) {
        if (!empty($summaryParts)) {
            $searchSummaryHtml = '<div class="search-summary"><strong>You searched for:</strong> ' . implode('', $summaryParts) . '</div>';
        }

        $queryParts = [];
        $queryParts[] = $validConditions[0]['condition'];
        for ($i = 1; $i < count($validConditions); $i++) {
            $queryParts[] = $validConditions[$i]['logical'];
            $queryParts[] = $validConditions[$i]['condition'];
        }
        $whereClause = "WHERE " . implode(' ', $queryParts);

        $countSql = "SELECT COUNT(dt.prot_id) as total FROM $fromAndJoinClause $whereClause";
        $countResult = mysqli_query($conn, $countSql);
        if ($countResult) {
            $row = mysqli_fetch_assoc($countResult);
            $totalResults = $row ? $row['total'] : 0;
            $totalPages = ceil($totalResults / $limit);
            if ($page > $totalPages && $totalPages > 0) {
                $page = $totalPages;
                $offset = ($page - 1) * $limit;
            }
        } else {
             echo "<div style='color:red;'>Count Query failed: " . mysqli_error($conn) . "</div>";
        }
        
        $sql = "SELECT dt.*, pc.COG_category, pc.COG_description 
                FROM $fromAndJoinClause 
                $whereClause 
                ORDER BY dt.pathogen_name ASC, dt.prot_id ASC 
                LIMIT $limit OFFSET $offset";
        $results = mysqli_query($conn, $sql);
        if (!$results) {
            echo "<div style='color:red;'>Query failed: " . mysqli_error($conn) . "</div>";
        }
    } else if (isset($_POST['submitted']) && $_SERVER["REQUEST_METHOD"] === "POST") {
         echo "<div style='color:orange;'>No valid search criteria were provided. Please enter a keyword or make a selection.</div>";
    }
}
// --- END OF PHP SCRIPT ---
?>

<!-- STYLES -->
<style>
    .query-builder-container { text-align: center; background-color: #fff; padding: 50px; border-radius: 20px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); width: 95%; max-width: 1400px; min-height: 450px; margin: 20px auto; }
    .query-builder-container h2, .query-builder-container h3 { text-align: center; color: #10428d; }
    
    .condition { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; color: #10428d; }
    .condition select, .condition input[type="text"], .condition button { padding: 10px; border: 1px solid #ccc; border-radius: 5px; color: #10428d; background-color: white; }
    .condition > .field-select { flex: 0 0 170px; }
    .condition > select[name="operator[]"] { flex: 0 0 120px; }
    .condition > select[name="logical_operator[]"] { flex: 0 0 80px; }
    .condition > button[onclick="removeCondition(this)"] { flex: 0 0 auto; }
    
    .pathogen-name-input-container,
    .protein-feature-container,
    .cog-feature-select {
        flex: 1 1 300px; /* Allow growth/shrinkage */
        min-width: 250px;
    }
    #conditions > .condition:first-child > select[name="logical_operator[]"] { visibility: hidden; }
    
    .keyword-checkbox-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 5px 10px; border: 1px solid #ccc; border-radius: 5px; padding: 10px; max-height: 150px; overflow-y: auto; text-align: left; background-color: #fff; width: 100%; }
    .protein-feature-container { display: flex; align-items: center; gap: 10px; }
    .protein-feature-select { flex: 1; }
    .protein-feature-value-select { flex: 0 0 auto; padding: 10px 8px; border: 1px solid #ccc; border-radius: 5px; color: #10428d; }
    
    .query-builder-container input[type="submit"], .query-builder-container button { background-color: #10428d; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; transition: background-color 0.3s; }
    .query-builder-container input[type="submit"]:hover, .query-builder-container button:hover { background-color: #0d3a7a; }
    .add-more-button { background-color: #2cab6c !important; }
    .search-submit-button { margin-left: 10px; }
    .reset-button { background-color: #6c757d !important; margin-left: 10px; }
    .reset-button:hover { background-color: #5a6268 !important; }

    /* --- START: Table Layout Fix --- */
    .results-table-container {
        width: 100%;
        overflow-x: auto; /* This enables horizontal scrolling */
        -webkit-overflow-scrolling: touch; /* Smooth scrolling on mobile */
    }
    .results-table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1); table-layout: auto; }
    /* --- END: Table Layout Fix --- */

    .results-table th, .results-table td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; word-wrap: break-word; white-space: nowrap; /* Prevent headers from wrapping */ font-size: 14px; }
    .results-table th { background-color: #10428d; color: white; text-transform: uppercase; font-weight: bold; }
    .results-table tr:nth-child(even) { background-color: #f2f2f2; }
    .results-table tr:hover { background-color: #f1f1f1; }
    .hidden { display: none !important; }
    .pagination-controls { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; padding: 10px; border-top: 1px solid #ddd; flex-wrap: wrap; gap: 20px; }
    .pagination-controls .page-links { display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 8px; padding: 5px 0; }
    .pagination-controls .page-links a { display: inline-block; padding: 8px 14px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #10428d; font-weight: 500; cursor: pointer; transition: background-color 0.2s, color 0.2s, border-color 0.2s; }
    .pagination-controls .page-links a:not(.active):not(.disabled):hover { background-color: #f0f5fa; border-color: #a3bde0; }
    .pagination-controls .page-links a.active { background-color: #10428d; color: white; border-color: #10428d; cursor: default; }
    .pagination-controls .page-links a.disabled { color: #adb5bd; background-color: #f8f9fa; border-color: #dee2e6; cursor: not-allowed; pointer-events: none; }
    .pagination-controls .page-links .ellipsis { padding: 8px 5px; color: #6c757d; cursor: default; }
    .pagination-controls .limit-selector label { margin-right: 10px; font-weight: bold; color: #10428d; }
    .pagination-controls .limit-selector select { padding: 8px; }
    .pagination-controls .results-info { color: #6c757d; font-size: 0.9em; }
    .keyword-checkbox-container label { display: flex; align-items: center; gap: 5px; padding: 4px; border-radius: 4px; transition: background-color 0.2s; margin-bottom: 0; font-weight: normal; cursor: pointer; }
    .keyword-checkbox-container label:hover { background-color: #f0f5fa; }
    .search-summary { text-align: left; padding: 15px; margin-top: 20px; margin-bottom: 20px; border: 1px solid #a3bde0; background-color: #f0f5fa; border-radius: 8px; color: #10428d; font-size: 1em; }
    .search-summary strong { color: #0d3a7a; }
    .search-summary em { font-style: normal; font-weight: bold; background-color: #e0e9f5; padding: 2px 6px; border-radius: 4px; }
    @media (max-width: 992px) { .condition { flex-direction: column; align-items: stretch; } .condition select, .condition input[type="text"], .keyword-checkbox-container, .protein-feature-container { width: 100%; margin-bottom: 10px; } .pagination-controls { flex-direction: column; } .keyword-checkbox-container { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); } #conditions > .condition:first-child > select[name="logical_operator[]"] { display: none; } }
</style>

<br>
<div class="query-builder-container">
    <h2>ARKbase Advanced Search | Query Builder</h2><br>

    <form method="POST" action="" id="searchForm">
        <input type="hidden" name="page" id="currentPage" value="<?php echo $page; ?>">
        <input type="hidden" name="limit" id="currentLimit" value="<?php echo $limit; ?>">
        <input type="hidden" name="submitted" value="1">

        <div id="conditions">
            <?php 
            $num_conditions = isset($_POST['fieldname']) ? count($_POST['fieldname']) : 1;
            for ($i = 0; $i < $num_conditions; $i++):
                $selectedField = isset($_POST['fieldname'][$i]) ? $_POST['fieldname'][$i] : 'pathogen_name';
                $selectedOperator = isset($_POST['operator'][$i]) ? $_POST['operator'][$i] : 'LIKE';
                $keywordValue = isset($_POST['keyword'][$i]) ? htmlspecialchars($_POST['keyword'][$i]) : '';
                $logicalOperator = isset($_POST['logical_operator'][$i - 1]) ? $_POST['logical_operator'][$i - 1] : 'AND';

                $selectedPathogenValues = [];
                $selectedFeatureKey = '';
                $selectedFeatureValue = '';
                $selectedCogValues = [];

                if ($selectedField === 'pathogen_name' && !empty($keywordValue)) {
                    $selectedPathogenValues = explode('|||', $keywordValue);
                } elseif ($selectedField === 'protein_feature' && !empty($keywordValue) && strpos($keywordValue, '|||') !== false) {
                    list($key, $val) = explode('|||', $keywordValue, 2);
                    $selectedFeatureKey = $key;
                    $selectedFeatureValue = $val;
                } elseif ($selectedField === 'pathogen_cog' && !empty($keywordValue)) {
                    $selectedCogValues = explode('|||', $keywordValue);
                }
            ?>
            <div class="condition">
                <select name="fieldname[]" class="field-select" onchange="toggleInputType(this)">
                    <?php foreach($field_display_names as $field_key => $field_label): ?>
                    <option value="<?php echo $field_key; ?>" <?php if($selectedField == $field_key) echo 'selected';?>><?php echo $field_label; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="operator[]">
                    <option value="LIKE" <?php if($selectedOperator == 'LIKE') echo 'selected';?>>EQUAL</option>
                    <option value="NOT LIKE" <?php if($selectedOperator == 'NOT LIKE') echo 'selected';?>>NOT EQUAL</option>
                </select>
                
                <input type="hidden" name="keyword[]" class="keyword-hidden-input" value="<?php echo $keywordValue; ?>">

                <div class="pathogen-name-input-container keyword-checkbox-container <?php if($selectedField !== 'pathogen_name') echo 'hidden'; ?>">
                    <?php foreach ((array)$pathogenNameValues as $short_name): 
                        $full_name = $pathogen_full_names[$short_name] ?? $short_name;
                    ?>
                        <label><input type="checkbox" value="<?php echo htmlspecialchars($short_name); ?>" <?php if(in_array($short_name, $selectedPathogenValues)) echo 'checked'; ?>> <?php echo htmlspecialchars($full_name); ?></label>
                    <?php endforeach; ?>
                </div>

                <div class="protein-feature-container <?php if($selectedField !== 'protein_feature') echo 'hidden'; ?>">
                    <div class="keyword-checkbox-container protein-feature-select">
                        <?php foreach($protein_feature_names as $key => $label): ?>
                        <label><input type="radio" name="protein_feature_key_<?php echo $i; ?>" value="<?php echo $key; ?>" <?php if($selectedFeatureKey == $key) echo 'checked'; ?>> <?php echo $label; ?></label>
                        <?php endforeach; ?>
                    </div>
                    <select class="protein-feature-value-select">
                        <option value="1" <?php if ($selectedFeatureValue === '1') echo 'selected'; ?>>Yes</option>
                        <option value="0" <?php if ($selectedFeatureValue === '0' || $selectedFeatureValue === '') echo 'selected'; ?>>No</option>
                    </select>
                </div>
                
                <div class="keyword-checkbox-container cog-feature-select <?php if($selectedField !== 'pathogen_cog') echo 'hidden'; ?>">
                    <?php foreach ($cogCategoryValues as $category): 
                        $description = $cog_category_descriptions[$category] ?? 'Unknown Category';
                    ?>
                        <label>
                            <input type="checkbox" value="<?php echo htmlspecialchars($category); ?>" <?php if(in_array($category, $selectedCogValues)) echo 'checked'; ?>>
                            <?php echo htmlspecialchars("[$category] $description"); ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <select name="logical_operator[]">
                    <option value="AND" <?php if($logicalOperator == 'AND') echo 'selected';?>>AND</option>
                    <option value="OR" <?php if($logicalOperator == 'OR') echo 'selected';?>>OR</option>
                </select>
                <button type="button" onclick="removeCondition(this)">Remove</button>
            </div>
            <?php endfor; ?>
        </div>
        <button type="button" class="add-more-button" onclick="addCondition()">Add More Fields</button>
        <button type="submit" class="search-submit-button">Search</button>
        <button type="button" class="reset-button" onclick="resetForm()">Reset</button>
    </form>
    <br>
    
    <?php 
    if (!empty($searchSummaryHtml)) { echo $searchSummaryHtml; }
    if (isset($_POST['submitted']) && $results): 
    ?>
        <!-- START: Added DIV wrapper for the table -->
        <div class="results-table-container">
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Sr.No.</th><th>Pathogen Name</th><th>Protein ID</th><th>Protein Description.</th>
                        <th>COG Category</th><th>COG Description</th>
                        <th>Non-Paralog</th><th>Virulence</th><th>Essential</th><th>TTD Novel</th>
                        <th>DrugBank Novel</th><th>Human NH</th><th>Anti-Target</th><th>Betweenness</th>
                        <th>Core</th><th>Not AMR</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $serialNo = $offset + 1;
                if (mysqli_num_rows($results) > 0) {
                    while ($row = mysqli_fetch_assoc($results)): ?>
                        <tr>
                            <td><?php echo $serialNo++; ?></td>
                            <td><?php echo htmlspecialchars($pathogen_full_names[$row['pathogen_name']] ?? $row['pathogen_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['prot_id']); ?></td>
                            <td style="white-space: normal;"><?php echo htmlspecialchars($row['prot_desc']); ?></td>
                            <td><?php echo htmlspecialchars($row['COG_category'] ?? ''); ?></td>
                            <td style="white-space: normal;"><?php echo htmlspecialchars($row['COG_description'] ?? ''); ?></td>
                            <td><?php echo ($row['non_paralog'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['virulence'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['essential'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['ttd_novel'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['drugbank_novel'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['human_NH'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['anti_target'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['betweenness'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['core'] == 1 ? 'Yes' : 'No'); ?></td>
                            <td><?php echo ($row['not_amr'] == 1 ? 'Yes' : 'No'); ?></td>
                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='16' style='text-align:center;'>No results found for your query.</td></tr>";
                }
                mysqli_free_result($results);
                ?>
                </tbody>
            </table>
        </div>
        <!-- END: Added DIV wrapper for the table -->

        <?php if ($totalPages > 1): ?>
        <div class="pagination-controls">
             <div class="limit-selector"><label for="limitSelect">Rows per page:</label><select id="limitSelect" onchange="changeLimit()"><option value="10" <?php if ($limit == 10) echo 'selected'; ?>>10</option><option value="20" <?php if ($limit == 20) echo 'selected'; ?>>20</option><option value="50" <?php if ($limit == 50) echo 'selected'; ?>>50</option></select></div><div class="page-links"><a onclick="changePage(<?php echo $page - 1; ?>)" class="<?php if($page <= 1){ echo 'disabled'; } ?>">Previous</a><?php $linksToShow = 5; $sideLinks = floor($linksToShow / 2); $startPage = max(1, $page - $sideLinks); $endPage = min($totalPages, $page + $sideLinks); if ($page - $sideLinks < 1) { $endPage = min($totalPages, $linksToShow); } if ($page + $sideLinks > $totalPages) { $startPage = max(1, $totalPages - $linksToShow + 1); } if ($startPage > 1) { echo '<a onclick="changePage(1)">1</a>'; if ($startPage > 2) { echo '<span class="ellipsis">...</span>'; } } for($i = $startPage; $i <= $endPage; $i++): ?><a onclick="changePage(<?php echo $i; ?>)" class="<?php if($page == $i) {echo 'active';} ?>"><?php echo $i; ?></a><?php endfor; if ($endPage < $totalPages) { if ($endPage < $totalPages - 1) { echo '<span class="ellipsis">...</span>'; } echo '<a onclick="changePage('.$totalPages.')">'.$totalPages.'</a>'; } ?><a onclick="changePage(<?php echo $page + 1; ?>)" class="<?php if($page >= $totalPages) { echo 'disabled'; } ?>">Next</a></div><div class="results-info">Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $totalResults); ?> of <?php echo $totalResults; ?> results</div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
    const multiSelectDelimiter = '|||'; 

    document.getElementById('searchForm').addEventListener('submit', function(e) {
        document.getElementById('currentPage').value = 1;
        syncAllKeywordInputs();
    });

    function changePage(newPage) { if (newPage < 1 || newPage > <?php echo $totalPages; ?>) return; document.getElementById('currentPage').value = newPage; syncAndSubmit(); }
    function changeLimit() { document.getElementById('currentLimit').value = document.getElementById('limitSelect').value; document.getElementById('currentPage').value = 1; syncAndSubmit(); }
    function syncAndSubmit() { syncAllKeywordInputs(); document.getElementById('searchForm').submit(); }

    function syncAllKeywordInputs() {
        document.querySelectorAll('.condition').forEach(condition => {
            const fieldSelect = condition.querySelector('.field-select');
            const selectedField = fieldSelect.value;
            const hiddenInput = condition.querySelector('.keyword-hidden-input');
            let keywordValue = '';

            if (selectedField === 'pathogen_name') {
                const container = condition.querySelector('.pathogen-name-input-container');
                const checkedBoxes = container.querySelectorAll('input[type="checkbox"]:checked');
                keywordValue = Array.from(checkedBoxes).map(cb => cb.value).join(multiSelectDelimiter);
            } else if (selectedField === 'protein_feature') {
                const selectedRadio = condition.querySelector('.protein-feature-select input[type="radio"]:checked');
                const featureKey = selectedRadio ? selectedRadio.value : '';
                const valueSelect = condition.querySelector('.protein-feature-value-select');
                const selectedValue = valueSelect.value;
                if (featureKey && selectedValue !== '') {
                    keywordValue = featureKey + multiSelectDelimiter + selectedValue;
                }
            } else if (selectedField === 'pathogen_cog') {
                const container = condition.querySelector('.cog-feature-select');
                const checkedBoxes = container.querySelectorAll('input[type="checkbox"]:checked');
                keywordValue = Array.from(checkedBoxes).map(cb => cb.value).join(multiSelectDelimiter);
            }
            hiddenInput.value = keywordValue;
        });
    }
    
    function toggleInputType(selectElement) { 
        const condition = selectElement.closest('.condition'); 
        const selectedField = selectElement.value; 
        
        const pathogenContainer = condition.querySelector('.pathogen-name-input-container');
        const proteinFeatureContainer = condition.querySelector('.protein-feature-container');
        const cogContainer = condition.querySelector('.cog-feature-select');

        pathogenContainer.classList.add('hidden');
        proteinFeatureContainer.classList.add('hidden');
        cogContainer.classList.add('hidden');

        if (selectedField === 'pathogen_name') {
            pathogenContainer.classList.remove('hidden');
        } else if (selectedField === 'protein_feature') {
            proteinFeatureContainer.classList.remove('hidden');
        } else if (selectedField === 'pathogen_cog') {
            cogContainer.classList.remove('hidden');
        }
    }

    function resetForm() { window.location.href = window.location.pathname; }
    
    function removeCondition(button) {
        const conditionsContainer = document.getElementById('conditions');
        if (conditionsContainer.children.length > 1) {
            button.closest('.condition').remove();
        } else {
            alert("Cannot remove the last search condition.");
        }
    }
    
    function addCondition() {
        const conditionsDiv = document.getElementById('conditions');
        const firstCondition = conditionsDiv.firstElementChild;
        const newCondition = firstCondition.cloneNode(true);
        
        newCondition.querySelector('.field-select').selectedIndex = 0;
        newCondition.querySelector('select[name="operator[]"]').selectedIndex = 0;
        newCondition.querySelector('.keyword-hidden-input').value = '';
        
        newCondition.querySelectorAll('.pathogen-name-input-container input[type="checkbox"]').forEach(cb => cb.checked = false);
        newCondition.querySelectorAll('.cog-feature-select input[type="checkbox"]').forEach(cb => cb.checked = false);

        const newIndex = conditionsDiv.children.length; 
        newCondition.querySelectorAll('.protein-feature-select input[type="radio"]').forEach(radio => {
            radio.name = `protein_feature_key_${newIndex}`;
            radio.checked = false;
        });
        if (newCondition.querySelector('.protein-feature-select input[type="radio"]')) {
             newCondition.querySelector('.protein-feature-select input[type="radio"]').checked = true;
        }

        newCondition.querySelector('.protein-feature-value-select').selectedIndex = 0;
        
        toggleInputType(newCondition.querySelector('.field-select'));
        
        conditionsDiv.appendChild(newCondition);
    }
    
    document.addEventListener('DOMContentLoaded', function() { 
        document.querySelectorAll('.condition .field-select').forEach(select => { 
            toggleInputType(select); 
        }); 
    });
</script>

<?php 
if ($conn) {
    mysqli_close($conn);
}
include 'footer.php'; 
?>