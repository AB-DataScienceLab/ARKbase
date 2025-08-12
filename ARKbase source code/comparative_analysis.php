<?php
// --- STEP 1: PHP BLOCK TO FETCH DATA FOR THE ENTIRE PAGE ---

// --- Database Connection Details ---
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";

// --- NEW: Default Selections for Circos Plots on Page Load ---
$default_pathogen_1 = 'a_baumannii';
$default_pathogen_2 = 'e_coli';

// --- NEW: Default Selections for BLAST Form on Page Load ---
$default_query_category = 'amr';
$default_source_pathogen = 'a_baumannii';
$default_query_protein = 'UID_13';

// This array will hold the pathogen names for all dropdowns on the page.
$pathogens_list = [];
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
} else {
    $sql = "SELECT DISTINCT pathogen_name FROM protein_search ORDER BY pathogen_name ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $pathogens_list[] = $row['pathogen_name'];
        }
    }
    $conn->close();
}

/**
 * Helper function to format pathogen names for display.
 * e.g., 'a_baumannii' becomes 'A baumannii'
 */
function format_pathogen_name($slug) {
    return ucfirst(str_replace('_', ' ', $slug));
}

// --- MAPPING FOR CIRCOS PLOTS ---
$circos_plot_map = [
    'a_baumannii'    => 'a_baumannii_genome_circos.html',
    'e_coli'         => 'e_coli_genome_circos.html',
    'e_faecium'      => 'e_faecium_genome_circos.html',
    'h_influenzae'   => 'h_influenzae_genome_circos.html',
    'k_pneumoniae'   => 'k_pneumoniae_genome_circos.html',
    'n_gonorrhoeae'  => 'n_gonorrhoeae_genome_circos.html',
    'p_aeruginosa'   => 'p_aeruginosa_genome_circos.html',
    's_agalactiae'   => 's_agalactiae_genome_circos.html',
    's_aureus'       => 's_aureus_genome_circos.html',
    's_enterica'     => 's_enterica_genome_circos.html',
    's_flexneri'     => 's_flexneri_genome_circos.html',
    's_pneumoniae'   => 's_pneumoniae_genome_circos.html',
    's_pyogenes'     => 's_pyogenes_genome_map.html',
    's_sonnei'       => 's_sonnei_genome_circos.html'
];

$plot_base_path = '/anshu/arkbase/interactive_circos';

include 'header.php';
?>

<!-- Custom CSS for the page styling -->
<style>
    /* Styles for BLAST Form */
    .blast-form-section {
        border: 1px solid #ddd;
        border-radius: 0.25rem;
        padding: 2rem 1.5rem 1.5rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
    }
    .blast-form-section .section-title {
        position: absolute;
        top: -0.8em;
        left: 1rem;
        background-color: #fff;
        padding: 0 0.5rem;
        font-weight: 500;
        color: #495057;
    }
    .blast-form-section .form-label { font-weight: 500; }
    
    .checkbox-list-container {
        border: 1px solid #ced4da;
        border-radius: .25rem;
        height: 200px;
        overflow-y: auto;
        padding: 0.5rem;
        background-color: #fff;
    }
    .checkbox-list-container .form-check {
        margin-bottom: 0.3rem;
    }
    .checkbox-list-container.is-loading {
        opacity: 0.7;
        pointer-events: none;
    }
    
    /* Styles for Visualization Section */
    .visualization-section {
        border: 1px solid #e9ecef;
        background-color: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.25rem;
    }
    .pathogen-select option { font-style: italic; }
    .plot-container iframe {
        width: 100%;
        height: 600px;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    .plot-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 600px;
        color: #6c757d;
        background-color: #e9ecef;
        border: 2px dashed #ced4da;
        border-radius: .25rem;
        text-align: center;
        padding: 1rem;
    }
