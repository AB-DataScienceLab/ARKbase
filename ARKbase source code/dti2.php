<?php 
include 'header.php'; 
include 'conn.php'; // Database connection

// Fetch data from database
try {
    // Adjust this query according to your actual database table structure
    $query = "SELECT
    Pathogen_name,	 
        interaction_id,
        Drugbank_ID,
       Target,
        Score,
        Drug_Name,
        Drug_Type,
        	InChIKey,
        	Protein_names,
        Gene_ID,
        Organism,
        Organism_ID,
        Pathway,
        GO_Biological_Process,
        GO_molecular_function,
        GO_cellular_component
    FROM dti_ab 
    ORDER BY score DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $dti_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert to JSON for JavaScript
    $dti_data_json = json_encode($dti_data);
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    $dti_data_json = '[]'; // Empty array as fallback
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug-Target Insights for Escherichia coli</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <!-- Cytoscape.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.23.0/cytoscape.min.js"></script>
    <script src="https://unpkg.com/popper.js@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js"></script>
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #fd7e14;
            --dark-color: #212529;
            --blue-color: #0d6efd;
            --green-color: #198754;
        }
        
        .section-title {
            color: var(--dark-color);
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            height: 450px;
        }
        
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .network-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            height: 500px;
        }
        
        #cy {
            width: 100%;
            height: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        #drug-image-popup {
            position: absolute;
            display: none;
            padding: 5px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        #drug-image-popup img {
            width: 150px;
            height: 150px;
        }
        
        .search-box {
            margin-bottom: 1rem;
        }
        
        .data-table {
            max-height: 400px;
            overflow-y: auto;
        }

        .data-table th:first-child, .data-table td:first-child {
            width: 30px; 
        }
        
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
        }
        
        .btn-download {
            background: linear-gradient(45deg, var(--success-color), #20c997);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 25px;
            color: white;
            font-weight: 500;
            transition: transform 0.2s;
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .organism-tag {
            font-style: italic;
            color: #e9ecef;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
    <br>
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1>Drug-Target Interactions for <em>Escherichia coli</em></h1>
                    <br>
                    <?php if(empty($dti_data)): ?>
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Database Status:</strong> 
                        <?php echo count($dti_data); ?> records loaded from database
                    </div>
                    <?php else: ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle"></i> 
                        <strong>Database Status:</strong> 
                        <?php echo count($dti_data); ?> records loaded successfully
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-end">
                    <button class="btn btn-download" onclick="downloadData()">
                        <i class="bi bi-download"></i> Download Full Data (.csv)
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <?php if(empty($dti_data)): ?>
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">No Data Available</h4>
            <p>No drug-target interaction data found in the database. Please check:</p>
            <ul>
                <li>Database connection</li>
                <li>Table name and structure</li>
                <li>Data availability</li>
            </ul>
            <hr>
            <p class="mb-0">Adjust the SQL query in this file to match your database schema.</p>
        </div>
        <?php else: ?>
        
        <!-- Overview Dashboard -->
        <section class="mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-container">
                        <h4 class="mb-3">Graphical Abstract</h4>
                        <canvas id="targetSummaryChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-container">
                        <h4 class="mb-3">GO Entries</h4>
                        <div class="btn-group mb-3" role="group" id="go-toggle-buttons">
                            <input type="radio" class="btn-check" name="go-type" id="btn-molecular" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="btn-molecular">Molecular Function</label>

                            <input type="radio" class="btn-check" name="go-type" id="btn-biological" autocomplete="off">
                            <label class="btn btn-outline-primary" for="btn-biological">Biological Process</label>

                            <input type="radio" class="btn-check" name="go-type" id="btn-cellular" autocomplete="off">
                            <label class="btn btn-outline-primary" for="btn-cellular">Cellular Component</label>
                        </div>
                        <canvas id="goChart"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Explorer -->
        <section class="mb-5">
            <h2 class="section-title">Interactive Explorer</h2>
            <div class="row">
                <div class="col-lg-7">
                    <div class="table-container">
                        <h4 class="mb-3">Searchable Data Table</h4>
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="Search by Target, Drug, Protein, etc.">
                        </div>
                        <div class="data-table">
                            <table class="table table-striped table-hover" id="dataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAllCheckbox"></th>
                                        <th>Target ID</th>
                                        <th>DrugBank ID</th>
                                        <th>Drug Name</th>
                                        <th>Score</th>
                                        <th>Drug Type</th>
                                        <th>Protein Names</th>
                                        <th>Pathway</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="loading-spinner">
                                                <div class="spinner-border" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="network-container">
                        <h4 class="mb-3">Target-Drug Interaction Network</h4>
                        <div id="cy"></div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
    </div>
    
    <!-- Floating image popup -->
    <div id="drug-image-popup"></div>

    <script>
        // Load data from PHP
        const dti_data = <?php echo $dti_data_json; ?>;
        
        // Global variables
        let filteredData = [...dti_data];
        let goChart;
        let summaryChart;
        let cy;

        // Color scheme
        const drugTypeColors = {
            'small molecule': 'var(--blue-color)',
            'peptide': 'var(--green-color)',
            'biotech': 'var(--warning-color)'
        };

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            if (dti_data.length > 0) {
                createTargetSummaryChart();
                createGoChart('molecular');
                setupGoToggle();
                initializeTable();
                createNetworkGraph();
                setupSearch();
                setupTableCheckboxes();
            } else {
                // Hide charts and show message if no data
                document.getElementById('tableBody').innerHTML = 
                    '<tr><td colspan="8" class="text-center text-muted">No data available from database</td></tr>';
            }
        });

        function createTargetSummaryChart() {
            const ctx = document.getElementById('targetSummaryChart').getContext('2d');
            
            const uniqueTargets = new Set(dti_data.map(d => d.Target));
            const uniqueDrugs = [...new Map(dti_data.map(item => [item.Drug_Name, item])).values()];
            
            const smallMoleculeCount = uniqueDrugs.filter(d => d.Drug_Type === 'small molecule').length;
            const peptideCount = uniqueDrugs.filter(d => d.Drug_Type === 'peptide').length;
            const biotechCount = uniqueDrugs.filter(d => d.Drug_Type === 'biotech').length;

            summaryChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Total Targets', 'Total Drugs'],
                    datasets: [
                        {
                            label: 'small molecule',
                            data: [null, smallMoleculeCount],
                            backgroundColor: drugTypeColors['small molecule'],
                        },
                        {
                            label: 'peptide',
                            data: [null, peptideCount],
                            backgroundColor: drugTypeColors['peptide'],
                        },
                        {
                            label: 'biotech',
                            data: [null, biotechCount],
                            backgroundColor: drugTypeColors['biotech'],
                        },
                        {
                            label: 'Total Targets',
                            data: [uniqueTargets.size, null],
                            backgroundColor: '#6c757d',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                filter: (legendItem, chartData) => legendItem.datasetIndex < 3
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'Total Targets') {
                                        return `Total Targets: ${context.parsed.y}`;
                                    }
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            stacked: true,
                            title: {
                                display: true,
                                text: 'Count'
                            }
                        }
                    },
                }
            });
        }

        function setupGoToggle() {
            document.getElementById('go-toggle-buttons').addEventListener('click', (event) => {
                if (event.target.tagName === 'LABEL') {
                    const buttonId = event.target.getAttribute('for');
                    let type = 'molecular';
                    if (buttonId === 'btn-biological') type = 'biological';
                    if (buttonId === 'btn-cellular') type = 'cellular';
                    createGoChart(type);
                }
            });
        }
        
        function createGoChart(goType) {
            const goColumnMap = {
                'molecular': 'Gene.Ontology.(molecular.function)',
                'biological': 'Gene.Ontology.(biological.process)',
                'cellular': 'Gene.Ontology.(cellular.component)'
            };
            const goColumn = goColumnMap[goType];

            const goTermCounts = {};
            dti_data.forEach(item => {
                const goTerms = item[goColumn] ? item[goColumn].split(';') : [];
                goTerms.forEach(term => {
                    const cleanTerm = term.trim().split(' [GO:')[0];
                    if (cleanTerm) {
                        goTermCounts[cleanTerm] = (goTermCounts[cleanTerm] || 0) + 1;
                    }
                });
            });

            const sortedTerms = Object.entries(goTermCounts)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10);
            
            const chartData = {
                labels: sortedTerms.map(([term, count]) => term),
                datasets: [{
                    label: 'Frequency',
                    data: sortedTerms.map(([term, count]) => count),
                    backgroundColor: '#6610f2',
                    borderColor: '#6610f2',
                    borderWidth: 1
                }]
            };

            const ctx = document.getElementById('goChart').getContext('2d');
            if (goChart) {
                goChart.data = chartData;
                goChart.options.scales.y.title.text = `GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Term`;
                goChart.update();
            } else {
                goChart = new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        scales: {
                            x: { title: { display: true, text: 'Frequency' } },
                            y: { title: { display: true, text: `GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Term` } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        }

        function initializeTable() {
            renderTable();
        }
        
        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = filteredData.map(item => `
                <tr data-interaction-id="${item.interaction_id}">
                    <td><input type="checkbox" class="row-checkbox" data-interaction-id="${item.interaction_id}"></td>
                    <td><a href="https://www.uniprot.org/uniprot/${item.Target}" target="_blank">${item.Target}</a></td>
                    <td><a href="https://go.drugbank.com/drugs/${item['Drugbank.ID']}" target="_blank">${item['Drugbank.ID']}</a></td>
                    <td>${item.Drug_Name}</td>
                    <td>${parseFloat(item.Score).toFixed(2)}</td>
                    <td><span class="badge" style="background-color: ${drugTypeColors[item.Drug_Type] || '#6c757d'}">${item.Drug_Type}</span></td>
                    <td>${item['Protein.names']}</td>
                    <td>${item.Pathway ? item.Pathway.replace('PATHWAY: ', '') : ''}</td>
                </tr>
            `).join('');
            
            document.querySelectorAll('#tableBody tr').forEach(row => {
                row.addEventListener('click', (e) => {
                    if (e.target.type === 'checkbox') return;
                    const interactionId = row.dataset.interactionId;
                    const interaction = dti_data.find(d => d.interaction_id === interactionId);
                    if (interaction) {
                        highlightNetworkEdge(interaction.Target, interaction.Drug_Name);
                    }
                });
            });
        }
        
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filteredData = dti_data.filter(item => 
                    (item.Target && item.Target.toLowerCase().includes(searchTerm)) ||
                    (item['Drugbank.ID'] && item['Drugbank.ID'].toLowerCase().includes(searchTerm)) ||
                    (item.Drug_Name && item.Drug_Name.toLowerCase().includes(searchTerm)) ||
                    (item['Protein.names'] && item['Protein.names'].toLowerCase().includes(searchTerm)) ||
                    (item.Pathway && item.Pathway.toLowerCase().includes(searchTerm))
                );
                renderTable();
                filterNetworkFromCheckboxes();
            });
        }
        
        function setupTableCheckboxes() {
            const table = document.getElementById('dataTable');
            const selectAll = document.getElementById('selectAllCheckbox');

            table.addEventListener('change', (e) => {
                if (e.target.matches('.row-checkbox') || e.target.matches('#selectAllCheckbox')) {
                    if (e.target === selectAll) {
                        document.querySelectorAll('.row-checkbox').forEach(cb => {
                            if (filteredData.some(d => d.interaction_id === cb.dataset.interactionId)) {
                                cb.checked = selectAll.checked;
                            }
                        });
                    }
                    filterNetworkFromCheckboxes();
                }
            });
        }

        function filterNetworkFromCheckboxes() {
            const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
            const selectedInteractionIds = Array.from(checkedBoxes).map(cb => cb.dataset.interactionId);

            if (selectedInteractionIds.length === 0) {
                if (cy) cy.elements().style({ 'display': 'element' });
            } else {
                const selectedElements = new Set();
                const selectedInteractions = dti_data.filter(d => selectedInteractionIds.includes(d.interaction_id));

                selectedInteractions.forEach(interaction => {
                    selectedElements.add(interaction.Target);
                    selectedElements.add(interaction.Drug_Name);
                });

                if (cy) {
                    cy.elements().forEach(el => {
                        if (el.isNode()) {
                            if (selectedElements.has(el.id())) {
                                el.style('display', 'element');
                            } else {
                                el.style('display', 'none');
                            }
                        } else if (el.isEdge()) {
                            const source = el.source().id();
                            const target = el.target().id();
                            const isSelected = selectedInteractions.some(i => 
                                (i.Target === source && i.Drug_Name === target) ||
                                (i.Target === target && i.Drug_Name === source)
                            );
                            if (isSelected) {
                                el.style('display', 'element');
                                el.source().style('display', 'element');
                                el.target().style('display', 'element');
                            } else {
                                el.style('display', 'none');
                            }
                        }
                    });
                }
            }
        }

        function createNetworkGraph() {
            const nodes = [];
            const edges = [];
            const addedNodes = new Set();
            const drugDataMap = new Map();

            dti_data.forEach(item => {
                drugDataMap.set(item.Drug_Name, { 
                    drugType: item.Drug_Type, 
                    imageUrl: item.drug_image_url || 'https://placehold.co/150x150/FFF/000?text=' + encodeURIComponent(item.Drug_Name)
                });
                
                if (!addedNodes.has(item.Target)) {
                    nodes.push({ data: { id: item.Target, label: item.Target, type: 'target' } });
                    addedNodes.add(item.Target);
                }
                if (!addedNodes.has(item.Drug_Name)) {
                    nodes.push({ data: { id: item.Drug_Name, label: item.Drug_Name, type: 'drug' } });
                    addedNodes.add(item.Drug_Name);
                }
                edges.push({ data: { id: item.interaction_id, source: item.Target, target: item.Drug_Name, score: item.Score } });
            });
            
            nodes.forEach(node => {
                if (node.data.type === 'drug') {
                    const drugInfo = drugDataMap.get(node.data.id);
                    node.data.drugType = drugInfo.drugType;
                    node.data.imageUrl = drugInfo.imageUrl;
                }
            });

            cy = cytoscape({
                container: document.getElementById('cy'),
                elements: { nodes, edges },
                style: [
                    { selector: 'node[type="target"]', style: { 'background-color': '#6c757d', 'label': 'data(label)', 'shape': 'ellipse' } },
                    { selector: 'node[type="drug"]', style: { 'background-color': ele => drugTypeColors[ele.data('drugType')] || '#999', 'label': 'data(label)', 'shape': 'rectangle' } },
                    { selector: 'edge', style: { 'width': ele => Math.max(1, parseFloat(ele.data('score')) * 4), 'line-color': '#ccc', 'curve-style': 'bezier' } },
                    { selector: '.highlighted', style: { 'background-color': '#ffc107', 'line-color': '#ffc107', 'transition-property': 'background-color, line-color', 'transition-duration': '0.3s' } }
                ],
                layout: { name: 'cose', animate: true, padding: 10 }
            });

            // Image pop-up on hover (simplified version)
            cy.on('mouseover', 'node[type="drug"]', function(evt) {
                const node = evt.target;
                console.log('Drug hovered:', node.id(), node.data('imageUrl'));
            });
        }
        
        function highlightNetworkEdge(targetId, drugName) {
            if (!cy) return;
            const edge = cy.elements(`edge[source="${targetId}"][target="${drugName}"], edge[source="${drugName}"][target="${targetId}"]`);
            const nodes = cy.getElementById(targetId).union(cy.getElementById(drugName));

            cy.elements().removeClass('highlighted');
            if (edge.length > 0 && nodes.length > 0) {
                nodes.addClass('highlighted');
                edge.addClass('highlighted');
                setTimeout(() => {
                    cy.elements().removeClass('highlighted');
                }, 3000);
            }
        }
        
        function downloadData() {
            if (dti_data.length === 0) {
                alert('No data available to download');
                return;
            }
            
            const headers = ['interaction_id', 'Drugbank.ID', 'Target', 'Score', 'Drug_Name', 'Drug_Type', 'Protein.names', 'Pathway'];
            const csvContent = [
                headers.join(','),
                ...dti_data.map(row => headers.map(field => `"${row[field] || ''}"`).join(','))
            ].join('\n');
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'escherichia_coli_dti_data.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>
<?php include 'footer.php'; ?>