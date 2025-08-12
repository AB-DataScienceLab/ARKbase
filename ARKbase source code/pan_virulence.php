<?php include 'header.php'; ?>

<!-- 
    NOTE: This code assumes 'header.php' has opened the <body> tag 
    and included necessary CSS like Bootstrap.
-->

<style>
    /* Page-specific styles */
    :root {
        --primary-color: #0d6efd;
        --dark-color: #212529;
        --light-gray: #f8f9fa;
    }

    /* Use a class on the body or a wrapper if 'body' is defined in header.php */
    body {
        font-family: 'Arial', sans-serif;
        background-color: var(--light-gray);
    }

    .header h1 {
        font-weight: 300;
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
        margin-bottom: 2rem;
    }

    .data-table-wrapper {
        max-height: 70vh; /* Use viewport height for responsiveness */
        overflow-y: auto;
    }

    /* --- MODIFICATION START --- */
    /* Center all table headers */
    #virulenceTable thead th {
        text-align: center;
        vertical-align: middle; /* This is good practice for vertical alignment */
    }
    /* --- MODIFICATION END --- */

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
    
    /* --- NEW: Pagination Styles to match reference image --- */
    .pagination {
        --bs-pagination-focus-box-shadow: none; /* Remove Bootstrap's default focus shadow */
    }
    
    .pagination .page-link {
        color: #295a9e; /* Dark blue text for numbers to match the active state's theme */
        background-color: #fff;
        border: 1px solid #dee2e6; /* Standard light gray border */
        margin: 0 3px; /* Add a little space between buttons */
        border-radius: 0.25rem !important; /* Force rounded corners on all items */
    }
    
    /* Hover effect for clickable, non-active links */
    .pagination .page-link:hover {
        background-color: #f0f3f5; /* A subtle hover */
    }
    
    /* Active Page Style (e.g., '1' in the image) */
    .pagination .page-item.active .page-link {
        background-color: #295a9e; /* Custom dark blue from image */
        border-color: #295a9e;
        color: white;
        z-index: 3;
    }
    .pagination .page-item.active .page-link:hover {
        background-color: #295a9e; /* Keep active color on hover */
    }
    
    
    /* Disabled Page Style (e.g., 'Previous' in the image) */
    .pagination .page-item.disabled .page-link {
        color: #c0c5cb; /* Very light gray text for disabled items */
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    /* Ellipsis style */
    .pagination .page-item.ellipsis .page-link,
    .pagination .page-item.ellipsis .page-link:hover {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        pointer-events: none; /* Make it unclickable */
    }
    
    /* Remove default Bootstrap focus shadow */
    .pagination .page-link:focus {
        box-shadow: none;
    }
</style>

<!-- Header -->
<header class="header py-4">
    <div class="container text-center">
<!--        <h1 class="mb-2">Pan-Virulence Factors</h1>-->
        <h2 class="lead mb-3">Virulence data for <em id="pathogenNameDisplay">...</em></h2>
    </div>
</header>

<!-- Main Content -->
<div class="container-fluid px-4">
    <section>
        <div class="table-container">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="searchInput" placeholder="Search across all fields...">
                    </div>
                    <div class="col-md-8 d-flex justify-content-end align-items-center">
                        <label for="itemsPerPageSelect" class="me-2 mb-0">Items per page:</label>
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
                <table class="table table-striped table-hover table-bordered" id="virulenceTable">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>S.No.</th>
                            <th>Protein ID</th>
                            <th>Subject Seq ID</th>
                            <th>Gene Symbol</th>
                            <th>Protein Name</th>
                            <th>VF Category</th>
                            <th>Operon ID</th>
                            <th>Card Desc</th>
                            <th>AMR Gene Family</th>
                            <th>Resistance Mech</th>
                            <th>Antibiotics</th>
                            <th>VF ID</th>
                            <th>VFC ID</th>
                            <th>Organism (VFDB)</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="14" class="text-center">Loading data...</td></tr>
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
    let allVirulenceData = [];
    let filteredVirulenceData = [];
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
        'a_baumannii': 'Acinetobacter baumannii', 'n_gonorrhoeae': 'Neisseria gonorrhoeae',
        's_sonnei': 'Shigella sonnei', 's_pyogenes': 'Streptococcus pyogenes',
        's_pneumoniae': 'Streptococcus pneumoniae', 's_flexneri': 'Shigella flexneri',
        's_enterica': 'Salmonella enterica', 's_aureus': 'Staphylococcus aureus',
        's_agalactiae': 'Streptococcus agalactiae', 'p_aeruginosa': 'Pseudomonas aeruginosa',
        'k_pneumoniae': 'Klebsiella pneumoniae', 'h_influenzae': 'Haemophilus influenzae',
        'e_faecium': 'Enterococcus faecium', 'e_coli': 'Escherichia coli'
    };

    // --- INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        currentPathogen = urlParams.get('pathogen');

        if (!currentPathogen) {
            handleError("Pathogen parameter is missing in the URL. (e.g., ?pathogen=e_coli)");
            disableControls();
            return;
        }

        const displayName = pathogenNamesMap[currentPathogen] || currentPathogen;
        // The pathogen name is now italicized via the <em> tag in the HTML
        document.getElementById('pathogenNameDisplay').textContent = displayName;

        setupEventListeners();
        fetchVirulenceData(currentPathogen).catch(error => {
            console.error(`Fetch failed:`, error);
            // Updated to use innerHTML to render the <em> tag
            handleError(`Could not load data for <em>${displayName}</em>. Please check the console for details.`);
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
        // Use innerHTML to allow for HTML tags like <em> in the message
        tableBody.innerHTML = `<tr><td colspan="14" class="text-center text-danger">${message}</td></tr>`;
        paginationInfo.textContent = '';
        paginationControls.innerHTML = '';
    }

    // --- DATA FETCHING ---
    async function fetchVirulenceData(pathogenName) {
        const response = await fetch(`https://datascience.imtech.res.in/anshu/arkbase/fetch_pan_virulence.php?pathogen=${encodeURIComponent(pathogenName)}`);
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ error: response.statusText }));
            throw new Error(`HTTP Error ${response.status}: ${errorData.error}`);
        }
        const data = await response.json();
        if (data.error) throw new Error(data.error);

        allVirulenceData = data;
        applyFilters();
    }

    // --- FILTERING & RENDERING ---
    function hasAmrData(item) {
        return (item.card_description && item.card_description !== 'N/A') ||
                (item.amr_gene_family && item.amr_gene_family !== 'N/A') ||
                (item.resistance_mechanism && item.resistance_mechanism !== 'N/A') ||
                (item.antibiotics && item.antibiotics !== 'N/A');
    }

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        
        filteredVirulenceData = allVirulenceData.filter(item => {
            return Object.values(item).some(val => 
                String(val).toLowerCase().includes(searchTerm)
            );
        });

        filteredVirulenceData.sort((a, b) => {
            const aHasData = hasAmrData(a);
            const bHasData = hasAmrData(b);
            return bHasData - aHasData; 
        });

        currentPage = 1;
        renderTable();
        updatePaginationControls();
    }

    function renderTable() {
        tableBody.innerHTML = '';
        if (filteredVirulenceData.length === 0) {
            const displayName = pathogenNamesMap[currentPathogen] || currentPathogen;
            const message = allVirulenceData.length > 0 
                ? "No records match current filter criteria." 
                : `No virulence data found for <em>${displayName}</em>.`;
            // Use innerHTML for the "no data" message to support italics
            tableBody.innerHTML = `<tr><td colspan="14" class="text-center">${message}</td></tr>`;
            updatePaginationControls();
            return;
        }

        const currentItemsPerPage = itemsPerPage === -1 ? filteredVirulenceData.length : itemsPerPage;
        const startIndex = (currentPage - 1) * currentItemsPerPage;
        const dataForPage = filteredVirulenceData.slice(startIndex, startIndex + currentItemsPerPage);
        
        tableBody.innerHTML = dataForPage.map((item, index) => `
            <tr>
                <td>${startIndex + index + 1}</td>
                <td>${item.prot_id ? `<a href="https://www.ncbi.nlm.nih.gov/protein/${item.prot_id}" target="_blank">${item.prot_id}</a>` : 'N/A'}</td>
                <td>${item.sseqid || 'N/A'}</td>
                <td>${item.Subject_Gene_Symbol || 'N/A'}</td>
                <td>${item.Subject_Protein_Name || 'N/A'}</td>
                <td>${item.VF_Category || 'N/A'}</td>
                <td>${item.Operon_ID || 'N/A'}</td>
                <td>${item.card_description || 'N/A'}</td>
                <td>${item.amr_gene_family || 'N/A'}</td>
                <td>${item.resistance_mechanism || 'N/A'}</td>
                <td>${item.antibiotics || 'N/A'}</td>
                <td>${item.VF_ID ? `${item.VF_ID}` : 'N/A'}</td>
                <td>${item.VFC_ID || 'N/A'}</td>
                <td>${item.Organism_vfdbhit || 'N/A'}</td>
            </tr>
        `).join('');
        updatePaginationControls();
    }

    // --- PAGINATION & DOWNLOAD ---

    /**
     * Creates an array of page numbers and ellipses for pagination.
     * e.g., createPaginationArray(4, 10) => [1, '...', 3, 4, 5, '...', 10]
     * @param {number} currentPage The current active page.
     * @param {number} totalPages The total number of pages.
     * @returns {Array<number|string>} An array of pages and ellipses.
     */
    function createPaginationArray(currentPage, totalPages) {
        const pages = new Set();
        // Always show first and last page
        pages.add(1);
        pages.add(totalPages);
        
        // Show current page and its immediate neighbors
        pages.add(currentPage);
        if (currentPage > 1) {
            pages.add(currentPage - 1);
        }
        if (currentPage < totalPages) {
            pages.add(currentPage + 1);
        }

        // Convert set to a sorted array
        const sortedPages = Array.from(pages).sort((a, b) => a - b);
        
        // Add ellipses where there are gaps
        const finalPages = [];
        let lastPage = 0;
        for (const page of sortedPages) {
            if (lastPage !== 0 && page - lastPage > 1) {
                finalPages.push('...');
            }
            finalPages.push(page);
            lastPage = page;
        }
        
        return finalPages;
    }

    // NEW updatePaginationControls function with ellipsis logic
    function updatePaginationControls() {
        const totalItems = filteredVirulenceData.length;
        const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(totalItems / itemsPerPage);

        paginationControls.innerHTML = '';
        if (totalPages <= 1) {
            paginationInfo.textContent = totalItems > 0 ? `Showing all ${totalItems} entries` : 'No entries found';
            return;
        }

        // --- Build Pagination Links using the new logic ---
        let links = `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="goToPage(event, ${currentPage - 1})">Previous</a></li>`;
        
        const pageNumbers = createPaginationArray(currentPage, totalPages);

        pageNumbers.forEach(page => {
            if (page === '...') {
                // Use a span for the ellipsis and add the custom ellipsis class for styling
                links += `<li class="page-item ellipsis disabled"><span class="page-link">...</span></li>`;
            } else {
                links += `<li class="page-item ${page === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="goToPage(event, ${page})">${page}</a></li>`;
            }
        });

        links += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="goToPage(event, ${currentPage + 1})">Next</a></li>`;
        
        paginationControls.innerHTML = links;
        
        // --- Update Info Text ---
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = itemsPerPage === -1 ? totalItems : Math.min(startIndex + itemsPerPage, totalItems);
        paginationInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalItems} entries`;
    }

    function goToPage(event, page) {
        event.preventDefault(); // Prevent page from jumping to top
        const totalPages = itemsPerPage === -1 ? 1 : Math.ceil(filteredVirulenceData.length / itemsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    }

    function downloadData() {
        if (filteredVirulenceData.length === 0) {
            alert("No data to download based on current filters.");
            return;
        }
        const headers = [
            "prot_id", "sseqid", "Subject_Gene_Symbol", "Subject_Protein_Name",
            "VF_Category", "Operon_ID", "card_description", "amr_gene_family",
            "resistance_mechanism", "antibiotics", "VF_ID", "VFC_ID", "Organism_vfdbhit"
        ];
        
        const csvContent = [
            headers.join(','),
            ...filteredVirulenceData.map(row => headers.map(field => {
                let value = row[field];
                if (value === null || value === undefined) return '';
                // Enclose in quotes and escape existing quotes
                return `"${String(value).replace(/"/g, '""')}"`;
            }).join(','))
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        // Create a user-friendly filename
        const pathogenFileName = (pathogenNamesMap[currentPathogen] || currentPathogen).replace(/ /g, '_').toLowerCase();
        const filename = `${pathogenFileName}_virulence_data.csv`;
        link.setAttribute("download", filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<?php include 'footer.php'; ?>