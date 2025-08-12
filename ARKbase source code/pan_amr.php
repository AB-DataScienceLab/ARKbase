<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #0d6efd;
            --dark-color: #212529;
            --light-gray: #f8f9fa;
        }
     	 .body {
  		font-family: 'Arial';
  		background-color: var(--light-gray);
		}
     
        .header .container {
             display: flex;
             justify-content: space-between;
             align-items: center;
             flex-wrap: wrap;
        }
        .section-title {
            color: var(--dark-color);
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .table-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        }
        .data-table-wrapper {
            max-height: 70vh;
            overflow-x: auto;
            overflow-y: auto;
        }
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
        .pagination-container {
             display: flex;
             justify-content: space-between;
             align-items: center;
             margin-top: 1rem;
        }

        /* --- MODIFICATION START --- */
        /* Center all table headers */
        #amrTable thead th {
            text-align: center;
            vertical-align: middle;
        }
        /* --- MODIFICATION END --- */
        
        /* Styles for boolean (Yes/No) columns */
        .boolean-cell {
            text-align: center;
            vertical-align: middle;
        }
        .boolean-cell i.bi-check-circle-fill {
            color: green;
            margin-right: 4px;
        }
        .boolean-cell i.bi-x-circle-fill {
            color: red;
            margin-right: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header py-4">
    <div class="container text-center">
        <h1 id="pageTitle" class="mb-2"></h1>
        <h5 class="mb-3">AMR data for <em id="pathogenNameDisplay">...</em></h5>
       <p></p>
    </div>
</header>

    

    <!-- Main Content -->
    <div class="container-fluid px-4">
        <section>
            <div class="table-container">
                 <div class="row mb-3">
                      <div class="col-md-6">
                           <input type="text" class="form-control" id="searchInput" placeholder="Search across all fields...">
                      </div>
                      <div class="col-md-6 d-flex justify-content-end align-items-center">
                          <label for="itemsPerPageSelect" class="me-2">Items per page:</label>
                          <select class="form-select form-select-sm w-auto" id="itemsPerPageSelect">
                              <option value="20" selected>20</option>
                              <option value="50">50</option>
                              <option value="100">100</option>
                              <option value="-1">All</option>
                          </select>
                           <button class="btn btn-success btn-sm ms-3" onclick="downloadData()">
                               <i class="bi bi-download"></i> Download
                           </button>
                      </div>
                 </div>

                <div class="data-table-wrapper">
                    <table class="table table-striped table-hover table-bordered" id="amrTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>#</th>
                                <th>Protein ID</th>
                                <th>Description</th>
                                <th>AMR Gene Family</th>
                                <th>Operon</th>
                                <th>Resistance Mechanism</th>
                                <th>Antibiotic</th>
                                <th class="boolean-cell">Virulence</th>
                                <th class="boolean-cell">Essential</th>
                                <th class="boolean-cell">Betweenness</th>
                                <th class="boolean-cell">Core</th>
                                <th class="boolean-cell">Drug Target</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Updated colspan to 12 -->
                            <tr><td colspan="12" class="text-center">Loading data...</td></tr>
                        </tbody>
                    </table>
                </div>

                 <div class="pagination-container">
                     <div id="paginationInfo" class="pagination-info"></div>
                     <nav><ul class="pagination pagination-sm mb-0" id="paginationControls"></ul></nav>
                 </div>
            </div>
        </section>
    </div>

 

    <script>
        // Global state
        let allAmrData = [];
        let filteredAmrData = [];
        let currentPathogen = '';
        let currentPage = 1;
        let itemsPerPage = 20;

        // DOM Element references
        const searchInput = document.getElementById('searchInput');
        const itemsPerPageSelect = document.getElementById('itemsPerPageSelect');
        const tableBody = document.getElementById('tableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        
        const pathogenNamesMap = {
            'p_aeruginosa': 'Pseudomonas aeruginosa', 'a_baumannii': 'Acinetobacter baumannii',
            'n_gonorrhoeae': 'Neisseria gonorrhoeae', 's_sonnei': 'Shigella sonnei',
            's_pyogenes': 'Streptococcus pyogenes', 's_pneumoniae': 'Streptococcus pneumoniae',
            's_flexneri': 'Shigella flexneri', 's_enterica': 'Salmonella enterica',
            's_aureus': 'Staphylococcus aureus', 's_agalactiae': 'Streptococcus agalactiae',
            'k_pneumoniae': 'Klebsiella pneumoniae', 'h_influenzae': 'Haemophilus influenzae',
            'e_faecium': 'Enterococcus faecium', 'e_coli': 'Escherichia coli'
        };

        // --- INITIALIZATION ---
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            currentPathogen = urlParams.get('pathogen');

            if (!currentPathogen) {
                handleError("Pathogen parameter is missing. (e.g., ?pathogen=p_aeruginosa)");
                disableControls();
                return;
            }

            const displayName = pathogenNamesMap[currentPathogen] || currentPathogen;
            document.getElementById('pageTitle').textContent = ``;
            document.getElementById('pathogenNameDisplay').textContent = displayName;

            setupEventListeners();
            fetchAmrData(currentPathogen).catch(error => {
                console.error(`Fetch failed:`, error);
                handleError(`Could not load AMR data for ${displayName}. Check console for details.`);
                disableControls();
            });
        });

        function setupEventListeners() {
            searchInput.addEventListener('input', applyFilters);
            itemsPerPageSelect.addEventListener('change', (e) => {
                itemsPerPage = parseInt(e.target.value, 10);
                currentPage = 1;
                renderTable();
                updatePaginationControls();
            });
        }

        function disableControls() {
            searchInput.disabled = true;
            itemsPerPageSelect.disabled = true;
        }
        
        function handleError(message) {
            tableBody.innerHTML = `<tr><td colspan="12" class="text-center text-danger">${message}</td></tr>`;
            paginationInfo.textContent = '';
            paginationControls.innerHTML = '';
        }

        // --- DATA FETCHING ---
        async function fetchAmrData(pathogenName) {
            const response = await fetch(`https://datascience.imtech.res.in/anshu/arkbase/fetch_pan_amr.php?pathogen=${encodeURIComponent(pathogenName)}`);
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ error: response.statusText }));
                throw new Error(`HTTP Error ${response.status}: ${errorData.error}`);
            }
            const data = await response.json();
            if (data.error) throw new Error(data.error);

            allAmrData = data;
            applyFilters();
        }

        // --- HELPER FUNCTION TO CALCULATE "YES" SCORE ---
        /**
         * Calculates a score for a row based on the number of "Yes" values
         * in the key boolean columns.
         * @param {object} item - A single row of data from the dataset.
         * @returns {number} The count of "Yes" values (from 0 to 5).
         */
        function calculateYesScore(item) {
            const booleanColumns = ['virulence', 'essential', 'betweenness', 'core', 'drug_target'];
            return booleanColumns.reduce((score, col) => {
                const value = String(item[col]).trim().toLowerCase();
                if (value === '1' || value === 'yes') {
                    return score + 1;
                }
                return score;
            }, 0);
        }

        // --- FILTERING, SORTING, & RENDERING ---
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            
            // 1. Filter data based on search term
            filteredAmrData = allAmrData.filter(item => {
                return Object.values(item).some(val => String(val).toLowerCase().includes(searchTerm));
            });

            // 2. Sort the filtered data by "Yes" score in descending order
            filteredAmrData.sort((a, b) => {
                const scoreB = calculateYesScore(b);
                const scoreA = calculateYesScore(a);
                // A higher score should come first, so we subtract scoreA from scoreB.
                return scoreB - scoreA;
            });

            // 3. Reset to the first page and render the sorted/filtered data
            currentPage = 1;
            renderTable();
            updatePaginationControls();
        }

        // Helper to format boolean values with icons
        function formatBoolean(value) {
            const strValue = String(value).trim().toLowerCase();
            if (strValue === '1' || strValue === 'yes') {
                return '<i class="bi bi-check-circle-fill"></i> Yes';
            } else if (strValue === '0' || strValue === 'no') {
                return '<i class="bi bi-x-circle-fill"></i> No';
            }
            if (value === null || value === undefined || strValue === '' || strValue === 'na') {
                return 'N/A';
            }
            return value; 
        }

        function renderTable() {
            tableBody.innerHTML = '';
            if (filteredAmrData.length === 0) {
                const message = allAmrData.length > 0 ? "No records match current filter criteria." : `No AMR data found for this pathogen.`;
                tableBody.innerHTML = `<tr><td colspan="12" class="text-center">${message}</td></tr>`;
                updatePaginationControls();
                return;
            }

            const currentItemsPerPage = itemsPerPage === -1 ? filteredAmrData.length : itemsPerPage;
            const startIndex = (currentPage - 1) * currentItemsPerPage;
            const dataForPage = filteredAmrData.slice(startIndex, startIndex + currentItemsPerPage);
            
            tableBody.innerHTML = dataForPage.map((item, index) => `
                <tr>
                    <td>${startIndex + index + 1}</td>
                    <td>${item.prot_id ? `<a href="https://www.ncbi.nlm.nih.gov/protein/${item.prot_id}" target="_blank">${item.prot_id}</a>` : 'N/A'}</td>
                    <td>${item.description || 'N/A'}</td>
                    <td>${item.amr_gene_family || 'N/A'}</td>
                    <td>${item.operon || 'N/A'}</td>
                    <td>${item.resistance_mechanism || 'N/A'}</td>
                    <td>${item.antibiotic || 'N/A'}</td>
                    <td class="boolean-cell">${formatBoolean(item.virulence)}</td>
                    <td class="boolean-cell">${formatBoolean(item.essential)}</td>
                    <td class="boolean-cell">${formatBoolean(item.betweenness)}</td>
                    <td class="boolean-cell">${formatBoolean(item.core)}</td>
                    <td class="boolean-cell">${formatBoolean(item.drug_target)}</td>
                </tr>
            `).join('');
            updatePaginationControls();
        }

        // --- PAGINATION & DOWNLOAD ---
        function updatePaginationControls() {
            const totalItems = filteredAmrData.length;
            const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage);
            paginationControls.innerHTML = '';
            if (totalPages <= 1) {
                paginationInfo.textContent = totalItems > 0 ? `Showing ${totalItems} entries` : '';
                return;
            }

            let links = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="goToPage(${currentPage - 1})">Previous</a></li>`;
            for (let i = 1; i <= totalPages; i++) {
                links += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="goToPage(${i})">${i}</a></li>`;
            }
            links += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="goToPage(${currentPage + 1})">Next</a></li>`;
            paginationControls.innerHTML = links;
            
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = itemsPerPage === -1 ? totalItems : Math.min(startIndex + itemsPerPage, totalItems);
            paginationInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`;
        }

        function goToPage(page) {
            event.preventDefault();
            const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(filteredAmrData.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
        }

        function downloadData() {
            if (filteredAmrData.length === 0) {
                alert("No data to download based on current filters.");
                return;
            }
            // The downloaded data will also be sorted according to the "Yes" score.
            const headers = [
                "prot_id", "description", "amr_gene_family", "operon", 
                "resistance_mechanism", "antibiotic", "virulence", "essential", 
                "betweenness", "core", "drug_target"
            ];
            
            const csvContent = [
                headers.join(','),
                ...filteredAmrData.map(row => headers.map(field => {
                    let value = row[field];
                    if (value === null || value === undefined) return '';
                    return `"${String(value).replace(/"/g, '""')}"`;
                }).join(','))
            ].join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            const filename = `${(pathogenNamesMap[currentPathogen] || currentPathogen).replace(/ /g, '_').toLowerCase()}_amr_data.csv`;
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
</body>
<?php include 'footer.php'; ?>
</html>