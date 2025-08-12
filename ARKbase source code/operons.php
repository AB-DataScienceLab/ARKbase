<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operon Details</title>

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
        .pagination-info {
             font-size: 0.9em;
             color: #6c757d;
        }
        .controls-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .page-item .page-link {
            cursor: pointer;
        }
        .page-item.disabled .page-link {
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div><center>
                <h1 id="pageTitle">Operon Details</h1>
                <p class="lead mb-0">Explore Predicted Operon for <em id="pathogenNameDisplay">...</em></p>
            </div> </center>
            <!-- Download button has been moved from here -->
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid px-4">
        <section>
<!--            <h2 class="section-title">Browse Operons</h2>-->
            <div class="table-container">
                 <!-- Controls Row - MODIFIED -->
                 <div class="row mb-3 align-items-center">
                      <div class="col-lg-5 col-md-12 mb-2 mb-lg-0">
                           <input type="text" class="form-control" id="searchInput" placeholder="Search by Operon ID, Locus Tag, Gene Name, etc...">
                      </div>
                       <div class="col-lg-7 col-md-12">
                           <div class="controls-container">
                               <div>
                                   <label for="itemsPerPageSelect" class="me-2">Items per page:</label>
                                   <select class="form-select form-select-sm d-inline-block w-auto" id="itemsPerPageSelect">
                                       <option value="20" selected>20</option>
                                       <option value="50">50</option>
                                       <option value="100">100</option>
                                       <option value="-1">All</option>
                                   </select>
                               </div>
                               <button class="btn btn-success btn-sm ms-3" onclick="downloadData()">
                               <i class="bi bi-download"></i> Download
                           </button>
                           </div>
                       </div>
                 </div>

                <div class="data-table-wrapper">
                    <table class="table table-striped table-hover" id="operonTable">
                        <!-- Table Head - MODIFIED -->
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>#</th>
                                <th>Operon ID</th>
                                <th>Locus Tag</th>
                                <th>Operon Length</th>
                                <th>Gene Name</th>
                                <th>Protein ID</th>
                                <th>Start</th>
                                <th>Stop</th>
                                <th>Strand</th>
                                <th>Product</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- Data will be loaded here by JavaScript -->
                            <tr><td colspan="10" class="text-center">Loading data...</td></tr>
                        </tbody>
                    </table>
                </div>

                 <!-- Pagination Controls -->
                 <div class="pagination-container">
                     <div id="paginationInfo" class="pagination-info"></div>
                     <nav>
                         <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                     </nav>
                 </div>
            </div>
        </section>
    </div>

  

    <script>
        // Global variables
        let operonData = [];
        let filteredOperonData = [];
        let currentPathogen = '';
        let currentPage = 1;
        let itemsPerPage = 20;

        // DOM elements
        const searchInput = document.getElementById('searchInput');
        const itemsPerPageSelect = document.getElementById('itemsPerPageSelect');
        const paginationControls = document.getElementById('paginationControls');
        const paginationInfo = document.getElementById('paginationInfo');
        const tableBody = document.getElementById('tableBody');

        const pathogenNamesMap = {
            'a_baumannii': 'Acinetobacter baumannii', 'n_gonorrhoeae': 'Neisseria gonorrhoeae', 's_sonnei': 'Shigella sonnei', 's_pyogenes': 'Streptococcus pyogenes', 's_pneumoniae': 'Streptococcus pneumoniae', 's_flexneri': 'Shigella flexneri', 's_enterica': 'Salmonella enterica', 's_aureus': 'Staphylococcus aureus', 's_agalactiae': 'Streptococcus agalactiae', 'p_aeruginosa': 'Pseudomonas aeruginosa', 'k_pneumoniae': 'Klebsiella pneumoniae', 'h_influenzae': 'Haemophilus influenzae', 'e_faecium': 'Enterococcus faecium', 'e_coli': 'Escherichia coli'
        };

        // --- INITIALIZATION ---
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            currentPathogen = urlParams.get('pathogen');
            if (!currentPathogen) {
                handleError("Pathogen parameter is missing in the URL. Please provide a pathogen, e.g., ?pathogen=a_baumannii");
                disableControls();
                return;
            }
            const displayPathogenName = pathogenNamesMap[currentPathogen] || currentPathogen;
            document.getElementById('pageTitle').textContent = ` `;
            document.getElementById('pathogenNameDisplay').textContent = displayPathogenName;
            setupEventListeners();
            fetchOperonData(currentPathogen).catch(error => {
                console.error(`Error fetching operon data for ${currentPathogen}:`, error);
                handleError(`Failed to load data for ${displayPathogenName}. Please check the console for details.`);
                disableControls();
            });
        });

        function setupEventListeners() {
            searchInput.addEventListener('input', applyFilters);
            itemsPerPageSelect.addEventListener('change', (event) => {
                itemsPerPage = parseInt(event.target.value, 10);
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
            tableBody.innerHTML = `<tr><td colspan="10" class="text-center text-danger">${message}</td></tr>`;
            paginationInfo.textContent = '';
            paginationControls.innerHTML = '';
        }

        // --- DATA FETCHING ---
        async function fetchOperonData(pathogenName) {
            try {
                const response = await fetch(`https://datascience.imtech.res.in/anshu/arkbase/fetch_operons.php?pathogen=${encodeURIComponent(pathogenName)}`);
                if (!response.ok) {
                    const errorData = await response.json().catch(() => null);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorData?.error || response.statusText}`);
                }
                const data = await response.json();
                if (data.error) throw new Error(`Backend error: ${data.error}`);
                if (!Array.isArray(data)) throw new Error("Invalid data format received from server.");
                operonData = data;
                applyFilters();
            } catch (error) {
                console.error(error);
                throw error;
            }
        }

        // --- FILTERING & RENDERING ---
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            filteredOperonData = operonData.filter(item =>
                (item.Locus_Tag && item.Locus_Tag.toLowerCase().includes(searchTerm)) ||
                (item.Operon_ID && item.Operon_ID.toLowerCase().includes(searchTerm)) ||
                (item.Gene_Name && item.Gene_Name.toLowerCase().includes(searchTerm)) ||
                (item.Protein_ID && item.Protein_ID.toLowerCase().includes(searchTerm)) ||
                (item.Product && item.Product.toLowerCase().includes(searchTerm))
            );
            currentPage = 1;
            renderTable();
            updatePaginationControls();
        }

        function renderTable() {
            tableBody.innerHTML = '';
            if (filteredOperonData.length === 0) {
                const message = operonData.length > 0 ? "No records match the current search criteria." : `No operon data found for ${pathogenNamesMap[currentPathogen] || currentPathogen}.`;
                tableBody.innerHTML = `<tr><td colspan="10" class="text-center">${message}</td></tr>`;
                return;
            }
            const currentItemsPerPage = itemsPerPage === -1 ? filteredOperonData.length : itemsPerPage;
            const startIndex = (currentPage - 1) * currentItemsPerPage;
            const endIndex = startIndex + currentItemsPerPage;
            const dataForCurrentPage = filteredOperonData.slice(startIndex, endIndex);

            // Table Row Generation - MODIFIED
            const rowsHtml = dataForCurrentPage.map((item, index) => `
                <tr>
                    <td>${startIndex + index + 1}</td>
                    <td>${item.Operon_ID || 'N/A'}</td>
                    <td>${item.Locus_Tag || 'N/A'}</td>
                    <td>${item.Operon_Length || 'N/A'}</td>
                    <td>${item.Gene_Name || 'N/A'}</td>
                    <td>${item.Protein_ID ? `<a href="https://www.ncbi.nlm.nih.gov/protein/${item.Protein_ID}" target="_blank">${item.Protein_ID}</a>` : 'N/A'}</td>
                    <td>${item.Start || 'N/A'}</td>
                    <td>${item.Stop || 'N/A'}</td>
                    <td>${item.Strand || 'N/A'}</td>
                    <td>${item.Product || 'N/A'}</td>
                </tr>
            `).join('');
            tableBody.innerHTML = rowsHtml;
        }

        // --- PAGINATION ---
        function updatePaginationControls() {
            const totalItems = filteredOperonData.length;
            const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage);
            paginationControls.innerHTML = '';
            if (totalItems === 0) {
                paginationInfo.textContent = 'No entries';
                return;
            }
            const currentItemsCount = itemsPerPage === -1 ? totalItems : itemsPerPage;
            const startIndex = (currentPage - 1) * currentItemsCount;
            const endIndex = Math.min(startIndex + currentItemsCount, totalItems);
            paginationInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`;
            if (totalPages <= 1) return;
            
            const createPageItem = (page, text, isDisabled = false, isActive = false) => {
                const liClass = `page-item ${isActive ? 'active' : ''} ${isDisabled ? 'disabled' : ''}`;
                const linkContent = text === '...' ? `<span class="page-link">${text}</span>` : `<a class="page-link" href="#" data-page="${page}">${text}</a>`;
                return `<li class="${liClass}">${linkContent}</li>`;
            };
            let paginationHtml = createPageItem(currentPage - 1, '&laquo;', currentPage === 1);
            const pages = [];
            const edgeCount = 2, middleCount = 3;
            for (let i = 1; i <= totalPages; i++) {
                if (i <= edgeCount || i > totalPages - edgeCount || (i >= currentPage - Math.floor(middleCount/2) && i <= currentPage + Math.floor(middleCount/2))) {
                    pages.push(i);
                }
            }
            let lastPage = 0;
            for (const page of [...new Set(pages)]) {
                if (lastPage + 1 !== page) {
                    paginationHtml += createPageItem(0, '...', true);
                }
                paginationHtml += createPageItem(page, page, false, page === currentPage);
                lastPage = page;
            }
            paginationHtml += createPageItem(currentPage + 1, '&raquo;', currentPage === totalPages);
            paginationControls.innerHTML = paginationHtml;
            paginationControls.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const page = parseInt(e.target.dataset.page, 10);
                    if (page) goToPage(page);
                });
            });
        }

        function goToPage(pageNumber) {
            currentPage = pageNumber;
            renderTable();
            updatePaginationControls();
        }

        // --- DOWNLOAD FUNCTION ---
        function downloadData() {
            if (filteredOperonData.length === 0) {
                alert("No data available to download based on current filters.");
                return;
            }
            // CSV Headers - MODIFIED to match new column order
            const headers = ["Operon_ID", "Locus_Tag", "Operon_Length", "Gene_Name", "Protein_ID", "Start", "Stop", "Strand", "Product"];
            const csvRows = [
                headers.join(','),
                ...filteredOperonData.map(row =>
                    headers.map(field => {
                        let value = row[field];
                        if (value === null || value === undefined) value = '';
                        const stringValue = String(value);
                        if (stringValue.includes(',') || stringValue.includes('"')) {
                            return `"${stringValue.replace(/"/g, '""')}"`;
                        }
                        return stringValue;
                    }).join(',')
                )
            ];
            const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const filenamePathogen = pathogenNamesMap[currentPathogen] || currentPathogen;
            a.href = url;
            a.download = `${filenamePathogen.toLowerCase().replace(/ /g, '_')}_operon_data.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
<?php include 'footer.php'; ?>
</html>