<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evolutionary Analysis</title>
    
    <!-- Redundant CSS links are okay, but ideally should only be in header.php. No harm in keeping them. -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root { --primary-color: #0dcaf0; --dark-color: #212529; --light-gray: #f8f9fa; }
        .body { font-family: 'Arial'; background-color: var(--light-gray); }
        .header h1 { font-weight: 300; }
        .table-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.07); margin-bottom: 2rem; }
        .data-table { max-height: 70vh; overflow-y: auto; }
        .pagination-container { display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; }
        .pagination-info { font-size: 0.9em; color: #6c757d; }
        .image-container { text-align: center; margin-bottom: 2rem; }
        .image-container img { max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        
        .toggle-text-link { color: #0d6efd; cursor: pointer; font-size: 0.85em; text-decoration: none; display: block; margin-top: 5px; }
        .toggle-text-link:hover { text-decoration: underline; }

        .domain-details-row td {
            padding: 0 !important;
            border: 0 !important;
        }
        .domain-iframe {
            width: 100%;
            height: 500px; 
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <center>
                <h1 id="pageTitle">Evolutionary Analysis of prioritized Drug Target</h1>
                <h2 class="lead mb-0"> <em id="pathogenNameDisplay">...</em></h2>
            </center>
        </div>
    </header>

    <div class="container-fluid px-4">
        <div class="image-container">
            <img src="Graphical_Abstract/Evo_ArkBase.png" alt="Evolutionary Analysis Graphical Abstract" class="img-fluid">
        </div>
    
        <section class="mb-5">
            <div class="table-container">
                 <div class="row mb-3">
                      <div class="col-md-6">
                           <input type="text" class="form-control" id="searchInput" placeholder="Search by Protein ID, UniProt ID, or Gene Name...">
                      </div>
                       <div class="col-md-6 d-flex justify-content-end align-items-center">
                           <label for="itemsPerPageSelect" class="me-2">Items per page:</label>
                           <select class="form-select form-select-sm w-auto" id="itemsPerPageSelect">
                               <option value="20" selected>20</option>
                               <option value="30">30</option>
                               <option value="50">50</option>
                               <option value="-1">All</option>
                           </select>
                           <button class="btn btn-success btn-sm ms-3" onclick="downloadData()"><i class="bi bi-download"></i> Download</button>
                       </div>
                 </div>

                <div class="data-table">
                    <table class="table table-striped table-hover table-bordered" id="evolutionaryTable">
                        <thead class="table-dark">
                            <tr>
                                <th>S.No</th><th>Protein ID</th><th>UniProt ID</th><th>Gene Name</th><th>FEL</th><th>FUBAR</th><th>SLAC</th><th>MEME</th><th>Busted P-Value</th><th>Domain</th><th>Literature (PMID)</th>
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

    <!-- 
        The script tags for jQuery and Bootstrap.bundle.min.js have been REMOVED from here 
        because they are now correctly loaded in header.php.
    -->
    
    <script>
        let evolutionaryData = [], filteredEvolutionaryData = [], currentPathogen = '', currentPage = 1, itemsPerPage = 20;
        const pathogenNamesMap = { 's_aureus': 'Staphylococcus aureus' };

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            currentPathogen = urlParams.get('pathogen');
            if (!currentPathogen || !pathogenNamesMap[currentPathogen]) { handleLoadingError("Invalid pathogen parameter."); return; }
            const displayPathogenName = pathogenNamesMap[currentPathogen];
            document.getElementById('pageTitle').innerHTML = `Evolutionary Analysis for Prioritized Drug Target`;
            document.getElementById('pathogenNameDisplay').textContent = displayPathogenName;
            document.getElementById('searchInput').addEventListener('input', applyFilters);
            document.getElementById('itemsPerPageSelect').addEventListener('change', (e) => { itemsPerPage = parseInt(e.target.value, 10); currentPage = 1; renderTable(); updatePaginationControls(); });
            fetchEvolutionaryData(currentPathogen);
        });
        
        function handleLoadingError(message) { document.getElementById('tableBody').innerHTML = `<tr><td colspan="11" class="text-center text-danger">${message}</td></tr>`; document.getElementById('searchInput').disabled = true; }
        
        async function fetchEvolutionaryData(pathogenName) {
            try {
                const response = await fetch(`fetch_evolutionary_data.php?pathogen=${encodeURIComponent(pathogenName)}`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                if (data.error) throw new Error(`Backend error: ${data.error}`);
                if (!Array.isArray(data)) throw new Error("Invalid data format received.");
                evolutionaryData = data;
                applyFilters(); 
            } catch (error) { console.error("Fetch error:", error); handleLoadingError(`Failed to load data. ${error.message}`); }
        }
        
        function applyFilters() {
             const searchTerm = document.getElementById('searchInput').value.toLowerCase();
             filteredEvolutionaryData = evolutionaryData.filter(item => (item.protein_id && String(item.protein_id).toLowerCase().includes(searchTerm)) || (item.uniprot_id && String(item.uniprot_id).toLowerCase().includes(searchTerm)) || (item.gene_name && String(item.gene_name).toLowerCase().includes(searchTerm)));
             currentPage = 1;
             renderTable();
             updatePaginationControls();
        }

        function createCollapsibleCell(text, maxLength = 30) {
            if (!text || text.length <= maxLength) return text || 'N/A';
            const shortText = text.substring(0, maxLength);
            const uniqueId = `col-${Math.random().toString(36).substr(2, 9)}`; 
            return `<span id="short_${uniqueId}">${shortText}...</span><span id="full_${uniqueId}" style="display:none;">${text}</span><a href="javascript:void(0)" class="toggle-text-link" onclick="toggleText(this, '${uniqueId}')">Show More</a>`;
        }

        function toggleText(linkElement, uniqueId) {
            const shortSpan = document.getElementById(`short_${uniqueId}`);
            const fullSpan = document.getElementById(`full_${uniqueId}`);
            if (fullSpan.style.display === 'none') { fullSpan.style.display = 'inline'; shortSpan.style.display = 'none'; linkElement.textContent = 'Show Less'; } 
            else { fullSpan.style.display = 'none'; shortSpan.style.display = 'inline'; linkElement.textContent = 'Show More'; }
        }

        function toggleDomainRow(buttonElement, proteinId) {
            const mainRow = document.getElementById(`row-${proteinId}`);
            const detailsRowId = `details-row-${proteinId}`;
            const existingDetailsRow = document.getElementById(detailsRowId);

            if (existingDetailsRow) {
                existingDetailsRow.remove();
                buttonElement.innerHTML = '<i class="bi bi-diagram-3"></i> View Domain';
                buttonElement.classList.remove('btn-secondary');
                buttonElement.classList.add('btn-info');
            } else {
                const newDetailsRow = document.createElement('tr');
                newDetailsRow.id = detailsRowId;
                newDetailsRow.className = 'domain-details-row';
                const domainUrl = `/anshu/arkbase/domain_info/${proteinId}.html`;
                const detailsCell = document.createElement('td');
                detailsCell.colSpan = 11;
                detailsCell.innerHTML = `<iframe class="domain-iframe" src="${domainUrl}" frameborder="0"></iframe>`;
                newDetailsRow.appendChild(detailsCell);
                mainRow.parentNode.insertBefore(newDetailsRow, mainRow.nextSibling);
                buttonElement.innerHTML = '<i class="bi bi-eye-slash"></i> Hide Domain';
                buttonElement.classList.remove('btn-info');
                buttonElement.classList.add('btn-secondary');
            }
        }

        function renderTable() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            if (filteredEvolutionaryData.length === 0) { tbody.innerHTML = '<tr><td colspan="11" class="text-center">No matching data found.</td></tr>'; updatePaginationControls(); return; }
            
            const currentItems = itemsPerPage === -1 ? filteredEvolutionaryData.length : itemsPerPage;
            const startIndex = (currentPage - 1) * currentItems;
            const dataForPage = filteredEvolutionaryData.slice(startIndex, startIndex + currentItems);

            const rowsHtml = dataForPage.map((item, index) => {
                const literatureLink = item.literature ? `<a href="https://pubmed.ncbi.nlm.nih.gov/${item.literature}/" target="_blank">${item.literature}</a>` : 'N/A';
                
                let domainCellHtml = 'N/A';
                if (item.protein_id) {
                    domainCellHtml = `<button type="button" class="btn btn-info btn-sm" onclick="toggleDomainRow(this, '${item.protein_id}')">
                        <i class="bi bi-diagram-3"></i> View Domain
                    </button>`;
                }
                
                return `
                <tr id="row-${item.protein_id}">
                    <td>${startIndex + index + 1}</td>
                    <td>${item.protein_id ? `<a href="https://www.ncbi.nlm.nih.gov/protein/${item.protein_id}" target="_blank">${item.protein_id}</a>` : 'N/A'}</td>
                    <td>${item.uniprot_id ? `<a href="https://www.uniprot.org/uniprot/${item.uniprot_id}" target="_blank">${item.uniprot_id}</a>` : 'N/A'}</td>
                    <td>${item.gene_name || 'N/A'}</td>
                    <td>${createCollapsibleCell(item.fel)}</td>
                    <td>${createCollapsibleCell(item.fubar)}</td>
                    <td>${createCollapsibleCell(item.slac)}</td>
                    <td>${createCollapsibleCell(item.meme)}</td>
                    <td>${item.busted_pvalue || 'N/A'}</td>
                    <td>${domainCellHtml}</td>
                    <td>${literatureLink}</td>
                </tr>`;
            }).join('');
            tbody.innerHTML = rowsHtml;
        }
        
        function updatePaginationControls() { const pControls = document.getElementById('paginationControls'); const pInfo = document.getElementById('paginationInfo'); if (!pControls || !pInfo) return; const totalItems = filteredEvolutionaryData.length; const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage); pControls.innerHTML = ''; if (totalPages <= 1) { pInfo.textContent = totalItems > 0 ? `Showing all ${totalItems} entries` : ''; return; } const prevLi = document.createElement('li'); prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`; prevLi.innerHTML = `<a class="page-link" href="#">Previous</a>`; prevLi.addEventListener('click', (e) => { e.preventDefault(); if (currentPage > 1) goToPage(currentPage - 1); }); pControls.appendChild(prevLi); let startPage = Math.max(1, currentPage - 2), endPage = Math.min(totalPages, startPage + 4); if (endPage - startPage + 1 < 5) startPage = Math.max(1, endPage - 4); for (let i = startPage; i <= endPage; i++) { const pageLi = document.createElement('li'); pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`; pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`; pageLi.addEventListener('click', (e) => { e.preventDefault(); goToPage(i); }); pControls.appendChild(pageLi); } const nextLi = document.createElement('li'); nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`; nextLi.innerHTML = `<a class="page-link" href="#">Next</a>`; nextLi.addEventListener('click', (e) => { e.preventDefault(); if (currentPage < totalPages) goToPage(currentPage + 1); }); pControls.appendChild(nextLi); const sIndex = (currentPage - 1) * itemsPerPage; const eIndex = Math.min(sIndex + itemsPerPage, totalItems); pInfo.textContent = itemsPerPage === -1 ? `Showing all ${totalItems} entries` : `Showing ${sIndex + 1} to ${eIndex} of ${totalItems} entries`; }
        function goToPage(pageNumber) { const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(filteredEvolutionaryData.length / itemsPerPage); if (pageNumber >= 1 && pageNumber <= totalPages) { currentPage = pageNumber; renderTable(); updatePaginationControls(); } }
        function downloadData() { if (filteredEvolutionaryData.length === 0) { alert("No data to download."); return; } const headers = ["protein_id", "uniprot_id", "gene_name", "organism", "fel", "fubar", "slac", "meme", "busted_pvalue", "domain", "literature"]; const csvRows = [headers.join(','), ...filteredEvolutionaryData.map(row => headers.map(field => `"${String(row[field] == null ? '' : row[field]).replace(/"/g, '""')}"`).join(','))]; const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' }); const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = `${currentPathogen}_evolutionary_analysis.csv`; document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url); }
    </script>
</body>
</html>