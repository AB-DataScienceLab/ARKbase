<?php include 'header.php'; ?>

<style>
    /* --- Base Styles & Color Palette --- */
    :root {
        --aware-access-bg: #C9E1CF; --aware-watch-bg: #FCF0CB; --aware-watch-reserve-bg: #AFC3F0; --aware-reserve-bg: #F8D7DA; --aware-uncategorized-bg: #E2E3E5; --aware-access-watch-bg: #EDBE80;
        --aware-access-text: #2B5738; --aware-watch-text: #66561D; --aware-watch-reserve-text: #213B6B; --aware-reserve-text: #73373D; --aware-uncategorized-text: #54585C; --aware-access-watch-text: #795221;
        --border-color: #dee2e6; --primary-bg: #f8f9fa; --accent-blue: #0d6efd; --accent-blue-light: #e7f1ff;
    }
    .antibiotics-page-wrapper { background-color: var(--primary-bg); padding: 2rem 1rem; }
    .database-container { display: flex; width: 100%; max-width: 1200px; height: 85vh; max-height: 800px; background: #ffffff; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; margin: 0 auto; }
    
    /* ... other styles are the same ... */
    .left-panel { width: 35%; max-width: 350px; border-right: 1px solid var(--border-color); display: flex; flex-direction: column; }
    .left-panel-header { padding: 16px; border-bottom: 1px solid var(--border-color); }
    .left-panel-header h2 { margin: 0 0 16px 0; font-size: 1.25rem; }
    #search-pathogens { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 1rem; box-sizing: border-box; margin-bottom: 12px; }
    #pathogen-list { list-style: none; padding: 8px 0; margin: 0; overflow-y: auto; flex-grow: 1; }
    #pathogen-list li { padding: 14px 16px; cursor: pointer; border-left: 4px solid transparent; border-bottom: 1px solid #f1f3f5; }
    #pathogen-list li:hover { background-color: var(--primary-bg); }
    #pathogen-list li.active { background-color: var(--accent-blue-light); border-left-color: var(--accent-blue); font-weight: 600; }
    #pathogen-list li .pathogen-name { display: block; font-size: 1.05rem; margin-bottom: 8px; }
    .category-counts { display: flex; flex-wrap: wrap; gap: 6px; }
    .count-badge { font-size: 0.75rem; padding: 3px 8px; border-radius: 12px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
    .count-badge.access { background-color: var(--aware-access-bg); color: var(--aware-access-text); }
    .count-badge.watch { background-color: var(--aware-watch-bg); color: var(--aware-watch-text); }
    .count-badge.watch-reserve { background-color: var(--aware-watch-reserve-bg); color: var(--aware-watch-reserve-text); }
    .count-badge.reserve { background-color: var(--aware-reserve-bg); color: var(--aware-reserve-text); }
    .count-badge.uncategorized { background-color: var(--aware-uncategorized-bg); color: var(--aware-uncategorized-text); }
    .count-badge.access-watch { background-color: var(--aware-access-watch-bg); color: var(--aware-access-watch-text); }
    .count-badge.active-filter { transform: translateY(-2px); box-shadow: 0 0 0 2px #343a40; font-weight: 700; }
    .download-button { width: 100%; padding: 10px; font-size: 0.95rem; font-weight: 600; color: #fff; background-color: #28a745; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.2s; }
    .download-button:hover { background-color: #218838; }
    .right-panel { width: 65%; padding: 24px 32px; overflow-y: auto; background-color: var(--primary-bg); }
    .pathogen-details-header { margin-bottom: 24px; padding: 16px; background-color: #fff; border-radius: 8px; border: 1px solid var(--border-color); }
    .pathogen-details-header h1 { margin: 0; font-size: 2rem; }
    .pathogen-details-header p { margin: 4px 0 0 0; font-size: 1rem; color: #6c757d; }
    .antibiotic-card { border: 2px solid transparent; border-radius: 8px; padding: 20px; margin-bottom: 16px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .antibiotic-card.aware-access { background-color: var(--aware-access-bg); }
    .antibiotic-card.aware-watch { background-color: var(--aware-watch-bg); }
    .antibiotic-card.aware-watch-reserve { background-color: var(--aware-watch-reserve-bg); }
    .antibiotic-card.aware-reserve { background-color: var(--aware-reserve-bg); }
    .antibiotic-card.aware-uncategorized { background-color: var(--aware-uncategorized-bg); }
    .antibiotic-card.aware-access-watch { background-color: var(--aware-access-watch-bg); }
    .card-top-section { display: flex; gap: 20px; margin-bottom: 20px; }
    .card-info { flex-grow: 1; }
    .antibiotic-name h3 { margin: 0; font-size: 1.5rem; }
    .structure-container canvas { border-radius: 4px; background: rgba(255,255,255,0.6); }
    .structure-container .no-structure-placeholder { display: flex; align-items: center; justify-content: center; width: 250px; height: 200px; border: 1px dashed var(--border-color); border-radius: 4px; background-color: rgba(255,255,255,0.4); color: #6c757d; font-style: italic; }
    .breakpoints h4 { margin-top: 20px; margin-bottom: 12px; font-size: 1rem; color: #343a40; }
    .breakpoint-row { display: flex; justify-content: space-between; padding: 8px; border-radius: 5px; margin-bottom: 6px; background-color: rgba(255,255,255,0.5); border: 1px solid #e9ecef; color: #212529; }
    .breakpoint-value { font-weight: 600; }
    .smiles-accordion .accordion-header { cursor: pointer; font-weight: 600; margin-top: 16px; display: flex; align-items: center; }
    .smiles-accordion .accordion-header::before { content: '▶'; margin-right: 8px; font-size: 0.8em; transition: transform 0.2s ease-in-out; }
    .smiles-accordion.open .accordion-header::before { transform: rotate(90deg); }
    .smiles-accordion .accordion-content { display: none; margin-top: 8px; padding: 12px; background: rgba(255,255,255,0.7); border-radius: 4px; word-break: break-all; font-family: monospace; font-size: 0.85rem; }
    .smiles-accordion.open .accordion-content { display: block; }
    .attribution-footer { text-align: center; padding: 20px 0; font-size: 0.85rem; color: #6c757d; }
    .attribution-footer a { color: #5865f2; text-decoration: none; }
    .attribution-footer a:hover { text-decoration: underline; }
    /* UPDATED: Add a separator for citation links */
    .attribution-footer span { margin: 0 8px; }
</style>

<!-- Main content wrapper -->
<div class="antibiotics-page-wrapper">
    <div class="database-container">
        <aside class="left-panel">
            <div class="left-panel-header">
                <h2>Known Antibiotics</h2>
                <input type="text" id="search-pathogens" placeholder="Search pathogens...">
                <button id="download-csv-btn" class="download-button">Download as CSV</button>
            </div>
            <ul id="pathogen-list"></ul>
        </aside>
        <main class="right-panel" id="pathogen-details"></main>
    </div>

    <!-- ==== UPDATED: Attribution Footer HTML ==== -->
    <div class="attribution-footer">
        Structure visualization created with 
        <a href="https://doi.org/10.1021/acs.jcim.7b00425" target="_blank" rel="noopener noreferrer">SMILES Drawer</a>.<span>|</span>
        Breakpoint data from 
        <a href="https://www.clsi.org" target="_blank" rel="noopener noreferrer">CLSI</a>.<span>|</span>
        AWARE classifications from the 
        <a href="https://www.who.int/publications/i/item/WHO-MHP-HPS-EML-2023.04" target="_blank" rel="noopener noreferrer">WHO AWaRe list 2023</a>.<span>|</span>
        SMILES data sourced from 
        <a href="https://pubchem.ncbi.nlm.nih.gov/" target="_blank" rel="noopener noreferrer">PubChem</a>.
    </div>
</div>

<!-- External script for drawing chemical structures -->
<script src="https://unpkg.com/smiles-drawer@2.0.1/dist/smiles-drawer.min.js"></script>

<script>
    // --- GLOBAL VARIABLES & CONSTANTS ---
    const pathogenList = document.getElementById('pathogen-list');
    const pathogenDetails = document.getElementById('pathogen-details');
    const searchInput = document.getElementById('search-pathogens');
    const downloadBtn = document.getElementById('download-csv-btn');
    const smilesDrawer = new SmilesDrawer.Drawer({ width: 250, height: 200 });
    let dbData = {};
    let rawDataForDownload = [];

    // --- DATA & HELPER FUNCTIONS ---
    function categoryToCssClass(category) { return (category ? category.toLowerCase().replace(/[^a-z0-9]/g, '-') : 'uncategorized'); }
    function processData(rawData) { /* ... no change ... */ }
    
    // --- UI RENDERING FUNCTIONS ---
    function renderDetails(key, categoryFilter = null) { /* ... no change ... */ }

    /**
     * Initializes the pathogen list in the left panel.
     * UPDATED: Now sorts the category badges according to a predefined order.
     */
    function initializeUI() {
        pathogenList.innerHTML = '';
        
        // Define the desired display order for categories
        const categoryOrder = ["Access", "Watch", "Reserve", "Access/Watch", "Watch/Reserve", "Uncategorized"];

        Object.keys(dbData).forEach(key => {
            const pathogen = dbData[key];
            const li = document.createElement('li');
            li.dataset.key = key;

            // Build the counts HTML by iterating through the ordered list
            let countsHTML = '<div class="category-counts">';
            categoryOrder.forEach(category => {
                // Check if the pathogen has antibiotics in this category
                if (pathogen.categoryCounts[category]) {
                    const count = pathogen.categoryCounts[category];
                    const badgeClass = categoryToCssClass(category);
                    countsHTML += `<span class="count-badge ${badgeClass}" data-category="${category}">${category}: ${count}</span>`;
                }
            });
            countsHTML += '</div>';
            
            li.innerHTML = `<span class="pathogen-name"><em>${pathogen.displayName}</em></span>${countsHTML}`;
            pathogenList.appendChild(li);
        });

        const firstPathogen = pathogenList.querySelector('li');
        if (firstPathogen) {
            firstPathogen.classList.add('active');
            renderDetails(firstPathogen.dataset.key);
        }
    }
    
    // --- EVENT LISTENERS & APP START (No changes here) ---
    function downloadAsCsv() { /* ... no change ... */ }
    pathogenList.addEventListener('click', e => { /* ... no change ... */ });
    pathogenDetails.addEventListener('click', e => { /* ... no change ... */ });
    searchInput.addEventListener('input', e => { /* ... no change ... */ });
    downloadBtn.addEventListener('click', downloadAsCsv);
    document.addEventListener('DOMContentLoaded', () => { /* ... no change ... */ });

    // --- Stubs for unchanged functions to keep it runnable ---
    processData = (rawData) => { const pathogens = {}; rawData.forEach(row => { const key = row.pathogen_name; if (!pathogens[key]) { pathogens[key] = { displayName: key.replace(/_/g, ' '), species: row["Organism/Organism Group"], antibiotics: [], categoryCounts: {} }; } const category = row.Category || "Uncategorized"; pathogens[key].antibiotics.push({ name: row["DRUG NAME"], category: category, breakpoints: { clsi: { s: row["CLSI <= S"], i: row["CLSI = I/SDD"], r: row["CLSI >= R"] } }, smiles: row["SMILES"] }); pathogens[key].categoryCounts[category] = (pathogens[key].categoryCounts[category] || 0) + 1; }); return pathogens; };
    renderDetails = (key, categoryFilter = null) => { const pathogen = dbData[key]; if (!pathogen) return; const antibioticsToRender = categoryFilter ? pathogen.antibiotics.filter(ab => ab.category === categoryFilter) : pathogen.antibiotics; let detailsHTML = `<div class="pathogen-details-header"><h1><em>${pathogen.displayName}</em></h1><p><em>${pathogen.species}</em></p></div>`; if (antibioticsToRender.length === 0) { detailsHTML += `<p>No antibiotics match the selected filter.</p>`; } else { antibioticsToRender.forEach(antibiotic => { const canvasId = `canvas-${antibiotic.name.replace(/[^a-zA-Z0-9]/g, '-')}`; const cardClass = categoryToCssClass(antibiotic.category); let structureContentHTML = antibiotic.smiles ? `<canvas id="${canvasId}"></canvas>` : `<div class="no-structure-placeholder">Not Available</div>`; detailsHTML += `<div class="antibiotic-card aware-${cardClass}"><div class="card-top-section"><div class="card-info"><div class="antibiotic-name"><h3>${antibiotic.name}</h3></div><div class="breakpoints"><h4>CLSI Breakpoints (µg/mL)</h4><div class="breakpoint-row"><span>Susceptible (≤S)</span><span class="breakpoint-value">${antibiotic.breakpoints.clsi.s}</span></div><div class="breakpoint-row"><span>Intermediate (I/SDD)</span><span class="breakpoint-value">${antibiotic.breakpoints.clsi.i}</span></div><div class="breakpoint-row"><span>Resistant (≥R)</span><span class="breakpoint-value">${antibiotic.breakpoints.clsi.r}</span></div></div></div><div class="structure-container">${structureContentHTML}</div></div><div class="smiles-accordion"><div class="accordion-header">SMILES Structure Data</div><div class="accordion-content">${antibiotic.smiles || 'Not Available'}</div></div></div>`; }); } pathogenDetails.innerHTML = detailsHTML; antibioticsToRender.forEach(antibiotic => { if (antibiotic.smiles) { const canvasId = `canvas-${antibiotic.name.replace(/[^a-zA-Z0-9]/g, '-')}`; try { SmilesDrawer.parse(antibiotic.smiles, tree => smilesDrawer.draw(tree, canvasId, 'light', false)); } catch (err) { console.error("SMILES-drawer error:", err); } } }); };
    downloadAsCsv = () => { if (rawDataForDownload.length === 0) { alert("Data not available."); return; } const headers = Object.keys(rawDataForDownload[0]); const csvString = [headers.join(','), ...rawDataForDownload.map(row => headers.map(header => `"${String(row[header] ?? '').replace(/"/g, '""')}"`).join(','))].join('\n'); const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' }); const link = document.createElement('a'); const url = URL.createObjectURL(blob); link.setAttribute('href', url); link.setAttribute('download', 'known_antibiotics_data.csv'); document.body.appendChild(link); link.click(); document.body.removeChild(link); };
    pathogenList.addEventListener('click', e => { const badge = e.target.closest('.count-badge'); const listItem = e.target.closest('li'); if (!listItem) return; pathogenList.querySelector('.active')?.classList.remove('active'); listItem.classList.add('active'); pathogenList.querySelectorAll('.active-filter').forEach(b => b.classList.remove('active-filter')); const pathogenKey = listItem.dataset.key; if (badge) { badge.classList.add('active-filter'); const categoryFilter = badge.dataset.category; renderDetails(pathogenKey, categoryFilter); } else { renderDetails(pathogenKey, null); } });
    pathogenDetails.addEventListener('click', e => { const header = e.target.closest('.accordion-header'); if(header) header.parentElement.classList.toggle('open'); });
    searchInput.addEventListener('input', e => { const searchTerm = e.target.value.toLowerCase(); pathogenList.querySelectorAll('li').forEach(item => { const name = item.querySelector('.pathogen-name').textContent.toLowerCase(); item.style.display = name.includes(searchTerm) ? '' : 'none'; }); });
    downloadBtn.addEventListener('click', downloadAsCsv);
    document.addEventListener('DOMContentLoaded', () => { fetch('data.json').then(response => { if (!response.ok) throw new Error('Network error'); return response.json(); }).then(data => { rawDataForDownload = data; dbData = processData(data); initializeUI(); }).catch(error => { console.error('Fetch error:', error); pathogenDetails.innerHTML = `<p style="color: red;">Error loading data.</p>`; }); });
</script>

<?php include 'footer.php'; ?>