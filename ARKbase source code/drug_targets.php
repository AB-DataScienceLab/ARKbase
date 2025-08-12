<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Targets</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root { --primary-color: #0d6efd; --dark-color: #212529; --light-gray: #f8f9fa; }
        .body { font-family: 'Arial'; background-color: var(--light-gray); }
        .header h1 { font-weight: 300; }
        .section-title { color: var(--dark-color); border-bottom: 3px solid var(--primary-color); padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
        .table-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 2rem; }
        .data-table { max-height: 70vh; overflow-y: auto; }
        .footer { background-color: var(--dark-color); color: white; padding: 2rem 0; margin-top: 3rem; }
        .boolean-cell { text-align: center; }
        .boolean-cell i.bi-check-circle-fill { color: green; }
        .boolean-cell i.bi-x-circle-fill { color: red; }
        .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .pagination-info { font-size: 0.9em; color: #6c757d; }
        .filter-row label { margin-right: 5px; font-weight: bold; font-size: 0.9em; }
        .filter-row select, .filter-row input[type="number"] { display: inline-block; width: auto; margin-right: 15px; font-size: 0.9em; }
        .filter-group { display: flex; align-items: center; margin-bottom: 5px; margin-top: 5px; }
        .filter-group label { margin-bottom: 0; }
        .filtered-view-info { background-color: #d1ecf1; border-color: #bee5eb; color: #0c5460; padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        

    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <center>
<!--                <h1 id="pageTitle">Drug Targets</h1>-->
                <h2 class="lead my-3">Explore potential drug targets for <em id="pathogenNameDisplay">...</em></h2>
            </center>
        </div>
    </header>

    <div class="container-fluid px-4">
        <section class="mb-5">
            <div class="table-container">
                 <div id="filteredViewInfo" class="filtered-view-info" style="display: none;"></div>
                 <div class="row mb-3">
                      <div class="col-lg-6 col-md-12 mb-2 mb-lg-0">
                           <input type="text" class="form-control" id="searchInput" placeholder="Search by Protein ID or Description...">
                      </div>
                       <div class="col-lg-6 col-md-12 d-flex justify-content-lg-end justify-content-start align-items-center">
                           <label for="itemsPerPageSelect" class="me-2">Items per page:</label>
                           <select class="form-select form-select-sm w-auto" id="itemsPerPageSelect">
                               <option value="20" selected>20</option>
                               <option value="30">30</option>
                               <option value="50">50</option>
                               <option value="-1">All</option>
                           </select>
                           <!-- This button is hidden by default and shown via JavaScript -->
                           <a id="evolutionaryAnalysisBtn" href="#" target="_blank" class="btn btn-info btn-sm ms-3" style="display: none;">
                               <i class="bi bi-diagram-3"></i> Evolutionary Analysis
                           </a>
                           <button class="btn btn-success btn-sm ms-3" onclick="downloadData()">
                               <i class="bi bi-download"></i> Download
                           </button>
                       </div>
                 </div>

                 <div class="row mb-3 filter-row">
                     <div class="col-md-12 d-flex flex-wrap align-items-center">
                          <span class="me-3 fw-bold">Filter by:</span>
                         <div class="filter-group"><label for="essentialFilter">Essential:</label><select class="form-select form-select-sm" id="essentialFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                         <div class="filter-group"><label for="humanNHFilter">Human NH:</label><select class="form-select form-select-sm" id="humanNHFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                         <div class="filter-group"><label for="antiTargetFilter">Anti-Target:</label><select class="form-select form-select-sm" id="antiTargetFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                         <div class="filter-group"><label for="nonParalogFilter">Non-Paralog:</label><select class="form-select form-select-sm" id="nonParalogFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                         <div class="filter-group"><label for="virulenceFilter">Virulence:</label><select class="form-select form-select-sm" id="virulenceFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                         <div class="filter-group"><label for="betweennessFilter">Betweenness:</label><select class="form-select form-select-sm" id="betweennessFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                         <div class="filter-group"><label for="ttdNovelFilter">TTD Novel:</label><select class="form-select form-select-sm" id="ttdNovelFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                         <div class="filter-group"><label for="drugbankNovelFilter">DrugBank Novel:</label><select class="form-select form-select-sm" id="drugbankNovelFilter"><option value="all">All</option><option value="1">Yes</option><option value="0">No</option></select></div>
                     </div>
                 </div>

                <div class="data-table">
                    <table class="table table-striped table-hover" id="drugTargetTable">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th>
                                <th>Protein ID</th>
                                <th>Protein Description</th>
                                <th class="boolean-cell">Essential</th>
                                <th class="boolean-cell">Human Non-homolog</th>
                                <th class="boolean-cell">Anti-Target</th>
                                <th class="boolean-cell">Non-Paralog</th>
                                <th class="boolean-cell">Virulence</th>
                                <th class="boolean-cell">Betweenness</th>
                                <th class="boolean-cell">TTD Novel</th>
                                <th class="boolean-cell">DrugBank Novel</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr><td colspan="11" class="text-center">Loading data...</td></tr>
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
   
    <?php include 'footer.php'; ?>

    <script>
        let drugTargetData = []; 
        let filteredDrugTargetData = [];
        let currentPathogen = '';

        let currentPage = 1;
        let itemsPerPage = 20;

        // Element references
        const searchInput = document.getElementById('searchInput');
        const itemsPerPageSelect = document.getElementById('itemsPerPageSelect');
        const paginationControls = document.getElementById('paginationControls');
        const paginationInfo = document.getElementById('paginationInfo');
        const nonParalogFilter = document.getElementById('nonParalogFilter');
        const virulenceFilter = document.getElementById('virulenceFilter');
        const essentialFilter = document.getElementById('essentialFilter');
        const ttdNovelFilter = document.getElementById('ttdNovelFilter');
        const drugbankNovelFilter = document.getElementById('drugbankNovelFilter');
        const humanNHFilter = document.getElementById('humanNHFilter');
        const antiTargetFilter = document.getElementById('antiTargetFilter');
        const betweennessFilter = document.getElementById('betweennessFilter');

        const pathogenNamesMap = {
            'a_baumannii': 'Acinetobacter baumannii', 'n_gonorrhoeae': 'Neisseria gonorrhoeae', 's_sonnei': 'Shigella sonnei', 's_pyogenes': 'Streptococcus pyogenes', 's_pneumoniae': 'Streptococcus pneumoniae', 's_flexneri': 'Shigella flexneri', 's_enterica': 'Salmonella enterica', 's_aureus': 'Staphylococcus aureus', 's_agalactiae': 'Streptococcus agalactiae', 'p_aeruginosa': 'Pseudomonas aeruginosa', 'k_pneumoniae': 'Klebsiella pneumoniae', 'h_influenzae': 'Haemophilus influenzae', 'e_faecium': 'Enterococcus faecium', 'e_coli': 'Escherichia coli'
        };

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            currentPathogen = urlParams.get('pathogen');
            const proteinId1 = urlParams.get('protein_id1');
            const proteinId2 = urlParams.get('protein_id2');

            // --- START: LOGIC FOR EVOLUTIONARY ANALYSIS BUTTON ---
            const evoAnalysisBtn = document.getElementById('evolutionaryAnalysisBtn');
            if (evoAnalysisBtn) {
                // Check if the current pathogen is Staphylococcus aureus
                if (currentPathogen === 's_aureus') {
                    evoAnalysisBtn.href = `evolutionary_analysis.php?pathogen=${currentPathogen}`;
                    evoAnalysisBtn.style.display = 'inline-block'; // Make the button visible
                } else {
                    evoAnalysisBtn.style.display = 'none'; // Ensure the button is hidden for all other pathogens
                }
            }
            // --- END: LOGIC FOR EVOLUTIONARY ANALYSIS BUTTON ---

            if (!currentPathogen) {
                handleLoadingError("Pathogen parameter is missing. Please provide a pathogen name (e.g., ?pathogen=s_aureus).");
                disableControls();
                return;
            }

            const displayPathogenName = pathogenNamesMap[currentPathogen] || currentPathogen;
//            document.getElementById('pageTitle').innerHTML = `Drug Targets for <i>${displayPathogenName}</i>`;
            document.getElementById('pathogenNameDisplay').textContent = displayPathogenName;

            setupEventListeners();

            fetchDrugTargetData(currentPathogen).then(() => {
                // Special filtering logic if protein IDs are passed in the URL
                if (proteinId1 && proteinId2) {
                    document.getElementById('pageTitle').innerHTML = `Drug Targets for Protein Pair in <i>${displayPathogenName}</i>`;
                    const infoDiv = document.getElementById('filteredViewInfo');
                    infoDiv.innerHTML = `Showing drug targets matching <strong>Protein ID 1:</strong> ${proteinId1} or <strong>Protein ID 2:</strong> ${proteinId2}.`;
                    infoDiv.style.display = 'block';
                    drugTargetData = drugTargetData.filter(item => item.prot_id === proteinId1 || item.prot_id === proteinId2);
                }
                applyFiltersAndSort(); 
            }).catch(error => {
                console.error(`Error fetching drug target data for ${currentPathogen}:`, error);
                handleLoadingError(`Failed to load drug target data for <i>${displayPathogenName}</i>. Please try again later.`);
                disableControls();
            });
        });

        function setupEventListeners() {
            searchInput.addEventListener('input', applyFiltersAndSort);
            nonParalogFilter.addEventListener('change', applyFiltersAndSort);
            virulenceFilter.addEventListener('change', applyFiltersAndSort);
            essentialFilter.addEventListener('change', applyFiltersAndSort);
            ttdNovelFilter.addEventListener('change', applyFiltersAndSort);
            drugbankNovelFilter.addEventListener('change', applyFiltersAndSort);
            humanNHFilter.addEventListener('change', applyFiltersAndSort);
            antiTargetFilter.addEventListener('change', applyFiltersAndSort);
            betweennessFilter.addEventListener('change', applyFiltersAndSort);
            itemsPerPageSelect.addEventListener('change', (event) => {
                itemsPerPage = parseInt(event.target.value, 10);
                currentPage = 1;
                renderTable();
                updatePaginationControls();
            });
        }

         function disableControls() {
              [searchInput, itemsPerPageSelect, nonParalogFilter, virulenceFilter, essentialFilter, ttdNovelFilter, drugbankNovelFilter, humanNHFilter, antiTargetFilter, betweennessFilter].forEach(el => el.disabled = true);
              paginationControls.innerHTML = '';
              paginationInfo.textContent = '';
         }

         function enableControls() {
              [searchInput, itemsPerPageSelect, nonParalogFilter, virulenceFilter, essentialFilter, ttdNovelFilter, drugbankNovelFilter, humanNHFilter, antiTargetFilter, betweennessFilter].forEach(el => el.disabled = false);
         }

         function handleLoadingError(message) {
              const tbody = document.getElementById('tableBody');
              if (tbody) tbody.innerHTML = `<tr><td colspan="11" class="text-center text-danger">${message}</td></tr>`;
         }

        async function fetchDrugTargetData(pathogenName) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '<tr><td colspan="11" class="text-center">Loading data...</td></tr>';
            disableControls();
            try {
                const response = await fetch(`https://datascience.imtech.res.in/anshu/arkbase/fetch_drug_targets.php?pathogen=${encodeURIComponent(pathogenName)}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                if (data && data.error) throw new Error(`Backend error: ${data.error}`);
                if (!Array.isArray(data)) throw new Error("Invalid data format received.");
                drugTargetData = data;
                enableControls();
            } catch (error) {
                console.error("Fetch error:", error);
                throw error; // Propagate error to be caught by the caller
            }
        }
        
        function calculateYesScore(item) {
            const booleanColumns = ['essential', 'human_NH', 'anti_target', 'non_paralog', 'virulence', 'betweenness', 'ttd_novel', 'drugbank_novel'];
            return booleanColumns.reduce((total, col) => total + (parseInt(item[col], 10) === 1 ? 1 : 0), 0);
        }

        function applyFiltersAndSort() {
             const searchTerm = searchInput.value.toLowerCase();
             const filters = {
                 non_paralog: nonParalogFilter.value,
                 virulence: virulenceFilter.value,
                 essential: essentialFilter.value,
                 ttd_novel: ttdNovelFilter.value,
                 drugbank_novel: drugbankNovelFilter.value,
                 human_NH: humanNHFilter.value,
                 anti_target: antiTargetFilter.value,
                 betweenness: betweennessFilter.value,
             };

             filteredDrugTargetData = drugTargetData.filter(item => {
                 const textMatch = (item.prot_id && String(item.prot_id).toLowerCase().includes(searchTerm)) || 
                                   (item.prot_desc && String(item.prot_desc).toLowerCase().includes(searchTerm));

                 const filterMatch = Object.keys(filters).every(key => {
                     return filters[key] === 'all' || String(item[key]) === filters[key];
                 });
                 
                 return textMatch && filterMatch;
             });

             // Sort by the number of 'Yes' values (descending)
             filteredDrugTargetData.sort((a, b) => calculateYesScore(b) - calculateYesScore(a));
             
             currentPage = 1;
             renderTable();
             updatePaginationControls();
        }

        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            if (filteredDrugTargetData.length === 0) {
                 tbody.innerHTML = '<tr><td colspan="11" class="text-center">No drug targets match the current criteria.</td></tr>';
                 updatePaginationControls();
                 return;
            }
            
            const currentItemsPerPage = itemsPerPage === -1 ? filteredDrugTargetData.length : itemsPerPage;
            const startIndex = (currentPage - 1) * currentItemsPerPage;
            const endIndex = startIndex + currentItemsPerPage;
            const dataForCurrentPage = filteredDrugTargetData.slice(startIndex, endIndex);

            const rowsHtml = dataForCurrentPage.map((item, index) => `
                <tr>
                    <td>${startIndex + index + 1}</td>
                    <td>${item.prot_id ? `<a href="https://www.ncbi.nlm.nih.gov/protein/${item.prot_id}" target="_blank">${item.prot_id}</a>` : 'N/A'}</td>
                    <td>${item.prot_desc || 'N/A'}</td>
                    <td class="boolean-cell">${formatBoolean(item.essential)}</td>
                    <td class="boolean-cell">${formatBoolean(item.human_NH)}</td>
                    <td class="boolean-cell">${formatBoolean(item.anti_target)}</td>
                    <td class="boolean-cell">${formatBoolean(item.non_paralog)}</td>
                    <td class="boolean-cell">${formatBoolean(item.virulence)}</td>
                    <td class="boolean-cell">${formatBoolean(item.betweenness)}</td>
                    <td class="boolean-cell">${formatBoolean(item.ttd_novel)}</td>
                    <td class="boolean-cell">${formatBoolean(item.drugbank_novel)}</td>
                </tr>
            `).join('');
            tbody.innerHTML = rowsHtml;
        }

         function formatBoolean(value) {
             const intValue = parseInt(value, 10);
             if (intValue === 1) return '<i class="bi bi-check-circle-fill" title="Yes"></i>';
             if (intValue === 0) return '<i class="bi bi-x-circle-fill" title="No"></i>';
             return 'N/A';
         }

        function updatePaginationControls() {
            const totalItems = filteredDrugTargetData.length;
            const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage);
            paginationControls.innerHTML = '';
            
            if (totalPages <= 1) {
                paginationInfo.textContent = totalItems > 0 ? `Showing all ${totalItems} entries` : '';
                return;
            }
            
            // Previous Button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous">Previous</a>`;
            prevLi.addEventListener('click', (e) => { e.preventDefault(); if (currentPage > 1) goToPage(currentPage - 1); });
            paginationControls.appendChild(prevLi);
            
            // Page Number Links (with ellipsis logic)
            const maxPageLinks = 3;
            let startPage = Math.max(1, currentPage - Math.floor(maxPageLinks / 2));
            let endPage = Math.min(totalPages, startPage + maxPageLinks - 1);
            if (endPage - startPage + 1 < maxPageLinks) {
                startPage = Math.max(1, endPage - maxPageLinks + 1);
            }
            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageLi.addEventListener('click', (e) => { e.preventDefault(); goToPage(i); });
                paginationControls.appendChild(pageLi);
            }

            // Next Button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next">Next</a>`;
            nextLi.addEventListener('click', (e) => { e.preventDefault(); if (currentPage < totalPages) goToPage(currentPage + 1); });
            paginationControls.appendChild(nextLi);

            // Update Info Text
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, totalItems);
            paginationInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`;
        }

        function goToPage(pageNumber) {
             const totalItems = filteredDrugTargetData.length;
             const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage);
             if (pageNumber >= 1 && pageNumber <= totalPages) {
                currentPage = pageNumber;
                renderTable();
                updatePaginationControls();
             }
        }

        function downloadData() {
             if (filteredDrugTargetData.length === 0) { 
                 alert("No data to download based on current filters."); 
                 return; 
             }
            const headers = Object.keys(filteredDrugTargetData[0]);
            const csvRows = [ 
                headers.join(','), 
                ...filteredDrugTargetData.map(row => 
                    headers.map(field => {
                        let value = row[field];
                        if (value === null || value === undefined) value = '';
                        // Escape quotes by doubling them
                        value = String(value).replace(/"/g, '""'); 
                        return `"${value}"`;
                    }).join(',')
                ) 
            ];
            const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const filenamePathogen = pathogenNamesMap[currentPathogen] || currentPathogen;
            a.download = `${filenamePathogen.toLowerCase().replace(/[^a-z0-9_]/g, '_')}_drug_targets_filtered.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    </script>
</body>
</html>