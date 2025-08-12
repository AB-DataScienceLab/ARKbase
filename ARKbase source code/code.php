<?php include 'header.php'; ?>
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

        body {
            font-family: 'Roboto';
            background-color: #f8f9fa;
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
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1>Drug-Target Interactions for <em> Escherichia coli</em></h1>
                    <br>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Overview Dashboard -->
        <section class="mb-5">
            <div class="row">
                <div class="col-lg-6 d-flex">
                    <div class="abstract-container w-100">
                        <h4>Abstract</h4>
                        <div class="abstract-content">
                             <p>This section contains a brief textual abstract summarizing the drug-target interactions for Escherichia coli. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                        </div>
                        <div class="graphical-abstract-placeholder">
                            <img src="/anshu/arkbase/Graphical_Abstract/GA.jpg" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-flex">
                    <div class="chart-container w-100">
                        <h4 class="mb-3">Top 10 GO Entries (All Data)</h4>
                        <div class="btn-group mb-3" role="group" id="go-toggle-buttons" style="display: none;">
                            <input type="radio" class="btn-check" name="go-type" id="btn-molecular" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="btn-molecular">Molecular Function</label>
                            <input type="radio" class="btn-check" name="go-type" id="btn-biological" autocomplete="off">
                            <label class="btn btn-outline-primary" for="btn-biological">Biological Process</label>
                            <input type="radio" class="btn-check" name="go-type" id="btn-cellular" autocomplete="off">
                            <label class="btn btn-outline-primary" for="btn-cellular">Cellular Component</label>
                        </div>
                        <div class="go-chart-wrapper">
                            <div id="go-chart-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>
                            <canvas id="goChart" style="display: none;"></canvas>
                            <div id="go-chart-error" class="text-danger text-center" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Explorer -->
        <section class="mb-5">
            <h2 class="section-title">Interactive Explorer</h2>
            <div class="row">
                <div class="col-lg-7 d-flex">
                    <div class="table-container w-100">
                        <h4 class="mb-3">Searchable Data Table (Current Page)</h4>
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput"
                                   placeholder="Search by Target, Drug, Protein, etc. (on current page)" disabled>
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
                                        <th>Drug Type</th>
                                        <th>Protein Names</th>
                                        <th>Pathway</th>
                                        <th>Toxicity Safe</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                     <!-- Table rows will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                         <div id="table-error" class="text-danger text-center" style="display: none;"></div>
                        <div class="pagination-container" id="pagination-controls" style="display: none;">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <li class="page-item" id="prev-page">
                                        <a class="page-link" href="#" aria-label="Previous">
                                            <span aria-hidden="true">«</span>
                                        </a>
                                    </li>
                                     <li class="page-item disabled" id="page-info"><span class="page-link">Page <span id="current-page-span"></span> of <span id="total-pages-span"></span> (<span id="total-records-span"></span> records)</span></li>
                                    <li class="page-item" id="next-page">
                                        <a class="page-link" href="#" aria-label="Next">
                                            <span aria-hidden="true">»</span>
                                        </a>
                                    </li>
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
                <div class="col-lg-5 d-flex">
                    <div class="network-container w-100">
                        <h4 class="mb-3">Target-Drug Interaction Network (Current Page)</h4>
                        <div class="network-graph-wrapper">
                            <div id="network-loading" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>
                            <div id="cy" style="display: none;"></div>
                            <div id="network-error" class="text-danger text-center" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Floating image popup -->
    <div id="drug-image-popup"></div>

    <script>
        // Global variables for fetched data and pagination state
        let dti_data = [];
        let filteredData = [];
        let currentPage = 1;
        let itemsPerPage = 20;
        let totalRecords = 0;
        let totalPages = 0;
        let go_data_all_records = null;
        let goChart;
        let cy;

        const drugTypeColors = {
            'small molecule': 'var(--blue-color)',
            'peptide': 'var(--green-color)',
            'biotech': 'var(--warning-color)',
            'unknown': '#6c757d'
        };

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

        async function fetchInitialData() {
             showLoadingIndicators();
             hideErrorMessages();
            await fetchGoData();
            await fetchDataPage(currentPage, itemsPerPage);
             hideLoadingIndicators();
             showContent();
             if (totalRecords > 0) {
                 enableControls();
             } else {
                 disableControls();
             }
        }

        async function fetchDataPage(page, limit) {
             if (currentPage !== page) {
                 document.getElementById('table-loading').style.display = 'block';
                 document.querySelector('.data-table').style.display = 'none';
                 document.getElementById('network-loading').style.display = 'flex';
                 document.getElementById('cy').style.display = 'none';
                  hideErrorMessages('table-error', 'network-error');
                 disableControls();
             }
            try {
                const response = await fetch(`${DATA_FETCH_URL}?page=${page}&limit=${limit}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.error) {
                    throw new Error(`Server error fetching table data: ${result.error}`);
                }
                dti_data = result.data || [];
                filteredData = [...dti_data];
                totalRecords = result.total_records || 0;
                itemsPerPage = limit;
                totalPages = totalRecords > 0 ? Math.ceil(totalRecords / itemsPerPage) : 0;
                currentPage = page;
                renderTable();
                updatePaginationControls();
                createGoChart(document.querySelector('#go-toggle-buttons input:checked').id.replace('btn-', ''));
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
             document.getElementById('go-chart-loading').style.display = 'flex';
             document.getElementById('goChart').style.display = 'none';
              document.getElementById('go-toggle-buttons').style.display = 'none';
              hideErrorMessages('go-chart-error');
             try {
                const response = await fetch(GO_FETCH_URL);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.error) {
                    throw new Error(`Server error fetching GO data: ${result.error}`);
                }
                go_data_all_records = result;
                const selectedGoType = document.querySelector('#go-toggle-buttons input:checked').id.replace('btn-', '');
                 createGoChart(selectedGoType);
             } catch (error) {
                 console.error("Failed to fetch GO data:", error);
                 showError(`Failed to load GO chart data: ${error.message}`, 'go-chart-error');
                 go_data_all_records = null;
                 if (goChart) goChart.destroy();
                 const ctx = document.getElementById('goChart').getContext('2d');
                 ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
             } finally {
                 document.getElementById('go-chart-loading').style.display = 'none';
                 document.getElementById('go-toggle-buttons').style.display = 'flex';
                  if (go_data_all_records) {
                     document.getElementById('goChart').style.display = 'block';
                 } else {
                      document.getElementById('goChart').style.display = 'none';
                 }
             }
         }

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

        function showLoadingIndicators() {
             document.getElementById('go-chart-loading').style.display = 'flex';
             document.getElementById('table-loading').style.display = 'block';
             document.getElementById('network-loading').style.display = 'flex';
             document.getElementById('goChart').style.display = 'none';
             document.getElementById('go-toggle-buttons').style.display = 'none';
             document.querySelector('.data-table').style.display = 'none';
             document.getElementById('cy').style.display = 'none';
             document.getElementById('pagination-controls').style.display = 'none';
             disableControls();
        }

         function hideLoadingIndicators() {}

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
                 errorElement.style.display = 'flex';
             }
         }

         function hideErrorMessages(...elementIds) {
             const idsToHide = elementIds.length > 0 ? elementIds : ['table-error', 'network-error', 'go-chart-error'];
              idsToHide.forEach(id => {
                  const element = document.getElementById(id);
                  if(element) {
                      element.style.display = 'none';
                  }
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

        function setupGoToggle() {
             const goToggleDiv = document.getElementById('go-toggle-buttons');
             const newGoToggleDiv = goToggleDiv.cloneNode(true);
             goToggleDiv.parentNode.replaceChild(newGoToggleDiv, goToggleDiv);

            newGoToggleDiv.addEventListener('click', (event) => {
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
             if (!go_data_all_records || !go_data_all_records[goType] || Object.keys(go_data_all_records[goType]).length === 0) {
                 if (goChart) {
                    goChart.destroy();
                    goChart = null;
                 }
                 const errorDiv = document.getElementById('go-chart-error');
                 errorDiv.innerHTML = `No GO data available for ${goType}.`;
                 errorDiv.style.display = 'flex';
                 document.getElementById('goChart').style.display = 'none';
                 return; 
             }
             document.getElementById('go-chart-error').style.display = 'none';
             document.getElementById('goChart').style.display = 'block';


            const goCounts = go_data_all_records[goType];

            const sortedTerms = Object.entries(goCounts)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10);

            const chartData = {
                labels: sortedTerms.map(([term, count]) => term),
                datasets: [{
                    label: 'Frequency (All Data)',
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
                 goChart.options.plugins.title.text = `Top 10 GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Terms (All Data)`;
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
                            x: {
                                beginAtZero: true,
                                title: { display: true, text: 'Frequency' },
                                ticks: { precision: 0 }
                            },
                            y: {
                                title: { display: true, text: `GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Term` }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                             title: {
                                display: true,
                                text: `Top 10 GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Terms (All Data)`,
                                padding: { bottom: 20 }
                             }
                        }
                    }
                });
            }
        }
        
        function renderTable() {
             const tbody = document.getElementById('tableBody');
             if (!tbody) return;
            tbody.innerHTML = '';
             const dataToRender = document.getElementById('searchInput').value ? filteredData : dti_data;
             if (dataToRender.length === 0) {
                 let message = 'No data available for this page.';
                 if (document.getElementById('searchInput').value) {
                     message = 'No results found on this page for your search term.';
                 } else if (totalRecords === 0) {
                      message = 'No data found for Escherichia coli in the database.';
                 } else if (totalRecords > 0 && dataToRender.length === 0) {
                      message = `No data found on page ${currentPage}. Try navigating to another page.`;
                 }
                 tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">${message}</td></tr>`;
             }
             else {
                dataToRender.forEach(item => {
                     const row = document.createElement('tr');
                     row.dataset.interactionId = item.interaction_id;
                     row.innerHTML = `
                         <td><input type="checkbox" class="row-checkbox" data-interaction-id="${item.interaction_id}" ${dataToRender.length === 0 ? 'disabled' : ''}></td>
                         <td>${item.Target ? `<a href="https://www.uniprot.org/uniprot/${item.Target}" target="_blank">${item.Target}</a>` : 'N/A'}</td>
                         <td>${item['Drugbank.ID'] ? `<a href="https://go.drugbank.com/drugs/${item['Drugbank.ID']}" target="_blank">${item['Drugbank.ID']}</a>` : 'N/A'}</td>
                         <td>${item.Drug_Name || 'N/A'}</td>
                         <td>${item.Score !== null && item.Score !== undefined ? item.Score.toFixed(2) : 'N/A'}</td>
                         <td><span class="badge" style="background-color: ${drugTypeColors[item.Drug_Type] || drugTypeColors['unknown']}">${item.Drug_Type || 'N/A'}</span></td>
                         <td>${item['Protein.names'] || 'N/A'}</td>
                         <td>${item.Pathway ? item.Pathway.replace('PATHWAY: ', '') : 'N/A'}</td>
                         <td>${item.DeepPK_toxicity_Safe || 'N/A'}</td>
                     `;
                     tbody.appendChild(row);
                });
             }
             filterNetworkFromCheckboxes();
             document.getElementById('selectAllCheckbox').disabled = dataToRender.length === 0;
        }

        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
             if (!searchInput) return;
             const newSearchInput = searchInput.cloneNode(true);
             searchInput.parentNode.replaceChild(newSearchInput, searchInput);
             newSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                if (!searchTerm) {
                    filteredData = [...dti_data];
                } else {
                    filteredData = dti_data.filter(item =>
                        (item.Target && item.Target.toLowerCase().includes(searchTerm)) ||
                        (item['Drugbank.ID'] && item['Drugbank.ID'].toLowerCase().includes(searchTerm)) ||
                        (item.Drug_Name && item.Drug_Name.toLowerCase().includes(searchTerm)) ||
                        (item['Protein.names'] && item['Protein.names'].toLowerCase().includes(searchTerm)) ||
                        (item.Pathway && item.Pathway.toLowerCase().includes(searchTerm)) ||
                         (item.DeepPK_toxicity_Safe && item.DeepPK_toxicity_Safe.toLowerCase().includes(searchTerm))
                    );
                }
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
                const isChecked = this.checked;
                const rowCheckboxes = newTableBody.querySelectorAll('.row-checkbox');
                 rowCheckboxes.forEach(cb => {
                     cb.checked = isChecked;
                 });
                 filterNetworkFromCheckboxes();
            });
            newTableBody.addEventListener('change', function(e) {
                 if (e.target && e.target.matches('.row-checkbox')) {
                     filterNetworkFromCheckboxes();
                    if (!e.target.checked) {
                        newSelectAll.checked = false;
                    } else {
                        const allVisibleCheckboxes = newTableBody.querySelectorAll('.row-checkbox');
                        const allChecked = Array.from(allVisibleCheckboxes).length > 0 && Array.from(allVisibleCheckboxes).every(cb => cb.checked);
                        newSelectAll.checked = allChecked;
                    }
                 }
            });
        }

        function filterNetworkFromCheckboxes() {
            if (!cy || !dti_data) {
                if (cy) cy.elements().remove();
                return;
            }
            const checkedBoxes = document.querySelectorAll('#tableBody .row-checkbox:checked');
            const selectedInteractionIds = new Set(Array.from(checkedBoxes).map(cb => cb.dataset.interactionId));
            const selectedInteractions = dti_data.filter(d => selectedInteractionIds.has(d.interaction_id));
            const nodesToShow = new Set();
            selectedInteractions.forEach(interaction => {
                if (interaction.Target) nodesToShow.add(interaction.Target);
                if (interaction.Drug_Name) nodesToShow.add(interaction.Drug_Name);
            });
            cy.elements().forEach(el => {
                if (el.isNode()) {
                    if (nodesToShow.has(el.id())) {
                         el.style('display', 'element');
                     } else {
                        el.style('display', 'none');
                     }
                } else if (el.isEdge()) {
                    if (selectedInteractionIds.has(el.data('id'))) {
                        el.style('display', 'element');
                    } else {
                        el.style('display', 'none');
                    }
                }
            });
             if (selectedInteractionIds.size === 0 && dti_data.length > 0) {
                 cy.elements().style('display', 'element');
             }
        }

        function createNetworkGraph() {
             if (!dti_data || dti_data.length === 0) {
                if (cy) {
                     cy.destroy();
                 }
                 document.getElementById('cy').style.display = 'none';
                 showError('No data available to build network graph for this page.', 'network-error');
                 return;
             }
             document.getElementById('cy').style.display = 'block';
             hideErrorMessages('network-error');
             if (cy) {
                 cy.destroy();
             }
            const nodes = [];
            const edges = [];
            const addedNodes = new Set();
            const drugDataMap = new Map();
            dti_data.forEach(item => {
                if (item.Drug_Name) {
                     drugDataMap.set(item.Drug_Name, { drugType: item.Drug_Type, imageUrl: item.drug_image_url });
                }
                if (item.Target && !addedNodes.has(item.Target)) {
                    nodes.push({ data: { id: item.Target, label: item.Target, type: 'target' } });
                    addedNodes.add(item.Target);
                }
                if (item.Drug_Name && !addedNodes.has(item.Drug_Name)) {
                    nodes.push({ data: { id: item.Drug_Name, label: item.Drug_Name, type: 'drug' } });
                    addedNodes.add(item.Drug_Name);
                }
                 if (item.Target && item.Drug_Name && item.interaction_id) {
                    edges.push({ data: { id: item.interaction_id, source: item.Target, target: item.Drug_Name, score: item.Score || 0 } });
                 }
            });
            nodes.forEach(node => {
                if (node.data.type === 'drug' && node.data.id) {
                    const drugInfo = drugDataMap.get(node.data.id);
                    if (drugInfo) {
                         node.data.drugType = drugInfo.drugType || 'unknown';
                         node.data.imageUrl = drugInfo.imageUrl;
                    } else {
                         node.data.drugType = 'unknown';
                         node.data.imageUrl = null;
                    }
                }
            });
            cy = cytoscape({
                container: document.getElementById('cy'),
                elements: { nodes, edges },
                style: [
                    { selector: 'node[type="target"]', style: {
                        'background-color': '#6c757d', 'label': 'data(label)', 'shape': 'ellipse', 'text-valign': 'bottom', 'text-halign': 'center', 'font-size': '10px', 'color': '#333', 'text-wrap': 'wrap', 'text-max-width': '80px'
                    } },
                    { selector: 'node[type="drug"]', style: {
                        'background-color': ele => drugTypeColors[ele.data('drugType')] || drugTypeColors['unknown'], 'label': 'data(label)', 'shape': 'rectangle', 'text-valign': 'bottom', 'text-halign': 'center', 'font-size': '10px', 'color': '#333', 'text-wrap': 'wrap', 'text-max-width': '80px'
                    } },
                    { selector: 'edge', style: {
                         'width': ele => Math.max(1, (ele.data('score') || 0) * 4 + 1), 'line-color': '#ccc', 'curve-style': 'bezier', 'opacity': 0.6
                    } },
                    { selector: '.highlighted', style: { 'background-color': '#ffc107', 'line-color': '#ffc107', 'transition-property': 'background-color, line-color, opacity', 'transition-duration': '0.3s', 'opacity': 1 } },
                    { selector: ':hidden', style: { 'display': 'none' } }
                ],
                layout: { name: 'cose', animate: true, animationDuration: 500, padding: 10, gravity: 1, edgeElasticity: 0.45, nodeRepulsion: 20000, idealEdgeLength: 100 }
            });
            let tippyInstance = null;
            cy.on('mouseover', 'node[type="drug"]', function(evt) {
                const node = evt.target;
                const imageUrl = node.data('imageUrl');
                const drugName = node.data('label');
                if (tippyInstance) {
                     tippyInstance.destroy();
                     tippyInstance = null;
                 }
                if (imageUrl) {
                    const dummy = document.createElement('div');
                    dummy.style.position = 'absolute';
                    const nodePos = node.renderedPosition();
                    dummy.style.left = `${nodePos.x}px`;
                    dummy.style.top = `${nodePos.y}px`;
                    document.body.appendChild(dummy);
                    tippyInstance = tippy(dummy, {
                        content: `<img src="${imageUrl}" alt="${drugName || 'Drug'} Image" style="max-width:150px; max-height:150px;">`,
                        trigger: 'manual', placement: 'right', arrow: true, allowHTML: true, interactive: true, appendTo: document.body, onHidden: (instance) => { instance.reference.remove(); }
                    });
                     tippyInstance.show();
                }
            });
            cy.on('mouseout', 'node[type="drug"]', function(evt) {
                 setTimeout(() => { if (tippyInstance) { tippyInstance.hide(); tippyInstance = null; } }, 50);
            });
             cy.on('position', 'node[type="drug"]', function(evt) { if (tippyInstance) { tippyInstance.popperInstance.update(); } });
             cy.on('zoom pan drag', function(evt) { if (tippyInstance) { tippyInstance.hide(); tippyInstance = null; } });
             filterNetworkFromCheckboxes();
        }

        function highlightNetworkEdge(targetId, drugName) {
            if (!cy) return;
            cy.elements().removeClass('highlighted');
            const targetNode = cy.getElementById(targetId);
            const drugNode = cy.getElementById(drugName);
             const interactionEdge = cy.edges().filter(edge =>
                 (edge.source().id() === targetId && edge.target().id() === drugName) ||
                 (edge.source().id() === drugName && edge.target().id() === targetId)
             );
            if (targetNode.length > 0 && drugNode.length > 0 && interactionEdge.length > 0) {
                 targetNode.addClass('highlighted');
                 drugNode.addClass('highlighted');
                 interactionEdge.addClass('highlighted');
                 const elesToFit = targetNode.union(drugNode).union(interactionEdge);
                 if (elesToFit.length > 0) {
                     cy.animate({
                         fit: { eles: elesToFit, padding: 50 },
                         duration: 500
                     });
                 }
                 setTimeout(() => {
                     targetNode.removeClass('highlighted');
                     drugNode.removeClass('highlighted');
                     interactionEdge.removeClass('highlighted');
                 }, 3000);
            } else {
                 console.warn(`Nodes/Edge not found in current page network for Target: ${targetId}, Drug: ${drugName}.`);
            }
        }
    </script>
</body>
<?php include 'footer.php'; ?>
</html>