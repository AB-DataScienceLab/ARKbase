<!-- <HPI_wireframe> -->

<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprehensive Host-Pathogen Interaction Insights</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.23.0/cytoscape.min.js"></script>

    <style>
        :root { --primary-color: #1269e3; --danger-color: #dc3545; --dark-color: #212529; --light-gray:#0579ed; }
        body { font-family: 'Arial'; background-color: var(--light-gray); }
      
        .section-title { color: var(--dark-color); border-bottom: 3px solid var(--primary-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
        .viz-container, .table-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 2rem; display: flex; flex-direction: column; }
        .viz-container { height: 550px; }
        .table-container { height: 750px; }
        .graphical-abstract-placeholder { flex-grow: 1; display: flex; align-items: center; justify-content: center; text-align: center; color: #6c757d; font-style: italic; }
        #goChart { flex-grow: 1; height: calc(100% - 80px); }
        #cy { width: 100%; flex-grow: 1; border: 1px solid #ddd; border-radius: 5px; min-height: 400px; }
        .data-table { max-height: 600px; overflow-y: auto; flex-grow: 1; }
        .pubmed-link { margin-right: 5px; display: inline-block; }
        #go-toggle-buttons .btn { flex-grow: 1; }
        .filter-container { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 1rem; }
        #cog-checkbox-area { max-height: 100px; overflow-y: auto; } /* Style for COG filter */
        .pagination-controls { padding-top: 1rem; }
        .loader { border: 5px solid #f3f3f3; border-radius: 50%; border-top: 5px solid var(--primary-color); width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .loader-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.7); display: flex; align-items: center; justify-content: center; z-index: 10; border-radius: 10px; }
        .viz-container { background-color: #f9f9f9; border: 1px solid #ddd; padding: 15px; height: 550px; display: flex; flex-direction: column; justify-content: flex-start; }
        .chart-wrapper { flex-grow: 1; position: relative; height: calc(100% - 100px); margin-top: 10px; }
        #goChart { width: 100% !important; height: 100% !important; }
        #go-toggle-buttons .btn-check:checked + .btn { background-color: #2075f5; border-color: #28a745; color: white; }
        #go-toggle-buttons .btn:hover { background-color: #e9f5ec; }
        #hpiTable thead th { text-align: center; vertical-align: middle; }
    </style>
</head>
<body>
   <header class="header bg-light py-3">
        <div class="container text-center">
            <h2 class="m-0">Host-Pathogen Interaction</h2>
        </div>
    </header>

    <div class="container-fluid px-4">
        <section class="mb-5">
            <div class="row">
                <div class="col-lg-7">
                    <div class="viz-container">
                        <div class="graphical-abstract-placeholder">
                            <img src="https://datascience.imtech.res.in/anshu/arkbase/images/ga/6-ARKbase.jpeg" alt="Graphical Abstract" class="img-fluid" style="max-height: 100%; object-fit: contain;">
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="viz-container position-relative">
                        <div class="loader-overlay d-none" id="go-loader"><div class="loader"></div></div>
                        <h5 class="text-center mb-3">Top 10 Pathogen Gene Ontology Terms</h5>
                        <div class="btn-group w-100 mb-2" role="group" id="go-toggle-buttons">
                            <input type="radio" class="btn-check" name="go-type" id="btn-biological" autocomplete="off" checked>
                            <label class="btn btn-outline-secondary btn-sm" for="btn-biological">Biological Process</label>
                            <input type="radio" class="btn-check" name="go-type" id="btn-molecular" autocomplete="off">
                            <label class="btn btn-outline-secondary btn-sm" for="btn-molecular">Molecular Function</label>
                            <input type="radio" class="btn-check" name="go-type" id="btn-cellular" autocomplete="off">
                            <label class="btn btn-outline-secondary btn-sm" for="btn-cellular">Cellular Component</label>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="goChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mb-5">
            <h2 class="section-title">Interactive Data Explorer</h2>
            <div class="row">
                <div class="col-lg-7">
                    <div class="table-container position-relative">
                        <div class="loader-overlay d-none" id="table-loader"><div class="loader"></div></div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Host-Pathogen Interaction Details</h4>
                            <button class="btn btn-sm btn-success" onclick="downloadData()">
                                <i class="bi bi-download"></i> Download
                            </button>
                        </div>

                        <div id="pathogen-filter-container" class="filter-container mb-3">
                            <p class="fw-bold mb-2">Filter by Pathogen Organism:</p>
                            <div id="pathogen-checkbox-area"></div>
                        </div>
                        <input type="text" class="form-control mb-3" id="searchInput" placeholder="Search across key columns...">
                        <div class="data-table">
                            <table class="table table-striped table-hover" id="hpiTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Host Protein (ID)</th>
                                        <th>Host Protein (Name)</th>
                                        <th>Pathogen Target (ID)</th>
                                        <th>Pathogen Protein (Name)</th>
                                        <th>Databases</th>
                                        <th>Methods</th>
                                        <th>PubMed IDs</th>
                                        <th>Pathogen Organism</th>
                                        <th>COG Category</th>
                                        <th>Host Protein Degree</th>
                                        <th>Pathogen Protein Degree</th>
                                        <th>Reference Protein ID</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pagination-controls">
                            <div id="pagination-info"></div>
                            <nav><ul class="pagination mb-0" id="pagination-nav"></ul></nav>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="table-container">
                        <h4 class="mb-3">Interaction Network</h4>
                        <div id="cog-filter-container" class="filter-container mb-3">
                            <p class="fw-bold mb-2">Filter Network by COG Category:</p>
                            <div id="cog-checkbox-area">
                                <span class="text-muted small">Loading categories...</span>
                            </div>
                        </div>
                        <div id="cy"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        let goChart, cy, searchDebounce;
        const API_URL = 'fetch_hpi_data.php';
        const PAGE_LIMIT = 50; 
        const state = { currentPage: 1, searchTerm: '', selectedPathogens: ['Escherichia coli'], selectedCogs: [], totalPages: 1, totalRecords: 0 };
        
        document.addEventListener('DOMContentLoaded', () => { setupPathogenFilter(); setupCogFilter(); setupEventListeners(); updateGoChart('biological'); fetchHpiData(); });

        async function fetchHpiData() {
            showLoader('table-loader', true);
            const params = new URLSearchParams({ 
                page: state.currentPage, 
                limit: PAGE_LIMIT, 
                search: state.searchTerm, 
                pathogens: state.selectedPathogens.join(','),
                cogs: state.selectedCogs.join(',')
            });
            try {
                const response = await fetch(`${API_URL}?${params.toString()}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();
                if (result.error) throw new Error(`Backend error: ${result.error}`);
                state.totalPages = result.totalPages;
                state.totalRecords = result.totalRecords;
                renderTable(result.data);
                renderPagination();
                createNetworkGraph(result.data);
            } catch (error) {
                console.error("Error fetching HPI data:", error);
                document.getElementById('tableBody').innerHTML = `<tr><td colspan="13" class="text-center text-danger">Failed to load HPI data.</td></tr>`;
                document.getElementById('cy').innerHTML = '<div class="graphical-abstract-placeholder text-danger">Failed to load network data.</div>';
            } finally {
                showLoader('table-loader', false);
            }
        }
        
        function setupEventListeners() {
            document.getElementById('searchInput').addEventListener('input', (e) => { clearTimeout(searchDebounce); searchDebounce = setTimeout(() => { state.searchTerm = e.target.value; state.currentPage = 1; fetchHpiData(); }, 400); });
            document.getElementById('pathogen-filter-container').addEventListener('change', () => { state.selectedPathogens = Array.from(document.querySelectorAll('#pathogen-checkbox-area .form-check-input:checked')).map(cb => cb.value); state.currentPage = 1; fetchHpiData(); });
            document.getElementById('cog-filter-container').addEventListener('change', () => {
                state.selectedCogs = Array.from(document.querySelectorAll('#cog-checkbox-area .form-check-input:checked')).map(cb => cb.value);
                state.currentPage = 1;
                fetchHpiData();
            });
            document.getElementById('go-toggle-buttons').addEventListener('click', (event) => { if (event.target.tagName === 'LABEL') { const buttonId = event.target.getAttribute('for'); let type = 'biological'; if (buttonId === 'btn-molecular') type = 'molecular'; else if (buttonId === 'btn-cellular') type = 'cellular'; updateGoChart(type); } });
        }
        
        function handlePageChange(newPage) { if (newPage > 0 && newPage <= state.totalPages) { state.currentPage = newPage; fetchHpiData(); } }
        
        function setupPathogenFilter() {
            const checkboxArea = document.getElementById('pathogen-checkbox-area');
            const specificPathogens = ['Escherichia coli', 'Haemophilus influenzae', 'Klebsiella pneumoniae', 'Neisseria gonorrhoeae', 'Pseudomonas aeruginosa', 'Salmonella typhimurium', 'Shigella flexneri', 'Staphylococcus aureus', 'Streptococcus agalactiae', 'Streptococcus pyogenes'];
            checkboxArea.innerHTML = specificPathogens.map(pathogen => {
                const id = `check-${pathogen.replace(/[^a-zA-Z0-9]/g, '-')}`;
                const isChecked = pathogen === 'Escherichia coli' ? 'checked' : '';
                return `<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="${pathogen}" id="${id}" ${isChecked}><label class="form-check-label small" for="${id}">${pathogen}</label></div>`;
            }).join('');
        }

        // *** MODIFICATION START: Updated function to display full COG descriptions ***
        async function setupCogFilter() {
            const checkboxArea = document.getElementById('cog-checkbox-area');
            try {
                const response = await fetch(`${API_URL}?get_cog_categories=true`);
                const categories = await response.json(); // Now receives an array of objects
                if (categories.error) throw new Error(categories.error);

                if(categories.length > 0) {
                    // Use the `code` for the value and the `description` for the label
                    checkboxArea.innerHTML = categories.map(cog => {
                        const id = `cog-${cog.code}`;
                        const labelText = `[${cog.code}] ${cog.description}`;
                        
                        return `<div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="${cog.code}" id="${id}">
                                    <label class="form-check-label small" for="${id}" title="${cog.description}">
                                        ${labelText}
                                    </label>
                                </div>`;
                    }).join('');
                } else {
                    checkboxArea.innerHTML = '<span class="text-muted small">No COG categories found.</span>';
                }
            } catch (error) {
                console.error("Error fetching COG categories:", error);
                checkboxArea.innerHTML = '<span class="text-danger small">Failed to load COG categories.</span>';
            }
        }
        // *** MODIFICATION END ***
        
        function renderTable(data) {
            // ... (This function remains unchanged)
            const tbody = document.getElementById('tableBody');
            if (data.length === 0) { tbody.innerHTML = `<tr><td colspan="13" class="text-center">No interactions match the current filters.</td></tr>`; return; }
            const startingSNo = (state.currentPage - 1) * PAGE_LIMIT;
            tbody.innerHTML = data.map((item, index) => {
                const serialNumber = startingSNo + index + 1;
                return `<tr><td>${serialNumber}</td><td>${item.Host_Protein ? `<a href="https://www.uniprot.org/uniprot/${item.Host_Protein}" target="_blank">${item.Host_Protein}</a>` : 'N/A'}</td><td>${item.Host_Protein_name || 'N/A'}</td><td>${item.Pathogen_target ? `<a href="https://www.uniprot.org/uniprot/${item.Pathogen_target}" target="_blank">${item.Pathogen_target}</a>` : 'N/A'}</td><td>${item.Pathogen_protein || 'N/A'}</td><td>${String(item.Source_Databases || 'N/A').replace(/"/g, '')}</td><td>${item.Experimental_Methods_Agg || 'N/A'}</td><td>${formatPubMedLinks(item.PubMed_IDs_Agg)}</td><td><em>${item.Pathogen_Organism || 'N/A'}</em></td><td>${item.COG_category || 'N/A'}</td><td>${item.Host_Protein_Degree || 'N/A'}</td><td>${item.Pathogen_Protein_Degree || 'N/A'}</td><td>${item.Reference_Protein_ID || 'N/A'}</td></tr>`;
            }).join('');
        }

        function renderPagination() {
            // ... (This function remains unchanged)
            document.getElementById('pagination-info').textContent = `Showing page ${state.currentPage} of ${state.totalPages} (${state.totalRecords} total records)`;
            const nav = document.getElementById('pagination-nav');
            nav.innerHTML = `<li class="page-item ${state.currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="handlePageChange(${state.currentPage - 1})">Previous</a></li><li class="page-item disabled"><span class="page-link">${state.currentPage}</span></li><li class="page-item ${state.currentPage === state.totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="handlePageChange(${state.currentPage + 1})">Next</a></li>`;
        }
        
        function createNetworkGraph(interactionData) {
            // ... (This function remains unchanged)
            const cyContainer = document.getElementById('cy');
            cyContainer.innerHTML = '';
            if (!interactionData || interactionData.length === 0) { cyContainer.innerHTML = '<div class="graphical-abstract-placeholder">No interactions to display for the current filters.</div>'; return; }
            const nodes = [], edges = [], addedNodes = new Set();
            interactionData.forEach(interaction => {
                const hostId = interaction.Host_Protein;
                const pathogenId = interaction.Pathogen_target;
                if (!hostId || !pathogenId) return;
                if (!addedNodes.has(hostId)) { nodes.push({ data: { id: hostId, type: 'host' } }); addedNodes.add(hostId); }
                if (!addedNodes.has(pathogenId)) { nodes.push({ data: { id: pathogenId, type: 'pathogen' } }); addedNodes.add(pathogenId); }
                edges.push({ data: { source: hostId, target: pathogenId, weight: parseConfidenceScore(interaction.Confidence_Scores_Agg) } });
            });
            if (cy) cy.destroy();
            cy = cytoscape({
                container: cyContainer, elements: { nodes, edges },
                style: [ { selector: 'node', style: { 'label': 'data(id)', 'font-size': '9px', 'text-valign': 'center', 'color': '#fff', 'text-outline-width': 2, 'text-outline-color': '#333', 'min-zoomed-font-size': 7, 'width': '40px', 'height': '40px' } }, { selector: 'node[type="host"]', style: { 'background-color': '#2E8B57', 'shape': 'ellipse' } }, { selector: 'node[type="pathogen"]', style: { 'background-color': '#DC143C', 'shape': 'rectangle' } }, { selector: 'edge', style: { 'width': ele => Math.max(1.5, ele.data('weight') * 5), 'line-color': '#888', 'curve-style': 'bezier', 'opacity': ele => Math.max(0.3, ele.data('weight')) } }, ],
                layout: { name: 'cose', animate: false, padding: 30, idealEdgeLength: 100, nodeRepulsion: 4000 }
            });
        }
        
        async function updateGoChart(goType) {
            // ... (This function remains unchanged)
            showLoader('go-loader', true);
            try {
                const response = await fetch(`${API_URL}?get_go_stats=${goType}`);
                const result = await response.json();
                if (result.error) throw new Error(result.error);
                const chartData = { labels: result.labels, datasets: [{ label: 'Frequency', data: result.data, backgroundColor: '#A52A2A' }]};
                const ctx = document.getElementById('goChart').getContext('2d');
                if (goChart) goChart.destroy();
                goChart = new Chart(ctx, { type: 'bar', data: chartData, options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, title: { display: true, text: 'Frequency' } } } }});
            } catch(error) { console.error(`Failed to update GO chart: ${error}`); } finally { showLoader('go-loader', false); }
        }
        
        function showLoader(id, show) { document.getElementById(id).classList.toggle('d-none', !show); }
        function parseConfidenceScore(scoreString) { if (!scoreString || scoreString === 'NA') return 0.1; const match = String(scoreString).match(/(\d+(\.\d+)?)/); return match ? parseFloat(match[1]) : 0.1; }
        function formatPubMedLinks(pmids) { if (!pmids || pmids === 'NA') return 'N/A'; const ids = String(pmids).replace(/"/g, '').split(/[|;]/).map(id => id.trim()).filter(Boolean); return ids.map(idString => { const pubmedMatch = idString.match(/pubmed:(\d+)/); if (pubmedMatch) return `<a href="https://pubmed.ncbi.nlm.nih.gov/${pubmedMatch[1]}" target="_blank">${pubmedMatch[1]}</a>`; if (/^\d+$/.test(idString)) return `<a href="https://pubmed.ncbi.nlm.nih.gov/${idString}" target="_blank">${idString}</a>`; return `<span>${idString}</span>`; }).join(' ') || 'N/A'; }

        function downloadData() {
            // ... (This function remains unchanged)
            if (state.totalRecords === 0) { alert("There is no data to download based on the current filters."); return; }
            const params = new URLSearchParams({ 
                download: 'true', 
                search: state.searchTerm, 
                pathogens: state.selectedPathogens.join(','),
                cogs: state.selectedCogs.join(',')
            });
            window.location.href = `${API_URL}?${params.toString()}`;
        }
    </script>
</body>
<?php include 'footer.php'; ?>
</html>