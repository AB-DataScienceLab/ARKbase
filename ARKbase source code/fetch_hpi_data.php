<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $username, $password, $dbname, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

// Get COG Categories Endpoint
if (isset($_GET['get_cog_categories'])) {
    $cog_category_descriptions = [
        'J' => 'Translation, ribosomal structure and biogenesis', 'A' => 'RNA processing and modification', 'K' => 'Transcription',
        'L' => 'Replication, recombination and repair', 'B' => 'Chromatin structure and dynamics', 'D' => 'Cell cycle control, cell division, chromosome partitioning',
        'Y' => 'Nuclear structure', 'V' => 'Defense mechanisms', 'T' => 'Signal transduction mechanisms',
        'M' => 'Cell wall/membrane/envelope biogenesis', 'N' => 'Cell motility', 'Z' => 'Cytoskeleton',
        'W' => 'Extracellular structures', 'U' => 'Intracellular trafficking, secretion, and vesicular transport',
        'O' => 'Posttranslational modification, protein turnover, chaperones', 'C' => 'Energy production and conversion',
        'G' => 'Carbohydrate transport and metabolism', 'E' => 'Amino acid transport and metabolism', 'F' => 'Nucleotide transport and metabolism',
        'H' => 'Coenzyme transport and metabolism', 'I' => 'Lipid transport and metabolism', 'P' => 'Inorganic ion transport and metabolism',
        'Q' => 'Secondary metabolites biosynthesis, transport and catabolism', 'R' => 'General function prediction only', 'S' => 'Function unknown',
        '-' => 'Not Classified'
    ];
    $sql = "SELECT DISTINCT COG_category FROM HPI_data_extended WHERE COG_category IS NOT NULL AND COG_category != '' AND COG_category != 'NA'";
    $result = $conn->query($sql);
    $allLetters = [];
    while ($row = $result->fetch_assoc()) {
        $letters = str_split($row['COG_category']);
        foreach ($letters as $letter) { $allLetters[] = $letter; }
    }
    $uniqueCodes = array_unique($allLetters);
    sort($uniqueCodes);
    $cogData = [];
    foreach ($uniqueCodes as $code) {
        if (isset($cog_category_descriptions[$code])) {
            $cogData[] = [ 'code' => $code, 'description' => $cog_category_descriptions[$code] ];
        }
    }
    echo json_encode($cogData);
    $conn->close();
    exit();
}

// --- FILTER AND SEARCH PARAMETERS ---
$search = isset($_GET['search']) ? $_GET['search'] : '';
$pathogens = isset($_GET['pathogens']) ? array_filter(explode(',', $_GET['pathogens'])) : [];
$cogs = isset($_GET['cogs']) ? array_filter(explode(',', $_GET['cogs'])) : [];

// --- LOGIC TO HANDLE CSV DOWNLOAD REQUEST ---
if (isset($_GET['download']) && $_GET['download'] === 'true') {
    header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="hpi_filtered_data.csv"'); header('Pragma: no-cache'); header('Expires: 0');
    $sql = "SELECT Host_protein AS Host_Protein, Host_Protein_name, Pathogen_protein AS Pathogen_target, Pathogen_protein_name AS Pathogen_protein, Source_Databases, Experimental_Methods, Confidence_Scores, PubMed_IDs, Host_Organism, Pathogen_Organism, COG_category, Host_Protein_Degree, Pathogen_Protein_Degree, Reference_Protein_ID FROM HPI_data_extended";
    $whereClauses = []; $params = []; $types = '';
    if (!empty($search)) { $searchTerm = "%{$search}%"; $whereClauses[] = "(Host_protein LIKE ? OR Host_Protein_name LIKE ? OR Pathogen_protein_name LIKE ? OR Pathogen_Organism LIKE ?)"; array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm); $types .= 'ssss'; }
    if (!empty($pathogens)) { $pathogenLikes = []; foreach ($pathogens as $pathogen) { $pathogenLikes[] = "Pathogen_Organism LIKE ?"; $params[] = "%" . trim($pathogen) . "%"; $types .= 's'; } $whereClauses[] = "(" . implode(" OR ", $pathogenLikes) . ")"; }
    if (!empty($cogs)) { $cogConditions = []; foreach ($cogs as $cog) { $trimmedCog = trim($cog); if ($trimmedCog === '-') { $cogConditions[] = "COG_category = ?"; $params[] = $trimmedCog; } else { $cogConditions[] = "COG_category LIKE ?"; $params[] = "%" . $trimmedCog . "%"; } $types .= 's'; } $whereClauses[] = "(" . implode(" OR ", $cogConditions) . ")"; }
    if (!empty($whereClauses)) { $sql .= " WHERE " . implode(" AND ", $whereClauses); }
    $stmt = $conn->prepare($sql); if (!empty($params)) { $stmt->bind_param($types, ...$params); } $stmt->execute(); $result = $stmt->get_result();
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Host_Protein', 'Host_Protein_name', 'Pathogen_target', 'Pathogen_protein', 'Source_Databases', 'Experimental_Methods', 'Confidence_Scores', 'PubMed_IDs', 'Host_Organism', 'Pathogen_Organism', 'COG_category', 'Host_Protein_Degree', 'Pathogen_Protein_Degree', 'Reference_Protein_ID']);
    while ($row = $result->fetch_assoc()) { fputcsv($output, $row); }
    fclose($output); $stmt->close(); $conn->close(); exit();
}