</style>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        
        <!-- =================================================================== -->
        <!-- SECTION 1: BLAST ANALYSIS                                           -->
        <!-- =================================================================== -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4 mt-2" style="font-weight: 600;"><center><i class="bi bi-shuffle" style="margin-right: 8px;"></i>Comparative Analysis Module</center></h3>
                <br>
                <h4><center><b>Compare Virulence/AMR/Drug Targets</b></center><h4>
            </div>
        </div>
        <form action="comparative_results.php" method="post" id="blast-form">
            <div class="blast-form-section">
                <div class="section-title">Step 1: Select Query Category</div>
                <div class="d-flex justify-content-center flex-wrap pt-2" id="query_category_group">
                     <div class="form-check form-check-inline mx-3"><input class="form-check-input" type="radio" name="query_category" id="cat-amr" value="amr" required <?php if ($default_query_category === 'amr') echo 'checked'; ?>><label class="form-check-label" for="cat-amr">AMR Proteins</label></div>
                    <div class="form-check form-check-inline mx-3"><input class="form-check-input" type="radio" name="query_category" id="cat-vf" value="vf" required <?php if ($default_query_category === 'vf') echo 'checked'; ?>><label class="form-check-label" for="cat-vf">Virulence Factor (VF) Proteins</label></div>
                    <div class="form-check form-check-inline mx-3"><input class="form-check-input" type="radio" name="query_category" id="cat-dt" value="dt" required <?php if ($default_query_category === 'dt') echo 'checked'; ?>><label class="form-check-label" for="cat-dt">Drug Target Proteins</label></div>
                </div>
            </div>
            <div class="blast-form-section">
                <div class="section-title">Step 2: Select Query Proteins</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="source_pathogen" class="form-label">Source Pathogen:</label>
                        <select id="source_pathogen" name="source_pathogen" class="form-select pathogen-select" required>
                            <option value="" selected disabled>-- Select a pathogen --</option>
                            <?php foreach ($pathogens_list as $pathogen_slug): ?>
                                <option value="<?php echo htmlspecialchars($pathogen_slug); ?>" <?php if ($pathogen_slug === $default_source_pathogen) echo 'selected'; ?>>
                                    <?php echo '<i>' . htmlspecialchars(format_pathogen_name($pathogen_slug)) . '</i>'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Query Proteins:</label>
                        <div id="query_proteins_checkbox_container" class="checkbox-list-container">
                            <div class="text-muted p-2">Please select a category and pathogen first</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="blast-form-section">
                <div class="section-title">Step 3: Select Target Organism(s) (Database)</div>
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <label for="target_pathogens_container" class="form-label">Search against the genome(s) of:</label>
                        <div id="target_pathogens_container" class="checkbox-list-container">
                            <?php if (empty($pathogens_list)): ?>
                                <div class="text-muted p-2">No target organisms available.</div>
                            <?php else: ?>
                                <?php foreach ($pathogens_list as $pathogen_slug): ?>
                                    <?php 
                                        $unique_id = 'target_pathogen_' . htmlspecialchars($pathogen_slug);
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="target_pathogen[]" value="<?php echo htmlspecialchars($pathogen_slug); ?>" id="<?php echo $unique_id; ?>">
                                        <label class="form-check-label" for="<?php echo $unique_id; ?>">
                                            <?php echo '<i>' . htmlspecialchars(format_pathogen_name($pathogen_slug)) . '</i>'; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <div id="target-pathogen-error" class="text-danger mt-1" style="font-size: 0.875em;"></div>
                    </div>
                </div>
            </div>
            <!-- RE-ADDED: Note about default BLAST parameters -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="text-muted small">
                        This analysis uses standard BLASTP default search criteria.
                        <a href="#" data-bs-toggle="tooltip" data-bs-title="This includes a default E-value of 10 and the BLOSUM62 scoring matrix.">
                            <i class="bi bi-info-circle"></i>
                        </a>
                    </p>
                    <button type="submit" class="btn btn-primary btn-lg px-5">Run Analysis</button>
                </div>
            </div>
        </form>

        <hr class="my-5">

        <!-- PRECOMPUTED RESULTS DOWNLOAD SECTION -->
        <div class="precomputed-results-section mb-5">
            <h3 class="text-center mb-3" style="font-weight: 600;">
                <i  style="margin-right: 8px;"></i>Download Precomputed BLAST Results
            </h3>
            <p class="text-center text-muted mb-4">
                These files contain the complete all-vs-all BLASTP analysis for each protein category, comparing every protein from one pathogen against all proteins from every other pathogen within the same category.
                <br>
                The analysis was performed using the following parameters: 
                <strong>E-value ≤ 1e-4</strong>, <strong>Percent Identity ≥ 35%</strong>, and <strong>Query Coverage ≥ 50%</strong>.
            </p>
            <div class="row text-center">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="p-4 border rounded bg-light h-100 d-flex flex-column justify-content-center">
                        <h5 class="mb-3">AMR Proteins</h5>
                        <a href="/anshu/arkbase/blast_precomputed/AMR_BLAST_Results_Filtered.xlsx" class="btn btn-success" download="AMR_BLAST_Results_Filtered.xlsx">
                            <i style="margin-right: 5px;"></i> Download AMR Results
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="p-4 border rounded bg-light h-100 d-flex flex-column justify-content-center">
                        <h5 class="mb-3">Virulence Factors (VF)</h5>
                        <a href="/anshu/arkbase/blast_precomputed/VF_BLAST_Results_Filtered.xlsx" class="btn btn-success" download="VF_BLAST_Results_Filtered.xlsx">
                            <i  style="margin-right: 5px;"></i> Download VF Results
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 mx-auto">
                    <div class="p-4 border rounded bg-light h-100 d-flex flex-column justify-content-center">
                        <h5 class="mb-3">Drug Targets</h5>
                        <a href="/anshu/arkbase/blast_precomputed/DT_BLAST_Results_Filtered.xlsx" class="btn btn-success" download="DT_BLAST_Results_Filtered.xlsx">
                            <i  style="margin-right: 5px;"></i> Download DT Results
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <hr class="my-5">

        <!-- CIRCOS PLOT SECTION -->
        <div class="visualization-section">
            <h3 class="text-center mb-4" style="font-weight: 600;">Interactive Genome Visualization</h3>
            <p class="text-center text-muted">Select two pathogens to compare their interactive genome maps (Circos Plots).</p>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label for="circos-pathogen-1" class="form-label"><b>Select Pathogen 1:</b></label>
                    <select id="circos-pathogen-1" class="form-select pathogen-select">
                        <option value="">-- Choose Pathogen 1 --</option>
                        <?php foreach ($pathogens_list as $pathogen_slug): ?>
                            <option value="<?php echo htmlspecialchars($pathogen_slug); ?>" <?php if ($pathogen_slug === $default_pathogen_1) echo 'selected'; ?>>
                                <?php echo htmlspecialchars(format_pathogen_name($pathogen_slug)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="circos-pathogen-2" class="form-label"><b>Select Pathogen 2:</b></label>
                    <select id="circos-pathogen-2" class="form-select pathogen-select">
                        <option value="">-- Choose Pathogen 2 --</option>
                         <?php foreach ($pathogens_list as $pathogen_slug): ?>
                            <option value="<?php echo htmlspecialchars($pathogen_slug); ?>" <?php if ($pathogen_slug === $default_pathogen_2) echo 'selected'; ?>>
                                <?php echo htmlspecialchars(format_pathogen_name($pathogen_slug)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="plot-area" class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div id="circos-plot-container-1" class="plot-container">
                        <?php
                        if (isset($circos_plot_map[$default_pathogen_1])) {
                            $plot_url = htmlspecialchars($plot_base_path . '/' . $circos_plot_map[$default_pathogen_1]);
                            $plot_name = htmlspecialchars(format_pathogen_name($default_pathogen_1));
                            echo "<iframe src='{$plot_url}' title='Interactive genome map for {$plot_name}'></iframe>";
                        } else {
                            echo '<div class="plot-placeholder"><span>Select Pathogen 1 to view its interactive genome map.</span></div>';
                        }
                        ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div id="circos-plot-container-2" class="plot-container">
                         <?php
                        if (isset($circos_plot_map[$default_pathogen_2])) {
                            $plot_url = htmlspecialchars($plot_base_path . '/' . $circos_plot_map[$default_pathogen_2]);
                            $plot_name = htmlspecialchars(format_pathogen_name($default_pathogen_2));
                            echo "<iframe src='{$plot_url}' title='Interactive genome map for {$plot_name}'></iframe>";
                        } else {
                            echo '<div class="plot-placeholder"><span>Select Pathogen 2 to view its interactive genome map.</span></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- BLAST FORM SCRIPT ---
    const blastForm = document.getElementById('blast-form');
    const categoryGroup = document.getElementById('query_category_group');
    const sourcePathogenSelect = document.getElementById('source_pathogen');
    const proteinContainer = document.getElementById('query_proteins_checkbox_container');
    const defaultProteinId = '<?php echo htmlspecialchars($default_query_protein); ?>';

    async function updateQueryProteins() {
        const selectedCategory = document.querySelector('input[name="query_category"]:checked');
        const category = selectedCategory ? selectedCategory.value : null;
        const pathogen = sourcePathogenSelect.value;

        if (!category || !pathogen) {
            proteinContainer.innerHTML = '<div class="text-muted p-2">Please select a category and pathogen first</div>';
            return;
        }

        proteinContainer.innerHTML = '<div class="text-muted p-2">Loading proteins...</div>';
        proteinContainer.classList.add('is-loading');

        try {
            const response = await fetch(`api/get_proteins.php?category=${category}&pathogen=${pathogen}`);
            if (!response.ok) throw new Error('Network response was not ok.');
            const proteins = await response.json();
            
            proteinContainer.innerHTML = ''; 

            if (proteins.length > 0) {
                proteins.forEach(protein => {
                    const uniqueId = `protein_id_${protein.id}`; 
                    
                    const wrapper = document.createElement('div');
                    wrapper.className = 'form-check';

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'form-check-input';
                    checkbox.name = 'query_proteins[]';
                    checkbox.value = protein.id;
                    checkbox.id = uniqueId;

                    const label = document.createElement('label');
                    label.className = 'form-check-label';
                    label.htmlFor = uniqueId;
                    label.textContent = `${protein.prot_id} - ${protein.name}`;

                    wrapper.appendChild(checkbox);
                    wrapper.appendChild(label);
                    proteinContainer.appendChild(wrapper);
                });
            } else {
                proteinContainer.innerHTML = '<div class="text-muted p-2">No proteins found for this selection.</div>';
            }
        } catch (error) {
            console.error('Fetch error:', error);
            proteinContainer.innerHTML = '<div class="text-danger p-2">Error loading proteins. Please try again.</div>';
        } finally {
            proteinContainer.classList.remove('is-loading'); 
        }
    }
    categoryGroup.addEventListener('change', updateQueryProteins);
    sourcePathogenSelect.addEventListener('change', updateQueryProteins);

    if (blastForm) {
        blastForm.addEventListener('submit', function(event) {
            const checkedTargets = document.querySelectorAll('input[name="target_pathogen[]"]:checked');
            const errorDiv = document.getElementById('target-pathogen-error');
            
            if (checkedTargets.length === 0) {
                event.preventDefault(); 
                errorDiv.textContent = 'Please select at least one target organism.';
            } else {
                errorDiv.textContent = ''; 
            }
        });
    }


    // --- CIRCOS PLOT VISUALIZATION SCRIPT (UNCHANGED) ---
    const circosPathogenSelect1 = document.getElementById('circos-pathogen-1');
    const circosPathogenSelect2 = document.getElementById('circos-pathogen-2');
    const plotContainer1 = document.getElementById('circos-plot-container-1');
    const plotContainer2 = document.getElementById('circos-plot-container-2');
    
    const circosPlotMap = <?php echo json_encode($circos_plot_map); ?>;
    const plotBasePath = <?php echo json_encode($plot_base_path); ?>;

    function updateCircosPlot(selectElement, plotContainer) {
        const pathogenSlug = selectElement.value;
        const placeholderText = plotContainer.id.includes('1') ? 'Pathogen 1' : 'Pathogen 2';
        if (pathogenSlug && circosPlotMap[pathogenSlug]) {
            const plotFilename = circosPlotMap[pathogenSlug];
            const plotUrl = `${plotBasePath}/${plotFilename}`;
            const plotName = format_pathogen_name(pathogenSlug);
            plotContainer.innerHTML = `<iframe src="${plotUrl}" title="Interactive genome map for ${plotName}"></iframe>`;
        } else if (pathogenSlug) {
            plotContainer.innerHTML = `<div class="plot-placeholder"><span>Sorry, an interactive genome map is not available for this selection.</span></div>`;
        } else {
            plotContainer.innerHTML = `<div class="plot-placeholder"><span>Select ${placeholderText} to view its interactive genome map.</span></div>`;
        }
    }

    function format_pathogen_name(slug) {
        if (!slug) return '';
        let str = slug.replace(/_/g, ' ');
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    circosPathogenSelect1.addEventListener('change', () => updateCircosPlot(circosPathogenSelect1, plotContainer1));
    circosPathogenSelect2.addEventListener('change', () => updateCircosPlot(circosPathogenSelect2, plotContainer2));

    
    // --- RE-ADDED: INITIALIZE BOOTSTRAP TOOLTIPS ---
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));


    // --- INITIALIZE FORM DEFAULTS ON PAGE LOAD ---
    async function initializeFormDefaults() {
        // Since the category and pathogen are pre-selected via PHP,
        // we call updateQueryProteins() to populate the list on page load.
        await updateQueryProteins();

        // After the protein list is fetched and rendered, find and check the default protein.
        if (defaultProteinId) {
            const defaultCheckbox = proteinContainer.querySelector(`input[name="query_proteins[]"][value="${defaultProteinId}"]`);
            if (defaultCheckbox) {
                defaultCheckbox.checked = true;
            } else {
                console.warn(`Default protein checkbox with value "${defaultProteinId}" not found after loading.`);
            }
        }
    }

    // Run the initialization logic once the DOM is ready.
    initializeFormDefaults();
});
</script>

<?php include 'footer.php'; ?>