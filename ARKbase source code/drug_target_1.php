<?php
include 'header.php';
include 'conn.php'; 

if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

// --- Pathogen Alias to Full Name Mapping ---
$pathogenAliasToFullNameMap = [
    'a_baumannii' => 'Acinetobacter baumannii',
    'n_gonorrhoeae' => 'Neisseria gonorrhoeae',
    's_sonnei' => 'Shigella sonnei',
    's_pyogenes' => 'Streptococcus pyogenes',
    's_pneumoniae' => 'Streptococcus pneumoniae',
    's_flexneri' => 'Shigella flexneri',
    's_enterica' => 'Salmonella enterica',
    's_aureus' => 'Staphylococcus aureus',
    's_agalactiae' => 'Streptococcus agalactiae',
    'p_aeruginosa' => 'Pseudomonas aeruginosa',
    'k_pneumoniae' => 'Klebsiella pneumoniae',
    'h_influenzae' => 'Haemophilus influenzae',
    'e_faecium' => 'Enterococcus faecium',
    'e_coli' => 'Escherichia coli'
];

// --- Get Parameters ---
$pathogen_alias = $_GET['pathogen'] ?? '';
$protein_id1 = $_GET['protein_id1'] ?? '';
$protein_id2 = $_GET['protein_id2'] ?? '';

if (empty($pathogen_alias) || (empty($protein_id1) && empty($protein_id2))) {
    $error_message = "Error: Pathogen and at least one Protein ID are required.";
}

$results_by_protein = [];

if (empty($error_message)) {
    $protein_ids_to_search = [];
    if (!empty($protein_id1)) $protein_ids_to_search[] = $protein_id1;
    if (!empty($protein_id2)) $protein_ids_to_search[] = $protein_id2;

    $placeholders = implode(',', array_fill(0, count($protein_ids_to_search), '?'));
    $types = 's' . str_repeat('s', count($protein_ids_to_search));
    $params = array_merge([$pathogen_alias], $protein_ids_to_search);

    $sql = "SELECT * FROM drug_target WHERE pathogen_name = ? AND prot_id IN ($placeholders)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $results_by_protein[$row['prot_id']][] = $row;
    }
}

function render_protein_details_card($protein_id, $protein_label, $results_array) {
    global $pathogenAliasToFullNameMap;

    if (empty($protein_id)) return;

    $output = "<div class='card mb-4'>";
    $output .= "<div class='card-header'><h4>Details for {$protein_label}: <a href='https://www.ncbi.nlm.nih.gov/protein/" . htmlspecialchars($protein_id) . "' target='_blank' rel='noopener noreferrer'>" . htmlspecialchars($protein_id) . "</a></h4></div>";
    $output .= "<div class='card-body'>";

    if (!empty($results_array[$protein_id])) {
        foreach ($results_array[$protein_id] as $index => $hit) {
            if ($index > 0) $output .= '<hr>';

            $output .= '<div class="table-responsive"><table class="table table-bordered table-sm mb-0"><tbody>';

            $excluded_keys = ['card_desc', 'amr_gene_family', 'resistance_mechanism', 'antibiotics'];
            foreach ($hit as $key => $value) {
                if (in_array($key, $excluded_keys)) continue;
                if ($value === null) continue;

                // Show full name in italics for 'pathogen_name'
                if ($key == 'pathogen_name' && isset($pathogenAliasToFullNameMap[$value])) {
                    $value = "<em>" . htmlspecialchars($pathogenAliasToFullNameMap[$value]) . "</em>";
                }

                $label = htmlspecialchars(ucwords(str_replace('_', ' ', $key)));
                $output .= "<tr><th style='width: 25%; background-color: #f8f9fa;'>{$label}</th><td>";

                if (in_array($key, ['non_paralog', 'virulence', 'essential', 'ttd_novel', 'drugbank_novel', 'human_NH', 'anti_target', 'core', 'not_amr', 'betweenness'])) {
                    $output .= $value == 1 ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
                } else {
                    $output .= $value;
                }

                $output .= "</td></tr>";
            }

            $output .= '</tbody></table></div>';
        }
    } else {
        $output .= '<p class="text-muted">No drug target information found for this protein.</p>';
    }

    $output .= "</div></div>";
    return $output;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Target Details</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<header class="text-center py-4 bg-white shadow-sm">
    <h1>Drug Target Details</h1>
</header>

<main class="container py-4">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center">
            <a href="javascript:history.back()" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Back to Previous Page
            </a>
        </div>

        <div class="p-3 mb-4 bg-light rounded-3">
            <p class="fs-4">
                Showing potential drug target information for the selected proteins in 
                <em><?php echo htmlspecialchars($pathogenAliasToFullNameMap[$pathogen_alias] ?? $pathogen_alias); ?></em>.
            </p>
        </div>

        <?php
        echo render_protein_details_card($protein_id1, 'Protein 1', $results_by_protein);
        echo render_protein_details_card($protein_id2, 'Protein 2', $results_by_protein);
        ?>
    <?php endif; ?>
</main>



<?php mysqli_close($conn); ?>
</body>
</html>

<?php  include('footer.php'); ?>