// --- Request for GO Chart Statistics ---
if (isset($_GET['get_go_stats'])) {
    $goType = $_GET['get_go_stats'];
    $columnMap = ['biological' => 'Pathogen_GO_Biological_process', 'molecular' => 'Pathogen_GO_Molecular_function', 'cellular' => 'Pathogen_GO_Cellular_Component'];
    if (!array_key_exists($goType, $columnMap)) { http_response_code(400); echo json_encode(["error" => "Invalid GO type specified."]); exit(); }
    $columnName = $columnMap[$goType];
    $sql = "SELECT {$columnName} FROM HPI_data_extended WHERE {$columnName} IS NOT NULL AND {$columnName} != 'NA'";
    $result = $conn->query($sql);
    $termCounts = [];
    while($row = $result->fetch_assoc()) {
        $terms = explode(';', $row[$columnName]);
        foreach($terms as $term) {
            $cleanTerm = trim(preg_replace('/\[GO:\d+\]$/', '', $term));
            if($cleanTerm) { $termCounts[$cleanTerm] = ($termCounts[$cleanTerm] ?? 0) + 1; }
        }
    }
    arsort($termCounts);
    $topTerms = array_slice($termCounts, 0, 10, true);
    echo json_encode(['labels' => array_keys($topTerms), 'data' => array_values($topTerms)]);
    $conn->close();
    
    // *** MODIFICATION START: Added the missing exit() statement ***
    exit();
    // *** MODIFICATION END ***
}

// --- Main Data Fetching (JSON / Pagination) ---
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50; $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; $offset = ($page - 1) * $limit;
$sql = "SELECT SQL_CALC_FOUND_ROWS Host_protein AS Host_Protein, Host_Protein_name, Pathogen_protein AS Pathogen_target, Pathogen_protein_name AS Pathogen_protein, Source_Databases, Experimental_Methods AS Experimental_Methods_Agg, PubMed_IDs AS PubMed_IDs_Agg, Host_TaxID, Host_Organism AS Host_Organism_Agg, Interaction_Types AS Interaction_Types_Agg, Confidence_Scores AS Confidence_Scores_Agg, PathogenGeneID AS PathogenGeneID_Original, Pathogen_Organism, Pathogen_organism_taxa, Pathogen_GO_Biological_process AS `Pathogen_Gene.Ontology..biological.process.`, Pathogen_GO_Molecular_function AS `Pathogen_Gene.Ontology..molecular.function.`, Pathogen_GO_Cellular_Component AS `Pathogen_Gene.Ontology..cellular_component.`, COG_category, Host_Protein_Degree, Pathogen_Protein_Degree, Reference_Protein_ID FROM HPI_data_extended";
$whereClauses = []; $params = []; $types = '';

if (!empty($search)) { $searchTerm = "%{$search}%"; $whereClauses[] = "(Host_protein LIKE ? OR Host_Protein_name LIKE ? OR Pathogen_protein_name LIKE ? OR Pathogen_Organism LIKE ?)"; array_push($params, $searchTerm, $searchTerm, $searchTerm, $searchTerm); $types .= 'ssss'; }
if (!empty($pathogens)) { $pathogenLikes = []; foreach ($pathogens as $pathogen) { $pathogenLikes[] = "Pathogen_Organism LIKE ?"; $params[] = "%" . trim($pathogen) . "%"; $types .= 's'; } $whereClauses[] = "(" . implode(" OR ", $pathogenLikes) . ")"; }
if (!empty($cogs)) { $cogConditions = []; foreach ($cogs as $cog) { $trimmedCog = trim($cog); if ($trimmedCog === '-') { $cogConditions[] = "COG_category = ?"; $params[] = $trimmedCog; } else { $cogConditions[] = "COG_category LIKE ?"; $params[] = "%" . $trimmedCog . "%"; } $types .= 's'; } $whereClauses[] = "(" . implode(" OR ", $cogConditions) . ")"; }

if (!empty($whereClauses)) { $sql .= " WHERE " . implode(" AND ", $whereClauses); }
$sql .= " LIMIT ? OFFSET ?"; $params[] = $limit; $params[] = $offset; $types .= 'ii';
$stmt = $conn->prepare($sql); if ($stmt === false) { error_log("SQL Prepare Error: " . $conn->error); http_response_code(500); echo json_encode(["error" => "Error preparing SQL statement."]); exit(); }
if (!empty($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute(); $result = $stmt->get_result(); $data = []; while($row = $result->fetch_assoc()) { $data[] = $row; } $stmt->close();
$totalRecordsResult = $conn->query("SELECT FOUND_ROWS()"); $totalRecords = $totalRecordsResult->fetch_row()[0];
$conn->close();
echo json_encode(['totalRecords' => (int)$totalRecords, 'totalPages' => ceil($totalRecords / $limit), 'currentPage' => $page, 'data' => $data]);
?>