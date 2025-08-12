<?php
include 'config.php';
include 'header.php';

// Get strain ID from URL parameter
$strain_id = isset($_GET['strain_id']) ? intval($_GET['strain_id']) : null;

// Fetch data from database
$pathogen_data = getPathogenData($strain_id);
$all_strains = getAllStrains();
$genome_stats = getGenomeStats();
$country_distribution = getCountryDistribution();
$isolation_sources = getIsolationSourceDistribution();

// If no data found and we have strains available, use first available strain
if (!$pathogen_data && !empty($all_strains)) {
    $strain_id = $all_strains[0]['id'];
    $pathogen_data = getPathogenData($strain_id);
}

// Handle case where no data exists at all
if (!$pathogen_data) {
    echo "<div class='error'>No data found in database. Please check your database connection and ensure data exists in the ab_data table.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="css/style2.css" type="text/css">
<link rel="stylesheet" href="css/style_tab.css" type="text/css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pathogen_data['strain_name'] ?? 'Acinetobacter baumannii'); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>

<body>
   <div class="container">
        <div class="header">
            <h1><em><?php echo htmlspecialchars($pathogen_data['strain_name'] ?? 'Acinetobacter baumannii'); ?></em></h1>
            
            <!-- Strain Selector -->
            <div class="strain-selector" style="margin-top: 10px;">
                <label for="strain-select">Select Strain: </label>
                <select id="strain-select" onchange="changeStrain(this.value)" style="padding: 5px; margin-left: 10px;">
                    <?php foreach ($all_strains as $strain): ?>
                        <option value="<?php echo $strain['id']; ?>" 
                                <?php echo ($strain['id'] == $strain_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($strain['strain_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="tabs-wrapper">
            <button class="scroll-btn left" onclick="scrollTabs('left')">‹</button>
            <button class="scroll-btn right" onclick="scrollTabs('right')">›</button>
            
            <div class="tabs-container">
                <div class="tabs">
                    <button class="tab-button active" onclick="showTab('overview')">Overview</button>
                    <button class="tab-button" onclick="showTab('genomes')">Genomes</button>
                    <button class="tab-button" onclick="showTab('genes-proteins')">Genes/Proteins</button>
                    <button class="tab-button" onclick="showTab('annotation')">Structural Annotation</button>
                    <button class="tab-button" onclick="showTab('drug-interactions')">Drug Target Interactions</button>
                    <button class="tab-button" onclick="showTab('host-pathogen')">Host-Pathogen Interactions</button>
                    <button class="tab-button" onclick="showTab('target')">Drug Target</button>
                    <button class="tab-button" onclick="showTab('gwas')">GWAS</button>
                    <button class="tab-button" onclick="showTab('ml-datasets')">ML Datasets</button>
                    <button class="tab-button" onclick="showTab('pan-amr')">Pan AMR</button>
                    <button class="tab-button" onclick="showTab('pan-virulence')">Pan Virulence</button>
                    <button class="tab-button" onclick="showTab('bgc')">BGC</button>
                </div>
            </div>
        </div>
        
        <div class="content">
            <div id="overview" class="tab-content active">
                <div class="dashboard-grid">
                    <div class="left-section">
                        <!-- Top row: Pathogen Overview and Genome Summary -->
                        <div class="card-row">
                            <div class="card tabular-style">
                                <h3><span class="icon"></span>Pathogen Overview</h3>
                                <div class="card-content">
                                    <table class="tabular-data">
                                        <tr><td>Organism Name:</td><td><em><?php echo htmlspecialchars($pathogen_data['strain_name'] ?? 'N/A'); ?></em></td></tr>
                                        <tr><td>Gram Stain:</td><td><?php echo htmlspecialchars($pathogen_data['gram_stain'] ?? 'N/A'); ?></td></tr>
                                        <tr><td>Taxon ID:</td><td><?php echo htmlspecialchars($pathogen_data['taxon_id'] ?? 'N/A'); ?></td></tr>
                                        <tr><td>Country of Isolation:</td><td><?php echo htmlspecialchars($pathogen_data['country_of_isolation'] ?? 'N/A'); ?></td></tr>
                                        <tr><td>Isolation Source:</td><td><?php echo htmlspecialchars($pathogen_data['isolation_source'] ?? 'N/A'); ?></td></tr>
                                    </table>
                                </div>
                            </div>

                            <div class="card tabular-style">
                                <h3><span class="icon"></span>Genome Summary</h3>
                                <div class="card-content">
                                    <table class="tabular-data">
                                        <tr><td>Assembly Accession:</td><td><?php echo htmlspecialchars($pathogen_data['assembly_accession'] ?? 'N/A'); ?></td></tr>
                                        <tr><td>Genome Size:</td><td><?php echo htmlspecialchars($pathogen_data['genome_size_mb'] ?? 'N/A'); ?> Mb</td></tr>
                                        <tr><td>GC Content:</td><td><?php echo htmlspecialchars($pathogen_data['gc_content_percent'] ?? 'N/A'); ?>%</td></tr>
                                        <tr><td>Total Genes:</td><td><?php echo htmlspecialchars(number_format($pathogen_data['total_genes'] ?? 0)); ?></td></tr>
                                        <tr><td>Protein Coding Genes:</td><td><?php echo htmlspecialchars(number_format($pathogen_data['protein_coding_genes'] ?? 0)); ?></td></tr>
                                        <tr><td>tRNA:</td><td><?php echo htmlspecialchars($pathogen_data['trna'] ?? 'N/A'); ?></td></tr>
                                        <tr><td>rRNA:</td><td><?php echo htmlspecialchars($pathogen_data['rrna'] ?? 'N/A'); ?></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Phenotype Chart and MLST Table -->
                        <div class="card-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
                            <div class="card" style="flex: 1;">
                                <h3><span class="icon"></span>Country Distribution</h3>
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="countryChart"></canvas>
                                </div>
                            </div>

                            <div class="card" style="flex: 2;">
                                <h3><span class="icon"></span>Database Statistics</h3>
                                <div class="card-content">
                                    <table class="mlst-table">
                                        <tr>
                                            <td>Total Genomes:</td>
                                            <td><?php echo number_format($genome_stats['total_genomes'] ?? 0); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Average Genome Size:</td>
                                            <td><?php echo number_format($genome_stats['avg_genome_size'] ?? 0, 2); ?> Mb</td>
                                        </tr>
                                        <tr>
                                            <td>Average GC Content:</td>
                                            <td><?php echo number_format($genome_stats['avg_gc_content'] ?? 0, 1); ?>%</td>
                                        </tr>
                                        <tr>
                                            <td>Average Total Genes:</td>
                                            <td><?php echo number_format($genome_stats['avg_total_genes'] ?? 0); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Genome Size Range:</td>
                                            <td><?php echo number_format($genome_stats['min_genome_size'] ?? 0, 2); ?> - <?php echo number_format($genome_stats['max_genome_size'] ?? 0, 2); ?> Mb</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Isolation Source Distribution -->
                        <div class="card full-width-card">
                            <h3><span class="icon"></span>Isolation Source Distribution</h3>
                            <div class="chart-container" style="height: 400px;">
                                <canvas id="isolationSourceChart"></canvas>
                            </div>
                        </div>

                        <!-- Circos placeholder -->
                        <div class="card full-width-card">
                            <h3><span class="icon"></span>Circos Plot</h3>
                            <div class="circos-placeholder">
                                Interactive Circos Plot - Genome Visualization for <?php echo htmlspecialchars($pathogen_data['strain_name'] ?? 'Selected Strain'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="right-section">
                        <div class="card tabular-style">
                            <h3><span class="icon"></span>Current Strain Details</h3>
                            <div class="card-content">
                                <table class="tabular-data">
                                    <tr><td>Genome Size:</td><td><?php echo htmlspecialchars($pathogen_data['genome_size_mb'] ?? 'N/A'); ?> Mb</td></tr>
                                    <tr><td>GC Content:</td><td><?php echo htmlspecialchars($pathogen_data['gc_content_percent'] ?? 'N/A'); ?>%</td></tr>
                                    <tr><td>Total Genes:</td><td><?php echo htmlspecialchars(number_format($pathogen_data['total_genes'] ?? 0)); ?></td></tr>
                                    <tr><td>Protein Genes:</td><td><?php echo htmlspecialchars(number_format($pathogen_data['protein_coding_genes'] ?? 0)); ?></td></tr>
                                    <tr><td>tRNAs:</td><td><?php echo htmlspecialchars($pathogen_data['trna'] ?? 'N/A'); ?></td></tr>
                                    <tr><td>rRNAs:</td><td><?php echo htmlspecialchars($pathogen_data['rrna'] ?? 'N/A'); ?></td></tr>
                                    <tr><td>Last Updated:</td><td><?php echo date('F Y'); ?></td></tr>
                                </table>
                            </div>
                        </div>

                        <!-- Pan Genome Distribution -->
                        <div class="card">
                            <h3><span class="icon"></span>Gene Distribution</h3>
                            <div class="chart-container">
                                <canvas id="geneDistributionChart"></canvas>
                            </div>
                        </div>

                        <!-- Recent Publications -->
                        <div class="card">
                            <h3><span class="icon"></span>Recent Publications</h3>
                            <div id="publications-container" class="publications-feed">
                                <div class="loading">
                                    <div class="loading-spinner"></div>
                                    Loading recent publications...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other tab contents -->
            <div id="genomes" class="tab-content" style="display: none;">
                <h2>Genomes Section</h2>
                <p>Genome analysis content will be displayed here.</p>
            </div>

            <div id="genes-proteins" class="tab-content" style="display: none;">
                <h2>Genes/Proteins Section</h2>
                <p>Genes and proteins analysis content will be displayed here.</p>
            </div>

            <div id="annotation" class="tab-content" style="display: none;">
                <h2>Structural Annotation Section</h2>
                <p>Structural annotation content will be displayed here.</p>
            </div>

            <div id="drug-interactions" class="tab-content" style="display: none;">
                <h2>Drug Target Interactions Section</h2>
                <p>Drug interaction analysis content will be displayed here.</p>
            </div>

            <div id="host-pathogen" class="tab-content" style="display: none;">
                <h2>Host-Pathogen Interactions Section</h2>
                <p>Host-pathogen interaction analysis content will be displayed here.</p>
            </div>

            <div id="target" class="tab-content" style="display: none;">
                <h2>Drug Target Section</h2>
                <p>Target identification content will be displayed here.</p>
            </div>

            <div id="gwas" class="tab-content" style="display: none;">
                <h2>GWAS Section</h2>
                <p>GWAS analysis content will be displayed here.</p>
            </div>

            <div id="ml-datasets" class="tab-content" style="display: none;">
                <h2>ML Datasets Section</h2>
                <p>Machine learning datasets content will be displayed here.</p>
            </div>

            <div id="pan-amr" class="tab-content" style="display: none;">
                <h2>Pan AMR Section</h2>
                <p>Pan AMR analysis content will be displayed here.</p>
            </div>

            <div id="pan-virulence" class="tab-content" style="display: none;">
                <h2>Pan Virulence Section</h2>
                <p>Pan virulence analysis content will be displayed here.</p>
            </div>

            <div id="bgc" class="tab-content" style="display: none;">
                <h2>BGC Section</h2>
                <p>BGC analysis content will be displayed here.</p>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const countryData = <?php echo json_encode($country_distribution); ?>;
        const isolationSourceData = <?php echo json_encode($isolation_sources); ?>;
        const currentStrain = <?php echo json_encode($pathogen_data); ?>;

        // Function to change strain
        function changeStrain(strainId) {
            window.location.href = '?strain_id=' + strainId;
        }

        // Tab switching functionality
        function showTab(tabName) {
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(tab => {
                tab.style.display = 'none';
                tab.classList.remove('active');
            });

            const tabButtons = document.querySelectorAll('.tab-button');
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });

            const selectedTab = document.getElementById(tabName);
            if (selectedTab) {
                selectedTab.style.display = 'block';
                selectedTab.classList.add('active');
            }

            event.target.classList.add('active');

            if (tabName === 'overview') {
                setTimeout(() => {
                    initializeCharts();
                }, 100);
            }
        }

        // Scroll tabs function
        function scrollTabs(direction) {
            const tabsContainer = document.querySelector('.tabs');
            const scrollAmount = 200;
            
            if (direction === 'left') {
                tabsContainer.scrollLeft -= scrollAmount;
            } else {
                tabsContainer.scrollLeft += scrollAmount;
            }
        }

        // Chart initialization
        function initializeCharts() {
            initializeCountryChart();
            initializeIsolationSourceChart();
            initializeGeneDistributionChart();
        }

        function initializeCountryChart() {
            const ctx = document.getElementById('countryChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: countryData.map(item => item.country_of_isolation),
                    datasets: [{
                        data: countryData.map(item => item.count),
                        backgroundColor: [
                            '#e74c3c', '#3498db', '#2ecc71', '#f39c12', 
                            '#9b59b6', '#1abc9c', '#34495e', '#e67e22'
                        ],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: { size: 11, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    }
                }
            });
        }

        function initializeIsolationSourceChart() {
            const ctx = document.getElementById('isolationSourceChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: isolationSourceData.map(item => item.isolation_source),
                    datasets: [{
                        label: 'Number of Strains',
                        data: isolationSourceData.map(item => item.count),
                        backgroundColor: '#3498db',
                        borderColor: '#2980b9',
                        borderWidth: 2,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { font: { weight: '600' } }
                        },
                        x: {
                            ticks: { font: { weight: '600' } }
                        }
                    }
                }
            });
        }

        function initializeGeneDistributionChart() {
            const ctx = document.getElementById('geneDistributionChart');
            if (!ctx) return;

            const proteinGenes = currentStrain.protein_coding_genes || 0;
            const trnaGenes = currentStrain.trna || 0;
            const rrnaGenes = currentStrain.rrna || 0;
            const otherGenes = (currentStrain.total_genes || 0) - proteinGenes - trnaGenes - rrnaGenes;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Protein Coding', 'tRNA', 'rRNA', 'Other'],
                    datasets: [{
                        data: [proteinGenes, trnaGenes, rrnaGenes, otherGenes],
                        backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c'],
                        borderWidth: 3,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: { size: 11, weight: '600' }
                            }
                        }
                    }
                }
            });
        }

        // Fetch publications (simulated)
        async function fetchPubMedFeed() {
            const publicationsContainer = document.getElementById('publications-container');
            
            setTimeout(() => {
                const mockPublications = [
                    {
                        title: "Novel antimicrobial resistance mechanisms in Acinetobacter baumannii: A comprehensive genomic analysis",
                        authors: "Smith J, Johnson A, Brown K",
                        date: "2025-06-10",
                        pmid: "39234567"
                    },
                    {
                        title: "Machine learning approaches for predicting virulence in carbapenem-resistant Acinetobacter baumannii",
                        authors: "Davis L, Wilson R, Martinez C",
                        date: "2025-06-08",
                        pmid: "39234566"
                    },
                    {
                        title: "Pan-genome analysis reveals novel drug targets in multidrug-resistant Acinetobacter baumannii",
                        authors: "Garcia M, Thompson S, Lee H",
                        date: "2025-06-05",
                        pmid: "39234565"
                    }
                ];

                publicationsContainer.innerHTML = mockPublications.map(pub => `
                    <div class="publication-item">
                        <div class="publication-title">${pub.title}</div>
                        <div class="publication-authors">${pub.authors}</div>
                        <div class="publication-date">Published: ${pub.date} | PMID: ${pub.pmid}</div>
                    </div>
                `).join('');
            }, 1500);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetchPubMedFeed();
            initializeCharts();
        });

        // Add interactive effects
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
<?php include 'footer.php'; ?>
</html>