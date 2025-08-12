<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Structural ARGs</title>

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
        .header { background: #ffffff; color: black; padding: 2rem 0; margin-bottom: 2rem; }
        .table-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.07); }
        .data-table-wrapper { max-height: 70vh; overflow-y: auto; overflow-x: auto; }
        .footer { background-color: var(--dark-color); color: white; padding: 2rem 0; margin-top: 3rem; }
        .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .pagination-info { font-size: 0.9em; color: #6c757d; }

        /* --- MODIFICATION START --- */
        /* Center all table headers */
        #structArgTable thead th {
            text-align: center;
            vertical-align: middle;
        }
        /* --- MODIFICATION END --- */
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container text-center">
            <h1 id="pageTitle">Structural ARGs</h1>
            <p class="lead mb-0">Explore structure of antimicrobial resistance genes for <em id="pathogenNameDisplay">...</em></p>
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
                    <table class="table table-striped table-hover table-bordered" id="structArgTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th>S.No.</th>
                                <th>Protein ID</th>
                                <th>Description</th>
                                <th>AMR Gene Family</th>
                                <th>Operon</th>
                                <th>Structure</th>
                                <th>Resistance Mechanism</th>
                                <th>Antibiotic</th>
                                <th>GO ID</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <!-- MODIFICATION: colspan changed from 10 to 9 -->
                            <tr><td colspan="9" class="text-center">Loading data...</td></tr>
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
        // --- Global State & DOM Elements ---
        let allStructArgData = [], filteredStructArgData = [], currentPathogen = '', currentPage = 1, itemsPerPage = 20;
        const searchInput = document.getElementById('searchInput');
        const itemsPerPageSelect = document.getElementById('itemsPerPageSelect');
        const tableBody = document.getElementById('tableBody');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationControls = document.getElementById('paginationControls');
        const pathogenNamesMap = {
            'p_aeruginosa': 'Pseudomonas aeruginosa', 'a_baumannii': 'Acinetobacter baumannii', 'n_gonorrhoeae': 'Neisseria gonorrhoeae', 's_sonnei': 'Shigella sonnei', 's_pyogenes': 'Streptococcus pyogenes', 's_pneumoniae': 'Streptococcus pneumoniae', 's_flexneri': 'Shigella flexneri', 's_enterica': 'Salmonella enterica', 's_aureus': 'Staphylococcus aureus', 's_agalactiae': 'Streptococcus agalactiae', 'k_pneumoniae': 'Klebsiella pneumoniae', 'h_influenzae': 'Haemophilus influenzae', 'e_faecium': 'Enterococcus faecium', 'e_coli': 'Escherichia coli'
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
            
            document.getElementById('pageTitle').innerHTML = `Structural ARGs for <em>${displayName}</em>`;
            document.getElementById('pathogenNameDisplay').textContent = displayName;

            setupEventListeners();
            fetchStructArgData(currentPathogen).catch(error => {
                console.error(`Fetch failed:`, error);
                handleError(`Could not load data for <i>${displayName}</i>. Check console for details.`);
                disableControls();
            });
        });

        function setupEventListeners() {
            searchInput.addEventListener('input', applyFilters);
            itemsPerPageSelect.addEventListener('change', (e) => {
                itemsPerPage = parseInt(e.target.value, 10);
                currentPage = 1;
                renderTable();
            });
        }

        // --- UI & STATE MANAGEMENT ---
        function disableControls() {
            searchInput.disabled = true;
            itemsPerPageSelect.disabled = true;
            paginationControls.innerHTML = '';
            paginationInfo.textContent = '';
        }

        function enableControls() {
            searchInput.disabled = false;
            itemsPerPageSelect.disabled = false;
        }

        function handleError(message) {
            // MODIFICATION: colspan changed from 10 to 9
            tableBody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">${message}</td></tr>`;
            paginationControls.innerHTML = '';
            paginationInfo.textContent = '';
        }

        // --- DATA FETCHING & FILTERING ---
        async function fetchStructArgData(pathogenName) {
            handleError('Loading data...');
            disableControls();
            const response = await fetch(`https://datascience.imtech.res.in/anshu/arkbase/fetch_struct_arg.php?pathogen=${encodeURIComponent(pathogenName)}`);
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ error: response.statusText }));
                throw new Error(`HTTP Error ${response.status}: ${errorData.error}`);
            }
            const data = await response.json();
            if (data.error) throw new Error(data.error);
            allStructArgData = data;
            enableControls();
            applyFilters();
        }

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            filteredStructArgData = allStructArgData.filter(item => {
                return Object.values(item).some(val => String(val).toLowerCase().includes(searchTerm));
            });
            currentPage = 1;
            renderTable();
        }

        // --- RENDERING & FORMATTING ---
        function formatStructure(pdbId) { if (!pdbId || pdbId.toLowerCase() === 'na' || !pdbId.trim()) return 'N/A'; const linkId = pdbId.split('_')[0]; return `<a href="https://www.rcsb.org/structure/${linkId}" target="_blank">${pdbId}</a>`; }
        function formatMultiLink(base_url, text, separator = ';') { if (!text) return 'N/A'; return text.split(separator).map(id => id.trim()).filter(id => id).map(id => `<a href="${base_url}${id}" target="_blank">${id}</a>`).join(', '); }

        function renderTable() {
            tableBody.innerHTML = '';
            if (filteredStructArgData.length === 0) {
                const message = allStructArgData.length > 0 ? "No records match current filters." : `No Structural ARG data found for <i>${pathogenNamesMap[currentPathogen] || currentPathogen}</i>.`;
                // MODIFICATION: colspan changed from 10 to 9
                tableBody.innerHTML = `<tr><td colspan="9" class="text-center">${message}</td></tr>`;
                updatePaginationControls();
                return;
            }
            const currentItemsPerPage = itemsPerPage === -1 ? filteredStructArgData.length : itemsPerPage;
            const startIndex = (currentPage - 1) * currentItemsPerPage;
            const dataForPage = filteredStructArgData.slice(startIndex, startIndex + currentItemsPerPage);
            
            // MODIFICATION: Removed the SP Primary table cell from the row template
            tableBody.innerHTML = dataForPage.map((item, index) => `
                <tr>
                    <td>${startIndex + index + 1}</td>
                    <td>${item.prot_id ? `<a href="https://www.ncbi.nlm.nih.gov/protein/${item.prot_id}" target="_blank">${item.prot_id}</a>` : 'N/A'}</td>
                    <td>${item.description || 'N/A'}</td>
                    <td>${item.amr_gene_family || 'N/A'}</td>
                    <td>${item.operon || 'N/A'}</td>
                    <td>${formatStructure(item.structure)}</td>
                    <td>${item.resistance_mechanism || 'N/A'}</td>
                    <td>${item.antibiotic || 'N/A'}</td>
                    <td>${formatMultiLink('https://amigo.geneontology.org/amigo/term/', item.go_id)}</td>
                </tr>
            `).join('');
            updatePaginationControls();
        }

        // --- PAGINATION & DOWNLOAD ---
        function updatePaginationControls() {
            const totalItems = filteredStructArgData.length;
            const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage);

            if (totalItems === 0) {
                paginationInfo.textContent = '';
                paginationControls.innerHTML = '';
                return;
            }

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = itemsPerPage === -1 ? totalItems : Math.min(startIndex + itemsPerPage, totalItems);
            paginationInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`;
            
            if (totalPages <= 1) {
                paginationControls.innerHTML = '';
                return;
            }
            
            paginationControls.innerHTML = buildPaginationLinks(totalPages, currentPage);
        }

        function buildPaginationLinks(totalPages, page) {
            let links = '';
            const createPageLink = (p, text, isDisabled = false, isActive = false) =>
                `<li class="page-item ${isActive ? 'active' : ''} ${isDisabled ? 'disabled' : ''}">
                    <a class="page-link" href="#" onclick="goToPage(event, ${p})">${text}</a>
                 </li>`;

            links += createPageLink(page - 1, 'Prev', page === 1);

            const maxVisible = 5;
            if (totalPages <= maxVisible + 2) {
                for (let i = 1; i <= totalPages; i++) links += createPageLink(i, i, false, i === page);
            } else {
                links += createPageLink(1, 1, false, 1 === page);
                if (page > 3) links += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                
                let start = Math.max(2, page - 1);
                let end = Math.min(totalPages - 1, page + 1);

                if (page <= 2) end = 3;
                if (page >= totalPages - 1) start = totalPages - 2;

                for (let i = start; i <= end; i++) links += createPageLink(i, i, false, i === page);

                if (page < totalPages - 2) links += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                links += createPageLink(totalPages, totalPages, false, totalPages === page);
            }

            links += createPageLink(page + 1, 'Next', page === totalPages);
            return links;
        }

        function goToPage(event, page) {
            event.preventDefault();
            const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(filteredStructArgData.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
        }

        function downloadData() {
            if (filteredStructArgData.length === 0) {
                alert("No data available to download based on current filters.");
                return;
            }
            // MODIFICATION: Removed "sp_primary" from the headers for CSV download
            const headers = ["prot_id", "description", "amr_gene_family", "operon", "structure", "resistance_mechanism", "antibiotic", "go_id"];
            const csvContent = [headers.join(','), ...filteredStructArgData.map(row => headers.map(field => {
                let value = row[field];
                if (value === null || value === undefined) value = '';
                return `"${String(value).replace(/"/g, '""')}"`;
            }).join(','))].join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            const filename = `${(pathogenNamesMap[currentPathogen] || currentPathogen).replace(/ /g, '_').toLowerCase()}_struct_arg_data.csv`;
            link.setAttribute("download", filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
<?php include 'footer.php'; ?>
</html>