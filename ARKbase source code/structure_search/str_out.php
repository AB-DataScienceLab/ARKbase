<?php
// 1. Include the site header.
include '/var/www/html/anshu/arkbase/header.php';

// --- PHP LOGIC TO PROCESS THE SEARCH ---
$python_script_path = 'rdkit_search.py';
$threshold = 0.8;
$query_smiles = null;
if (!empty($_FILES['myfile']['name'])) { $filename = $_FILES['myfile']['name']; $tmp_name = $_FILES['myfile']['tmp_name']; $uploaded_path = "uploaded_files/" . basename($filename); move_uploaded_file($tmp_name, $uploaded_path); $query_smiles = shell_exec("LD_LIBRARY_PATH='/usr/local/lib:/usr/lib64:/usr/lib'; /usr/local/bin/obabel " . escapeshellarg($uploaded_path) . " -osmi"); } elseif (!empty($_POST['smil'])) { $query_smiles = $_POST['smil']; } elseif (!empty($_POST['smi'])) { $query_smiles = $_POST['smi']; }
$query_smiles = trim($query_smiles ?? '');
$allowed_databases = [ 'Known Antibiotics' => '/var/www/html/anshu/arkbase/structure_search/database/database_drugs.csv', 'BGC Secondary Metabolites' => '/var/www/html/anshu/arkbase/structure_search/database/bgc_known_structures.csv' ];
$database_path = ''; $database_name = 'Unknown';
$selected_path = $_POST['database_choice'] ?? '';
if (in_array($selected_path, $allowed_databases)) { $database_path = $selected_path; $database_name = array_search($selected_path, $allowed_databases); }
?>

