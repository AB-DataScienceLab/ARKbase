<?php
include 'header.php';
// STEP 1: Get pathogen name from URL. Default to E. coli if not set.
$pathogenName = isset($_GET['pathogen']) ? htmlspecialchars($_GET['pathogen']) : 'Escherichia_coli';
// Create a display-friendly name by replacing underscores with spaces
$pathogenDisplayName = str_replace('_', ' ', $pathogenName);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Title is now dynamic -->
    <title>Drug-Target Insights for <?php echo $pathogenDisplayName; ?></title>

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
        /* All your CSS styles remain unchanged here... */
        /* ... */
        .header {
             position: relative; 
             z-index: 1050; 
        }
         :root {
       
            
        }

        body {
            font-family: 'Roboto';
          
        }
         .section-title {
            color: var(--dark-color);
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        /* --- Alignment for Top Row --- */
        .chart-container, .abstract-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            height: 550px; 
            display: flex;      
            flex-direction: column; 
        }

        .abstract-content {
             overflow-y: auto; 
             margin-bottom: 1rem;
        }
        
        .graphical-abstract-placeholder {
             flex-grow: 1; 
             background-color: #e9ecef;
             border: 1px dashed #ccc;
             border-radius: 5px;
             display: flex;
             align-items: center;
             justify-content: center;
             text-align: center;
             color: #6c757d;
             font-style: italic;
             min-height: 150px;
        }
        
        .go-chart-wrapper {
            position: relative; 
            flex-grow: 1;      
            min-height: 300px;
        }
        
        .go-chart-wrapper > div {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Alignment for Bottom Row --- */
        .table-container, .network-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            height: 650px;
            display: flex;
            flex-direction: column;
        }

        .data-table {
            flex-grow: 1;
            overflow-y: auto;
            position: relative;
        }

        .network-graph-wrapper {
            position: relative;
            flex-grow: 1;
        }

        #cy {
            width: 100%;
            height: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .network-graph-wrapper > .text-center {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- General Styles --- */
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

        .data-table th:first-child, .data-table td:first-child {
            width: 30px;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
        }
        
         .pagination .page-item.active .page-link {
             z-index: 3;
             color: #fff;
             background-color: var(--primary-color);
             border-color: var(--primary-color);
         }
         .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
         }

        /* --- Center table headers --- */
        #dataTable thead th {
            text-align: center;
            vertical-align: middle;
        }
         
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row">
               <div class="row justify-content-center text-center my-3">
  <div class="col-md-8">
    <h2>Drug-Target Interactions for <em><?php echo $pathogenDisplayName; ?></em></h2>
  </div>
</div>

            </div>
        </div>
    </header>

    <!-- Main Content -->
    <!-- MODIFICATION: Changed "container" to "container-fluid px-4" to make the layout wider -->
    <div class="container-fluid px-4">
        <!-- Overview Dashboard -->
        <section class="mb-5">
            <div class="row">
                <div class="col-lg-6">
                    <div class="abstract-container">
                        <div class="graphical-abstract-placeholder">
                            <img src="/anshu/arkbase/Graphical_Abstract/GA.jpg" style="width:100%">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
    <div class="chart-container p-3">
        <h4 class="mb-1">Top 10 GO terms</h4>

        <div class="btn-group mb-3" role="group" id="go-toggle-buttons" style="display: none;">
            <input type="radio" class="btn-check" name="go-type" id="btn-molecular" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="btn-molecular">Molecular Function</label>

            <input type="radio" class="btn-check" name="go-type" id="btn-biological" autocomplete="off">
            <label class="btn btn-outline-primary" for="btn-biological">Biological Process</label>

            <input type="radio" class="btn-check" name="go-type" id="btn-cellular" autocomplete="off">
            <label class="btn btn-outline-primary" for="btn-cellular">Cellular Component</label>
        </div>

        <div id="go-chart-loading" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <canvas id="goChart" style="display: none; width: 100%; max-height: 450px;"></canvas>

        <div id="go-chart-error" class="text-danger text-center" style="display: none;"></div>
    </div>
