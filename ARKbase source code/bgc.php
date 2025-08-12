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
        .header {
            background:;
            color: black;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .header .container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
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
        .pagination-info {
            font-size: 0.9em;
            color: #6c757d;
        }
        .action-cell {
            text-align: center;
        }
        #bgcTable thead th {
            text-align: center;
            vertical-align: middle;
        }
        .toggle-link {
            cursor: pointer;
            color: var(--primary-color);
            font-size: 0.85em;
            text-decoration: underline;
            display: block;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container text-center">
            <h2 class="lead mb-0">Browse BGC data across different pathogens</h2>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container-fluid px-4">
        <div class="row justify-content-center">
            <div class="col-xl-11">
                <section>
                    <div class="table-container">
                         <div class="row mb-3 align-items-center">
                              <!-- Pathogen Filter -->
                              <div class="col-md-4">
                                  <label for="pathogenFilter" class="form-label fw-bold">Filter by Pathogen:</label>
                                  <select class="form-select" id="pathogenFilter">
                                      <option value="all" selected>All Pathogens</option>
                                  </select>
                              </div>
                              <!-- Search Input -->
                              <div class="col-md-4">
                                   <label for="searchInput" class="form-label fw-bold">Search Table:</label>
                                   <input type="text" class="form-control" id="searchInput" placeholder="Search any column...">
                              </div>
                              <!-- MODIFICATION START: Updated layout for buttons -->
                              <div class="col-md-4 d-flex justify-content-end align-items-end">
                                   <div class="me-3">
                                       <label for="itemsPerPageSelect" class="form-label fw-bold">Items per page:</label>
                                       <select class="form-select" id="itemsPerPageSelect">
                                           <option value="20" selected>20</option>
                                           <option value="50">50</option>
                                           <option value="100">100</option>
                                           <option value="-1">All</option>
                                       </select>
                                   </div>
                                   <div class="btn-group">
                                    <button class="btn btn-success btn-sm" onclick="downloadData()">
                                       <i class="bi bi-download"></i> Download CSV
                                   </button>
                                   <button class="btn btn-info btn-sm" onclick="downloadBigScapeResults()">
                                       <i class="bi bi-archive-fill"></i> Download BiG-SCAPE result
                                   </button>
                                   </div>
                               </div>
                               <!-- MODIFICATION END -->
                         </div>

                        <div class="data-table-wrapper">
                            <table class="table table-striped table-hover" id="bgcTable">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Strain Name (BPP)</th>
                                        <th>BGC Region</th>
                                        <th>BGC Genes (Locus Tags)</th>
                                        <th>Gene Count</th>
                                        <th>Known Metabolite</th>
                                        <th>Metabolite SMILES</th>
                                        <th>MIBiG Accession</th>
                                        <th>BGC Category</th>
                                        <th>BiG-SCAPE GCF</th>
                                        <th class="action-cell">antiSMASH Result</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <tr><td colspan="11" class="text-center">Loading data...</td></tr>
                                </tbody>
                            </table>
                        </div>

                         <div class="pagination-container">
                             <div id="paginationInfo" class="pagination-info"></div>
                             <nav>
                                 <ul class="pagination pagination-sm mb-0" id="paginationControls"></ul>
                             </nav>
                         </div>
                    </div>
                </section>
            </div>
        </div>
    </div>


    <script>
        // Global variables
        let allBgcData = [], filteredBgcData = [], currentPage = 1, itemsPerPage = 20;
        const antiSmashLinks = { 'Acinetobacter baumannii': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/a_baumannii_NZ_CP045110.1/index.html', 'Escherichia coli': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/e_coli_NC_002695.2/index.html', 'Enterococcus faecium': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/e_faecium_NZ_CP038996.1/index.html', 'Haemophilus influenzae': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/h_influenzae_NZ_CP085952.1/index.html', 'Klebsiella pneumoniae': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/k_pneumoniae_NC_016845.1/index.html', 'Neisseria gonorrhoeae': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/n_gonorrhoeae_NZ_AP023069.1/index.html', 'Pseudomonas aeruginosa': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/p_aeruginosa_NC_002516.2/index.html', 'Streptococcus agalactiae': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/s_agalactiae_NZ_CP012480.1/index.html', 'Staphylococcus aureus': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/s_aureus_NC_007795.1/index.html', 'Salmonella enterica': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/s_enterica_NC_003197.2/index.html', 'Shigella flexneri': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/s_flexneri_NC_004337.2/index.html', 'Streptococcus pneumoniae': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/s_pneumoniae_NZ_LN831051.1/index.html', 'Streptococcus pyogenes': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/s_pyogenes_NZ_LS483338.1/index.html', 'Shigella sonnei': 'https://datascience.imtech.res.in/anshu/arkbase/bgc/s_sonnei_NZ_CP026802.1/index.html' };
        const pathogenFilter = document.getElementById('pathogenFilter'), searchInput = document.getElementById('searchInput'), itemsPerPageSelect = document.getElementById('itemsPerPageSelect'), tableBody = document.getElementById('tableBody'), paginationInfo = document.getElementById('paginationInfo'), paginationControls = document.getElementById('paginationControls');

        document.addEventListener('DOMContentLoaded', () => { setupEventListeners(); fetchBgcData().catch(error => { console.error('Error fetching BGC data:', error); handleError('Failed to load BGC data.'); }); });
        function setupEventListeners() { pathogenFilter.addEventListener('change', applyFilters); searchInput.addEventListener('input', applyFilters); itemsPerPageSelect.addEventListener('change', (event) => { itemsPerPage = parseInt(event.target.value, 10); currentPage = 1; renderTable(); updatePaginationControls(); }); }
        function handleError(message) { tableBody.innerHTML = `<tr><td colspan="11" class="text-center text-danger">${message}</td></tr>`; }

        async function fetchBgcData() {
            const response = await fetch('https://datascience.imtech.res.in/anshu/arkbase/fetch_bgc.php');
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json(); if (data.error) throw new Error(`Backend error: ${data.error}`);
            allBgcData = data; populatePathogenFilter(); applyFilters();
        }

        function populatePathogenFilter() { const pathogenNames = [...new Set(allBgcData.map(item => item.pathogen_id))].sort(); pathogenNames.forEach(name => { const option = document.createElement('option'); option.value = name; option.textContent = name; pathogenFilter.appendChild(option); }); }
        function applyFilters() { const selectedPathogen = pathogenFilter.value; const searchTerm = searchInput.value.toLowerCase(); filteredBgcData = allBgcData.filter(item => (selectedPathogen === 'all' || item.pathogen_id === selectedPathogen) && Object.values(item).some(val => String(val).toLowerCase().includes(searchTerm))); currentPage = 1; renderTable(); updatePaginationControls(); }
        function toggleContent(element) { const parent = element.parentElement, truncatedSpan = parent.querySelector('.truncated-content'), fullSpan = parent.querySelector('.full-content'); const isHidden = fullSpan.style.display === 'none'; fullSpan.style.display = isHidden ? 'inline' : 'none'; truncatedSpan.style.display = isHidden ? 'none' : 'inline'; element.textContent = isHidden ? 'Show Less' : 'Show More'; }
        function createExpandableCell(fullText, maxLength = 50) { const text = fullText || ''; if (text.length <= maxLength) return text || 'N/A'; const truncatedText = text.substring(0, maxLength); return `<div><span class="truncated-content">${truncatedText}...</span><span class="full-content" style="display: none;">${text}</span><a href="javascript:void(0);" onclick="toggleContent(this)" class="toggle-link">Show More</a></div>`; }

        function renderTable() {
            if (filteredBgcData.length === 0) { tableBody.innerHTML = `<tr><td colspan="11" class="text-center">${allBgcData.length > 0 ? "No records match filters." : "No BGC data found."}</td></tr>`; return; }
            const currentItemsPerPage = itemsPerPage === -1 ? filteredBgcData.length : itemsPerPage, startIndex = (currentPage - 1) * currentItemsPerPage;
            const dataForCurrentPage = filteredBgcData.slice(startIndex, startIndex + currentItemsPerPage);
            tableBody.innerHTML = dataForCurrentPage.map((item, index) => {
                const bppNameHtml = item.refseq_ac ? `<a href="https://www.ncbi.nlm.nih.gov/nuccore/${item.refseq_ac}" target="_blank">${item.bpp_name || 'N/A'}</a>` : (item.bpp_name || 'N/A');
                const mibigHtml = item.MIBiG_accession && item.MIBiG_accession !== 'No hit' ? `${item.MIBiG_accession}` : 'No hit';
                const antiSmashUrl = antiSmashLinks[item.pathogen_id], antiSmashHtml = antiSmashUrl ? `<a href="${antiSmashUrl}" target="_blank" class="btn btn-primary btn-sm">View Report</a>` : 'N/A';
                const genesHtml = createExpandableCell(item.bgc_genes_locus_tag, 50), smilesHtml = createExpandableCell(item.metabolite_SMILES, 40);
                return `<tr><td>${startIndex + index + 1}</td><td>${bppNameHtml}</td><td>${item.bgc_region || 'N/A'}</td><td>${genesHtml}</td><td>${item.genes_count || 'N/A'}</td><td>${item.bioactive_secondary_metabolite || 'N/A'}</td><td>${smilesHtml}</td><td>${mibigHtml}</td><td>${item.bgc_category || 'N/A'}</td><td>${item.bigscape_gcf || 'N/A'}</td><td class="action-cell">${antiSmashHtml}</td></tr>`;
            }).join('');
        }

        function updatePaginationControls() {
            const totalItems = filteredBgcData.length, totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage);
            if (totalPages <= 1) { paginationInfo.textContent = totalItems > 0 ? `Showing ${totalItems} entries` : ''; paginationControls.innerHTML = ''; return; }
            let paginationHtml = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="goToPage(event, ${currentPage - 1})">«</a></li>`;
            for (let i = 1; i <= totalPages; i++) paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="goToPage(event, ${i})">${i}</a></li>`;
            paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="goToPage(event, ${currentPage + 1})">»</a></li>`;
            paginationControls.innerHTML = paginationHtml;
            const startIndex = (currentPage - 1) * itemsPerPage, endIndex = itemsPerPage === -1 ? totalItems : Math.min(startIndex + itemsPerPage, totalItems);
            paginationInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`;
        }

        function goToPage(event, pageNumber) { event.preventDefault(); const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(filteredBgcData.length / itemsPerPage); if (pageNumber < 1 || pageNumber > totalPages) return; currentPage = pageNumber; renderTable(); updatePaginationControls(); }

        function downloadData() {
            if (filteredBgcData.length === 0) { alert("No data to download based on current filters."); return; }
            const headers = ["pathogen_id", "bpp_name", "refseq_ac", "bgc_region", "bgc_genes_locus_tag", "genes_count", "bioactive_secondary_metabolite", "metabolite_SMILES", "MIBiG_accession", "bgc_category", "bigscape_gcf"];
            const csvRows = [headers.join(','), ...filteredBgcData.map(row => headers.map(field => `"${String(row[field] || '').replace(/"/g, '""')}"`).join(','))];
            const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob); const a = document.createElement('a');
            a.href = url; a.download = 'bgc_data_filtered.csv'; document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
        }

        // --- MODIFICATION START: New function for BiG-SCAPE download ---
        function downloadBigScapeResults() {
            // This simply redirects to the PHP script that will generate the ZIP file.
            window.location.href = 'download_bigscape.php';
        }
        // --- MODIFICATION END ---
    </script>
</body>
<?php include 'footer.php'; ?>
</html>