<!-- STYLESHEET AND JAVASCRIPT FOR THIS PAGE -->
<style>
    .header h1 { font-weight: 300; }
    .section-title { color: #212529; border-bottom: 3px solid #0d6efd; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
    .table-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 2rem; }
    .data-table { max-height: 70vh; overflow-y: auto; }
    .error-box { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; }
    .query-info { background-color: #e9ecef; border-left: 4px solid #0d6efd; padding: 1rem; margin-bottom: 1.5rem; border-radius: 5px; }
    pre { background: #fff; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; white-space: pre-wrap; word-wrap: break-word; border: 1px solid #ced4da; }
    
    .view-structure-btn { background-color: #0d6efd; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; font-size: 0.9em; transition: background-color 0.2s; }
    .view-structure-btn:hover { background-color: #0a58ca; }
    .structure-row { display: none; }
    .structure-row td { background-color: #f8f9fa; padding: 20px; text-align: center; }
    .structure-img { max-width: 300px; display: inline-block; border: 1px solid #ccc; border-radius: 5px; }
    .download-link { display: inline-block; margin-top: 1rem; background-color: #198754; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; }
    .download-link:hover { background-color: #157347; }
    .data-row:nth-child(even) { background-color: #f8f9fa; }
</style>

<script>
    function toggleStructure(rowId) {
        const row = document.getElementById(rowId);
        if (row.style.display === 'table-row') {
            row.style.display = 'none';
        } else {
            row.style.display = 'table-row';
        }
    }
</script>


<!-- MAIN CONTENT AREA -->
<div class="container-fluid px-4">
    <header class="header pt-4">
        <div class="container">
            <center>
                <h1 class="section-title">Structure Search Results</h1>
            </center>
        </div>
    </header>

    <section>
        <div class="table-container">
            <?php if (!$query_smiles || !$database_path): ?>
                <div class='error-box'><h2>Error: Input data not found or invalid database selected.</h2></div>
            <?php else:
                // --- RDKit Script Execution ---
                $command = "python " . escapeshellarg($python_script_path) .
                           " --query " . escapeshellarg($query_smiles) .
                           " --db_file " . escapeshellarg($database_path) .
                           " --threshold " . escapeshellarg($threshold);
                $json_output = shell_exec($command);
                $results = json_decode($json_output, true);
            ?>
                <!-- Query Info Box -->
                <div class="query-info">
                    <h5>Search Query</h5>
                    <p class="mb-1"><strong>Database:</strong> <?php echo htmlspecialchars($database_name); ?></p>
                    <p class="mb-1"><strong>SMILES:</strong></p>
                    <pre><?php echo htmlspecialchars($query_smiles); ?></pre>
                </div>

                <?php if (is_array($results) && !empty($results)): ?>
                    <h4 class="mt-4"><?php echo count($results); ?> hits found</h4>
                    <div class="data-table">
                        <table class="table table-hover">
                            <thead class="table-dark" style="position: sticky; top: 0;">
                                <tr>
                                    <th>S.No.</th>
                                    <th>Drug / Metabolite Name</th>
                                    <th>Associated Pathogen</th>
                                    <th>Tanimoto Similarity</th>
                                    <th>View Structure</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $hit_number = 1;
                                $tsv_data = "Match\tDrug_Name\tPathogen\tTanimoto_Similarity\tSMILES\n";
                                foreach ($results as $hit):
                                    $tsv_data .= "{$hit_number}\t{$hit['drug_name']}\t{$hit['pathogen']}\t{$hit['similarity']}\t{$hit['smiles']}\n";
                                ?>
                                    <tr class="data-row">
                                        <td><?php echo $hit_number; ?></td>
                                        <td><?php echo htmlspecialchars($hit['drug_name']); ?></td>
                                        <td><em><?php echo htmlspecialchars($hit['pathogen']); ?></em></td>
                                        <td><?php echo number_format($hit['similarity'], 3); ?></td>
                                        <td>
                                            <button class='view-structure-btn' onclick="toggleStructure('structure-row-<?php echo $hit_number; ?>')">
                                                <i class="bi bi-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class='structure-row' id='structure-row-<?php echo $hit_number; ?>'>
                                        <td colspan="5">
                                            <img src="data:image/png;base64,<?php echo $hit['image_base64']; ?>" 
                                                 alt="Structure of <?php echo htmlspecialchars($hit['drug_name']); ?>" 
                                                 class="structure-img">
                                        </td>
                                    </tr>
                                <?php
                                    $hit_number++;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php
                    $download_file_path = 'output/matched_results.tsv';
                    file_put_contents($download_file_path, $tsv_data);
                    ?>
                    <a href="<?php echo $download_file_path; ?>" download="matched_results.tsv" class="download-link">
                        <i class="bi bi-download"></i> Download Results (TSV)
                    </a>

                <?php else: ?>
                    <!--
                    ======================================================================
                     MODIFIED "NO RESULTS" BLOCK WITH CONDITIONAL BGC LIBRARY DISPLAY
                    ======================================================================
                    -->
                    <div class="alert alert-warning mt-4" role="alert">
                        <h4>No Matches Found</h4>
                        <p>No structures in the '<strong><?php echo htmlspecialchars($database_name); ?></strong>' database met the similarity threshold.</p>
                    </div>

                    <?php
                    // Check if the BGC database was the one searched
                    if ($database_name === 'BGC Secondary Metabolites'):
                    ?>
                        <hr class="my-4">
                        <h4 class="mt-4">Explore the BGC Secondary Metabolites Collection</h4>
                        <p>No direct matches were found with your query. However, the complete BGC library is provided below for further exploration and discovery.</p>
                        
                        <div class="data-table">
                            <table class="table table-hover">
                                <thead class="table-dark" style="position: sticky; top: 0;">
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Metabolite Name</th>
                                        <th>Associated Pathogen</th>
                                        <th>SMILES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Open the CSV file and display its contents
                                    if (file_exists($database_path) && ($handle = fopen($database_path, "r")) !== FALSE):
                                        $row_number = 1;
                                        fgetcsv($handle); // Skip the header row of the CSV file
                                        
                                        while (($data = fgetcsv($handle, 2000, ",")) !== FALSE):
                                            // Assuming CSV columns are: pathogen_id, DRUG NAME, SMILES
                                            $pathogen = htmlspecialchars($data[0]);
                                            $drug_name = htmlspecialchars($data[1]);
                                            $smiles = htmlspecialchars($data[2]);
                                    ?>
                                            <tr class="data-row">
                                                <td><?php echo $row_number; ?></td>
                                                <td><?php echo $drug_name; ?></td>
                                                <td><em><?php echo $pathogen; ?></em></td>
                                                <td><small style="word-break: break-all;"><?php echo $smiles; ?></small></td>
                                            </tr>
                                    <?php
                                            $row_number++;
                                        endwhile;
                                        fclose($handle);
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <a href="/anshu/arkbase/structure_search/database/bgc_known_structures.csv" download="bgc_secondary_metabolites_library.csv" class="download-link">
 					<i class="bi bi-download"></i> Download Full BGC Library (CSV)
				     </a>
				     
                    <?php endif; // End of the BGC library display condition ?>
                    
                <?php endif; // End of main results check ?>
            <?php endif; // End of input validation check ?>
        </div>
    </section>
</div>

<?php
// 3. Include the site footer.
include '/var/www/html/anshu/arkbase/footer.php';
?>