</div>

        </section>

        <!-- Interactive Explorer -->
        <section class="mb-5">
            <h2 class="section-title">Interactive Explorer</h2>
            <div class="row">
                <div class="col-lg-7">
                    <div class="table-container">
                        <!-- MODIFICATION: Added a flex container for the title and the new download button -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Explore Drug Target Interactions</h4>
                            <button class="btn btn-sm btn-success" onclick="downloadFullData()">
                                <i class="bi bi-download"></i> Download Full Dataset
                            </button>
                        </div>
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by Target, Drug, Protein, etc. (on current page)" disabled>
                        </div>
                        <div id="table-loading" class="text-center mb-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>
                        <div class="data-table" style="display: none;">
                            <table class="table table-striped table-hover" id="dataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAllCheckbox" disabled></th>
                                        <th>Target ID</th>
                                        <th>DrugBank ID</th>
                                        <th>Drug Name</th>
                                        <th>Score</th>
                                        <th>Protein Names</th>
                                        <th>Drug Degree</th>
                                        <th>Target Degree</th>
                                        <th>Reference Protein ID</th>
                                        <th>Pathway</th>
                                        <th>Toxicity Safe</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody"></tbody>
                            </table>
                        </div>
                        <div id="table-error" class="text-danger text-center" style="display: none;"></div>
                        <div class="pagination-container" id="pagination-controls" style="display: none;">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <li class="page-item" id="prev-page"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">Prev</span></a></li>
                                    <li class="page-item disabled" id="page-info"><span class="page-link">Page <span id="current-page-span"></span> of <span id="total-pages-span"></span> (<span id="total-records-span"></span> records)</span></li>
                                    <li class="page-item" id="next-page"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">Next</span></a></li>
                                </ul>
                            </nav>
                            <div class="ms-3">
                                <select class="form-select form-select-sm" id="items-per-page-select">
                                    <option value="20" selected>20 items per page</option>
                                    <option value="30">30 items per page</option>
                                    <option value="50">50 items per page</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="network-container">
                        <h4 class="mb-3">Target-Drug Interaction Network</h4>
                        <div id="network-loading" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>
                        <div id="cy" style="display: none;"></div>
                        <div id="network-error" class="text-danger text-center" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal and other popups -->
    <div id="drug-image-popup"></div>
    <div class="modal fade" id="targetInfoModal" tabindex="-1" aria-labelledby="targetInfoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="targetInfoModalLabel">Connected Drugs</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body" id="targetInfoModalBody"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div></div></div>
    </div>

    <script>
        // STEP 2: Get the current pathogen name from PHP into JavaScript
        const PATHOGEN_NAME = "<?php echo $pathogenName; ?>";

        // Global variables
        let dti_data = [];
        let filteredData = [];
        let currentPage = 1;
        let itemsPerPage = 20;
        let totalRecords = 0;
        let totalPages = 0;
        let go_data_all_records = null;
        let goChart;
        let cy;

        // Constants
        const drugTypeColors = { 'small molecule': 'var(--blue-color)', 'peptide': 'var(--green-color)', 'biotech': 'var(--warning-color)', 'unknown': '#6c757d' };
        const DATA_FETCH_URL = 'fetch_dti_data2.php';
        const GO_FETCH_URL = 'fetch_go_data.php';

        document.addEventListener('DOMContentLoaded', function() {
            const itemsPerPageSelect = document.getElementById('items-per-page-select');
            itemsPerPage = parseInt(itemsPerPageSelect.value);
            itemsPerPageSelect.addEventListener('change', handleItemsPerPageChange);
            fetchInitialData();
            setupSearch();
            setupTableCheckboxes();
            setupGoToggle();
        });

        // MODIFICATION: New function to handle the download button click
        function downloadFullData() {
            // This URL triggers the download logic that needs to be added to the backend.
            const downloadUrl = `${DATA_FETCH_URL}?pathogen=${PATHOGEN_NAME}&download=true`;
            window.location.href = downloadUrl;
        }

        async function fetchInitialData() {
            showLoadingIndicators();
            hideErrorMessages();
            await fetchGoData();
            await fetchDataPage(currentPage, itemsPerPage);
            hideLoadingIndicators();
            showContent();
            if (totalRecords > 0) enableControls();
            else disableControls();
        }

        async function fetchDataPage(page, limit) {
            console.log(`Fetching page ${page} for ${PATHOGEN_NAME} with ${limit} items...`);
            if (currentPage !== page) {
                // Show loading indicators only when paginating
                document.getElementById('table-loading').style.display = 'block';
                document.querySelector('.data-table').style.display = 'none';
                document.getElementById('network-loading').style.display = 'block';
                document.getElementById('cy').style.display = 'none';
                hideErrorMessages('table-error', 'network-error');
                disableControls();
            }

            try {
                // STEP 3: Pass pathogen name to the fetch call
                const response = await fetch(`${DATA_FETCH_URL}?pathogen=${PATHOGEN_NAME}&page=${page}&limit=${limit}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();
                if (result.error) throw new Error(`Server error: ${result.error}`);

                dti_data = result.data || [];
                filteredData = [...dti_data];
                totalRecords = result.total_records || 0;
                itemsPerPage = limit;
                totalPages = totalRecords > 0 ? Math.ceil(totalRecords / itemsPerPage) : 0;
                currentPage = page;

                renderTable();
                updatePaginationControls();
                createNetworkGraph();

            } catch (error) {
                console.error("Failed to fetch table data:", error);
                showError(`Failed to load table data: ${error.message}`, 'table-error');
                showError(`Failed to load network graph: ${error.message}`, 'network-error');
                dti_data = [];
                filteredData = [];
                totalRecords = 0;
                totalPages = 0;
                currentPage = 1;
                renderTable();
                updatePaginationControls();
                createNetworkGraph();
            } finally {
                document.getElementById('table-loading').style.display = 'none';
                document.querySelector('.data-table').style.display = 'block';
                document.getElementById('network-loading').style.display = 'none';
                if (dti_data.length > 0) {
                    document.getElementById('cy').style.display = 'block';
                } else if (cy) {
                    cy.destroy();
                    document.getElementById('cy').style.display = 'none';
                }
                enableControls();
            }
        }

        async function fetchGoData() {
            console.log(`Fetching GO data for ${PATHOGEN_NAME}...`);
            document.getElementById('go-chart-loading').style.display = 'block';
            document.getElementById('goChart').style.display = 'none';
            document.getElementById('go-toggle-buttons').style.display = 'none';
            hideErrorMessages('go-chart-error');

            try {
                // STEP 3: Pass pathogen name to the fetch call
                const response = await fetch(`${GO_FETCH_URL}?pathogen=${PATHOGEN_NAME}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();
                if (result.error) throw new Error(`Server error: ${result.error}`);
                go_data_all_records = result;
                console.log("GO data fetched successfully.");
                createGoChart(document.querySelector('#go-toggle-buttons input:checked').id.replace('btn-', ''));
            } catch (error) {
                console.error("Failed to fetch GO data:", error);
                showError(`Failed to load GO chart data: ${error.message}`, 'go-chart-error');
                go_data_all_records = null;
                if (goChart) goChart.destroy();
            } finally {
                document.getElementById('go-chart-loading').style.display = 'none';
                document.getElementById('go-toggle-buttons').style.display = 'flex';
                if (go_data_all_records && (Object.keys(go_data_all_records.biological).length > 0 || Object.keys(go_data_all_records.molecular).length > 0)) {
                    document.getElementById('goChart').style.display = 'block';
                } else {
                    document.getElementById('goChart').style.display = 'none';
                }
            }
        }

        function renderTable() {
            const tbody = document.getElementById('tableBody');
            if (!tbody) return;
            tbody.innerHTML = '';
            const dataToRender = document.getElementById('searchInput').value ? filteredData : dti_data;
            if (dataToRender.length === 0) {
                let message = (totalRecords === 0) ? 'No data available for this pathogen.' : 'No results found on this page for your search term.';
                tbody.innerHTML = `<tr><td colspan="11" class="text-center text-muted">${message}</td></tr>`;
            } else {
                dataToRender.forEach(item => {
                    const row = document.createElement('tr');
                    row.dataset.interactionId = item.interaction_id;
                    row.innerHTML = `
                        <td><input type="checkbox" class="row-checkbox" data-interaction-id="${item.interaction_id}"></td>
                        <td>${item.Target ? `<a href="https://www.uniprot.org/uniprot/${item.Target}" target="_blank">${item.Target}</a>` : 'N/A'}</td>
                        <td>${item['Drugbank.ID'] ? `<a href="https://go.drugbank.com/drugs/${item['Drugbank.ID']}" target="_blank">${item['Drugbank.ID']}</a>` : 'N/A'}</td>
                        <td>${item.Drug_Name || 'N/A'}</td>
                        <td>${item.Score !== null ? item.Score.toFixed(2) : 'N/A'}</td>
                        <td>${item['Protein.names'] || 'N/A'}</td>
                        <td>${item.Drug_Degree || 'N/A'}</td>
                        <td>${item.Target_Degree || 'N/A'}</td>
                        <td>${item.Ref_locus_tag || 'N/A'}</td>
                        <td>${item.Pathway ? item.Pathway.replace('PATHWAY: ', '') : 'N/A'}</td>
                        <td>${item.DeepPK_toxicity_Safe || 'N/A'}</td>
                    `;
                    tbody.appendChild(row);
                });
            }
            setupTableRowHighlight();
            filterNetworkFromCheckboxes();
            document.getElementById('selectAllCheckbox').disabled = dataToRender.length === 0;
        }

        // All other JavaScript functions (setupSearch, createNetworkGraph, etc.) remain unchanged.
        // They will work correctly because they use the global `dti_data` variable,
        // which is now being populated with data for the selected pathogen.
        // ... (All your other JS functions go here without modification)
         function updatePaginationControls() {
             const paginationContainer = document.getElementById('pagination-controls');
             if (!paginationContainer) return;
            document.getElementById('total-records-span').textContent = totalRecords;
            document.getElementById('current-page-span').textContent = totalRecords > 0 ? currentPage : 0;
            document.getElementById('total-pages-span').textContent = totalPages;
            const prevPageItem = document.getElementById('prev-page');
            const nextPageItem = document.getElementById('next-page');
            prevPageItem.classList.toggle('disabled', currentPage === 1 || totalRecords === 0);
            nextPageItem.classList.toggle('disabled', currentPage === totalPages || totalRecords === 0 || totalPages === 0);
             if (totalRecords > itemsPerPage || (totalRecords > 0 && totalPages > 1)) {
                 paginationContainer.style.display = 'flex';
            } else {
                paginationContainer.style.display = 'none';
            }
        }

         function handleItemsPerPageChange(event) {
             const newItemsPerPage = parseInt(event.target.value);
             if (newItemsPerPage !== itemsPerPage) {
                 itemsPerPage = newItemsPerPage;
                 currentPage = 1; 
                 fetchDataPage(currentPage, itemsPerPage);
             }
         }

        document.addEventListener('click', function(event) {
            const prevPageLink = event.target.closest('#prev-page .page-link');
            const nextPageLink = event.target.closest('#next-page .page-link');
            if (prevPageLink && !prevPageLink.parentElement.classList.contains('disabled')) {
                event.preventDefault();
                fetchDataPage(currentPage - 1, itemsPerPage);
            } else if (nextPageLink && !nextPageLink.parentElement.classList.contains('disabled')) {
                 event.preventDefault();
                fetchDataPage(currentPage + 1, itemsPerPage);
            }
        });


        // --- Loading and Error UI Management ---
        function showLoadingIndicators() {
             document.getElementById('go-chart-loading').style.display = 'block';
             document.getElementById('table-loading').style.display = 'block';
             document.getElementById('network-loading').style.display = 'block';
             document.getElementById('goChart').style.display = 'none';
             document.getElementById('go-toggle-buttons').style.display = 'none';
             document.querySelector('.data-table').style.display = 'none';
             document.getElementById('cy').style.display = 'none';
             document.getElementById('pagination-controls').style.display = 'none';
             disableControls();
        }

         function hideLoadingIndicators() {} // Handled in individual fetches

         function showContent() {
              document.getElementById('go-toggle-buttons').style.display = 'flex';
            if (dti_data && dti_data.length > 0) {
                 document.querySelector('.data-table').style.display = 'block';
                 document.getElementById('cy').style.display = 'block';
            } else {
                 document.querySelector('.data-table').style.display = 'block';
                 document.getElementById('cy').style.display = 'none';
                 document.getElementById('pagination-controls').style.display = 'none';
            }
             enableControls();
        }

         function showError(message, elementId) {
             const errorElement = document.getElementById(elementId);
             if(errorElement) {
                 errorElement.textContent = message;
                 errorElement.style.display = 'block';
             }
         }

         function hideErrorMessages(...elementIds) {
             const idsToHide = elementIds.length > 0 ? elementIds : ['table-error', 'network-error', 'go-chart-error'];
              idsToHide.forEach(id => {
                  const element = document.getElementById(id);
                  if(element) element.style.display = 'none';
              });
         }


         function disableControls() {
              const searchInput = document.getElementById('searchInput');
              const selectAllCheckbox = document.getElementById('selectAllCheckbox');
              const itemsPerPageSelect = document.getElementById('items-per-page-select');
              if (searchInput) searchInput.disabled = true;
              if (selectAllCheckbox) selectAllCheckbox.disabled = true;
              if (itemsPerPageSelect) itemsPerPageSelect.disabled = true;
               document.getElementById('prev-page').classList.add('disabled');
               document.getElementById('next-page').classList.add('disabled');
         }
          function enableControls() {
              if (totalRecords > 0) {
                  const searchInput = document.getElementById('searchInput');
                  const itemsPerPageSelect = document.getElementById('items-per-page-select');
                  if (searchInput) searchInput.disabled = false;
                   if (itemsPerPageSelect) itemsPerPageSelect.disabled = false;
              } else {
                  disableControls();
              }
         }


        // --- Chart/Table/Network Functions ---

        function setupGoToggle() {
             const goToggleDiv = document.getElementById('go-toggle-buttons');
             const newGoToggleDiv = goToggleDiv.cloneNode(true);
             goToggleDiv.parentNode.replaceChild(newGoToggleDiv, goToggleDiv);
            newGoToggleDiv.addEventListener('click', (event) => {
                if (event.target.tagName === 'LABEL') {
                    const buttonId = event.target.getAttribute('for');
                    let type = buttonId.replace('btn-', '');
                     createGoChart(type);
                }
            });
        }

        function createGoChart(goType) {
             if (!go_data_all_records || !go_data_all_records[goType] || Object.keys(go_data_all_records[goType]).length === 0) {
                 if (goChart) goChart.destroy();
                 showError(`No GO data available for ${goType}.`, 'go-chart-error');
                 document.getElementById('goChart').style.display = 'none';
                 return;
             }
             document.getElementById('goChart').style.display = 'block';
             hideErrorMessages('go-chart-error'); 

            const sortedTerms = Object.entries(go_data_all_records[goType])
                .sort((a, b) => b[1] - a[1]).slice(0, 10);
            const chartData = {
                labels: sortedTerms.map(([term, count]) => term),
                datasets: [{
                    label: 'Frequency (All Data)',
                    data: sortedTerms.map(([term, count]) => count),
                    backgroundColor: '#6610f2',
                    borderColor: '#6610f2',
                    borderWidth: 1 }] };
            const ctx = document.getElementById('goChart').getContext('2d');
            if (goChart) {
                goChart.data = chartData;
                goChart.options.scales.y.title.text = `GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Term`;
                goChart.options.plugins.title.text = `Top 10 GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Terms (All Data)`;
                goChart.update();
            } else {
                goChart = new Chart(ctx, { type: 'bar', data: chartData, options: {
                    responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                    scales: {
                        x: { beginAtZero: true, title: { display: true, text: 'Frequency' }, ticks: { precision: 0 } },
                        y: { title: { display: true, text: `GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Term` } } },
                    plugins: { legend: { display: false },
                        title: { display: true, text: `Top 10 GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Terms (All Data)`, padding: { bottom: 20 } } } } });
            }
        }

         function setupTableRowHighlight() {
             document.querySelectorAll('#tableBody tr').forEach(row => {
                 const newRow = row.cloneNode(true);
                 row.parentNode.replaceChild(newRow, row);
             });
             document.querySelectorAll('#tableBody tr').forEach(row => {
                row.addEventListener('click', (e) => {
                    if (e.target.closest('input[type="checkbox"]') || e.target.closest('a')) return;
                    const interaction = dti_data.find(d => d.interaction_id === row.dataset.interactionId);
                    if (interaction) highlightNetworkEdge(interaction.Target, interaction['Drugbank.ID']);
                });
            });
         }


        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
             if (!searchInput) return;
             const newSearchInput = searchInput.cloneNode(true);
             searchInput.parentNode.replaceChild(newSearchInput, searchInput);
            newSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filteredData = !searchTerm ? [...dti_data] : dti_data.filter(item =>
                    Object.values(item).some(val => val && val.toString().toLowerCase().includes(searchTerm))
                );
                renderTable();
            });
        }


        function setupTableCheckboxes() {
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
            const tableBody = document.getElementById('tableBody');
             if (!selectAllCheckbox || !tableBody) return;
             const newSelectAll = selectAllCheckbox.cloneNode(true);
             selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
             const newTableBody = tableBody.cloneNode(true);
             tableBody.parentNode.replaceChild(newTableBody, tableBody);

            newSelectAll.addEventListener('change', function() {
                newTableBody.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
                 filterNetworkFromCheckboxes();
            });
            newTableBody.addEventListener('change', function(e) {
                 if (e.target && e.target.matches('.row-checkbox')) {
                     filterNetworkFromCheckboxes();
                    if (!e.target.checked) newSelectAll.checked = false;
                    else {
                        const allCheckboxes = newTableBody.querySelectorAll('.row-checkbox');
                        newSelectAll.checked = Array.from(allCheckboxes).every(cb => cb.checked);
                    }
                 }
            });
        }


        function filterNetworkFromCheckboxes() {
            if (!cy || !dti_data) return;
            const selectedInteractionIds = new Set(Array.from(document.querySelectorAll('#tableBody .row-checkbox:checked')).map(cb => cb.dataset.interactionId));
            const selectedInteractions = dti_data.filter(d => selectedInteractionIds.has(d.interaction_id));
            const nodesToShow = new Set();
            selectedInteractions.forEach(i => {
                if (i.Target) nodesToShow.add(i.Target);
                if (i['Drugbank.ID']) nodesToShow.add(i['Drugbank.ID']);
            });

            cy.elements().forEach(el => {
                let show = false;
                if (selectedInteractionIds.size === 0) {
                    show = true; // Show all if no selection
                } else {
                    if (el.isNode()) show = nodesToShow.has(el.id());
                    else if (el.isEdge()) show = selectedInteractionIds.has(el.data('id'));
                }
                el.style('display', show ? 'element' : 'none');
            });
        }

        function createNetworkGraph() {
             if (!dti_data || dti_data.length === 0) {
                 if (cy) cy.destroy();
                 document.getElementById('cy').style.display = 'none';
                 showError('No data available to build network graph for this page.', 'network-error');
                 return;
             }
             document.getElementById('cy').style.display = 'block';
             document.getElementById('network-error').style.display = 'none';
             if (cy) cy.destroy();

            const nodes = [], edges = [], addedNodes = new Set();
            dti_data.forEach(item => {
                if (item.Target && item['Drugbank.ID']) {
                    if (!addedNodes.has(item.Target)) {
                        nodes.push({ data: { id: item.Target, label: item.Target, type: 'target' } });
                        addedNodes.add(item.Target);
                    }
                    if (!addedNodes.has(item['Drugbank.ID'])) {
                        nodes.push({ data: { 
                            id: item['Drugbank.ID'], label: item['Drugbank.ID'], type: 'drug',
                            drugType: item.Drug_Type || 'unknown', imageUrl: item.drug_image_url, drugName: item.Drug_Name 
                        }});
                        addedNodes.add(item['Drugbank.ID']);
                    }
                    if (item.interaction_id) { 
                        edges.push({ data: { 
                            id: item.interaction_id, source: item.Target, target: item['Drugbank.ID'], score: item.Score || 0 
                        }});
                    }
                }
            });

            cy = cytoscape({
                container: document.getElementById('cy'),
                elements: { nodes, edges },
                style: [
                    { selector: 'node[type="target"]', style: {
                        'background-color': '#343a40', 'label': 'data(label)', 'shape': 'diamond',
                        'width': '50px', 'height': '50px', 'text-valign': 'center', 'text-halign': 'center',
                        'font-size': '11px', 'font-weight': 'bold', 'color': 'white',
                        'text-outline-color': '#343a40', 'text-outline-width': 2 }},
                    { selector: 'node[type="drug"]', style: {
                        'background-color': ele => drugTypeColors[ele.data('drugType')] || drugTypeColors['unknown'],
                        'label': 'data(label)', 'shape': 'round-rectangle', 'width': '80px', 'height': '35px',
                        'text-valign': 'center', 'text-halign': 'center', 'font-size': '12px', 'color': 'white',
                        'text-outline-color': ele => drugTypeColors[ele.data('drugType')] || drugTypeColors['unknown'],
                        'text-outline-width': 2, 'text-wrap': 'wrap', 'text-max-width': '75px' }},
                    { selector: 'edge', style: {
                        'width': ele => Math.max(1.5, (ele.data('score') || 0) * 5 + 1),
                        'line-color': '#adb5bd', 'curve-style': 'bezier', 'opacity': 0.7 }},
                    { selector: '.highlighted', style: {
                        'background-color': '#ffc107', 'line-color': '#ffc107', 'color': 'black',
                        'text-outline-color': '#ffc107', 'transition-property': 'background-color, line-color, opacity, color, text-outline-color',
                        'transition-duration': '0.3s', 'opacity': 1, 'z-index': 9999 }},
                    { selector: ':hidden', style: { 'display': 'none' } }
                ],
                layout: { name: 'cose', animate: true, animationDuration: 500, padding: 30,
                    nodeRepulsion: 400000, idealEdgeLength: 150, gravity: 80, numIter: 1000,
                    initialTemp: 200, coolingFactor: 0.95, minTemp: 1.0, fit: true }
            });

            let nodeTippyInstance, edgeTippyInstance;

            function makeTippy(target, content) {
                const dummy = document.createElement('div');
                return tippy(dummy, {
                    getReferenceClientRect: () => target.renderedBoundingBox(),
                    trigger: 'manual', content: content, arrow: true,
                    placement: 'bottom', hideOnClick: false, allowHTML: true,
                    appendTo: document.body
                });
            }

            // === EVENT LISTENERS FOR NETWORK ===

            // Drug Node Hover (Image)
            cy.on('mouseover', 'node[type="drug"]', (evt) => {
                const node = evt.target;
                if (nodeTippyInstance) nodeTippyInstance.destroy();
                if (node.data('imageUrl')) {
                    const content = `<div class="text-center"><strong>${node.data('drugName') || node.id()}</strong><br><img src="${node.data('imageUrl')}" alt="${node.data('drugName')}" style="max-width:150px; max-height:150px; margin-top: 5px;"></div>`;
                    nodeTippyInstance = makeTippy(node, content);
                    nodeTippyInstance.show();
                }
            });

            // Edge Hover (Score)
            cy.on('mouseover', 'edge', (evt) => {
                const edge = evt.target;
                if (edgeTippyInstance) edgeTippyInstance.destroy();
                const score = edge.data('score');
                const content = `<strong>Score:</strong> ${score.toFixed(3)}`;
                edgeTippyInstance = makeTippy(edge, content);
                edgeTippyInstance.show();
            });
            
            // Cleanup on mouseout
            cy.on('mouseout', 'node, edge', () => {
                if (nodeTippyInstance) nodeTippyInstance.destroy();
                if (edgeTippyInstance) edgeTippyInstance.destroy();
                nodeTippyInstance = edgeTippyInstance = null;
            });
            
            // Cleanup on pan/zoom
            cy.on('zoom pan drag', () => {
                 if (nodeTippyInstance) nodeTippyInstance.destroy();
                 if (edgeTippyInstance) edgeTippyInstance.destroy();
                 nodeTippyInstance = edgeTippyInstance = null;
            });

            // Target Node Click (Show Modal)
            cy.on('tap', 'node[type="target"]', (evt) => {
                const targetNode = evt.target;
                const modalTitle = document.getElementById('targetInfoModalLabel');
                const modalBody = document.getElementById('targetInfoModalBody');
                
                modalTitle.textContent = `Drugs Interacting with Target: ${targetNode.id()}`;
                modalBody.innerHTML = ''; // Clear previous content

                const connectedDrugNodes = targetNode.connectedEdges().connectedNodes('[type="drug"]');

                if (connectedDrugNodes.length > 0) {
                    const listGroup = document.createElement('div');
                    listGroup.className = 'list-group';
                    
                    connectedDrugNodes.forEach(drugNode => {
                        const drugData = drugNode.data();
                        const imageUrl = `https://go.drugbank.com/structures/${drugData.id}/image.svg`;
                        const listItem = document.createElement('a');
                        listItem.href = imageUrl;
                        listItem.target = '_blank';
                        listItem.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                        listItem.innerHTML = `
                            <div>
                                <strong>${drugData.id}</strong>
                                <br>
                                <small class="text-muted">${drugData.drugName || 'N/A'}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill"><i class="bi bi-box-arrow-up-right"></i> View</span>
                        `;
                        listGroup.appendChild(listItem);
                    });
                    modalBody.appendChild(listGroup);
                } else {
                    modalBody.innerHTML = '<p class="text-muted">No connected drugs found for this target on the current page.</p>';
                }

                const targetModal = new bootstrap.Modal(document.getElementById('targetInfoModal'));
                targetModal.show();
            });

             filterNetworkFromCheckboxes();
        }

        function highlightNetworkEdge(targetId, drugId) {
            if (!cy) return;
            cy.elements().removeClass('highlighted');
            const targetNode = cy.getElementById(targetId);
            const drugNode = cy.getElementById(drugId);
            const interactionEdge = cy.edges(`[source = "${targetId}"][target = "${drugId}"], [source = "${drugId}"][target = "${targetId}"]`);
            if (targetNode.length > 0 && drugNode.length > 0 && interactionEdge.length > 0) {
                const elementsToHighlight = targetNode.union(drugNode).union(interactionEdge);
                elementsToHighlight.addClass('highlighted');
                 cy.animate({ fit: { eles: elementsToHighlight, padding: 50 }, duration: 500 });
                 setTimeout(() => elementsToHighlight.removeClass('highlighted'), 3000);
            }
        }
    </script>
</body>
<?php include 'footer.php'; ?>
</html>