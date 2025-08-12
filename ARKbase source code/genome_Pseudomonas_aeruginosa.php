<?php include('header.php'); ?>

<style>
    /* Tab Styling */
    .tab-panel { margin: 40px 0; }
    .tab-nav { display: flex; list-style: none; padding: 0; margin: 0 0 -2px 0; border-bottom: 2px solid #ccc; }
    .tab-nav li { padding: 10px 20px; cursor: pointer; font-size: 1.1em; font-weight: 500; color: var(--arkbase-secondary); border: 2px solid transparent; }
    .tab-nav li.active { color: var(--arkbase-primary); border-color: #ccc; border-bottom-color: white; background-color: white; border-radius: 6px 6px 0 0; font-weight: bold; }
    .tab-content { background-color: white; padding: 25px; border: 2px solid #ccc; border-radius: 0 8px 8px 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .tab-content .tab-pane { display: none; }
    .tab-content .tab-pane.active { display: block; }
    
    /* Category Heading Style */
    .tab-content h2 { background-color: var(--arkbase-secondary); color: white; font-size: 1.2rem; font-weight: 500; padding: 8px 15px; margin-bottom: 20px; border-radius: 4px; }
    
    /* Table Header Style */
    table.display thead th { background-color: var(--arkbase-primary); color: white; }

    /* Phenotype Row Colors */
    tr.phenotype-susceptible td { background-color: #d4edda !important; color: #155724 !important; }
    tr.phenotype-resistant td { background-color: #f8d7da !important; color: #721c24 !important; }
    tr.phenotype-intermediate td { background-color: #fff3cd !important; color: #856404 !important; }
    table.display tbody tr:hover td { filter: brightness(95%); }

    /* --- NEW: Phenotype Filter Buttons --- */
    .phenotype-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 6px;
    }
    .phenotype-filters button {
        border: 2px solid transparent;
        border-radius: 5px;
        padding: 5px 15px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
    }
    .phenotype-filters button.filter-all { background-color: #6c757d; color: white; }
    .phenotype-filters button.filter-susceptible { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
    .phenotype-filters button.filter-resistant { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    .phenotype-filters button.filter-intermediate { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
    
    /* Style for the active filter button */
    .phenotype-filters button.active {
        transform: scale(1.05);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.4);
    }
</style>

<div class="main-content">
    <div class="container">
        
        <!-- NEW: Phenotype Filter Controls -->
        <div class="phenotype-filters">
            <button class="filter-all active" data-phenotype="All">Show All</button>
            <button class="filter-susceptible" data-phenotype="Susceptible">Susceptible</button>
            <button class="filter-resistant" data-phenotype="Resistant">Resistant</button>
            <button class="filter-intermediate" data-phenotype="Intermediate">Intermediate</button>
        </div>

        <div class="tab-panel">
           <ul class="tab-nav">
    <li class="active" data-category="Access" data-target-table="#table_access">Access</li>
    <li data-category="Watch" data-target-table="#table_watch">Watch</li>
    <li data-category="Reserve" data-target-table="#table_reserve">Reserve</li>
    <li data-category="Access/Watch" data-target-table="#table_accesswatch">Access/Watch</li>
    <li data-category="Watch/Reserve" data-target-table="#table_watchreserve">Watch/Reserve</li>
    <li data-category="Unclassified" data-target-table="#table_unclassified">Unclassified</li>
</ul>


            <div class="tab-content">

    <!-- Access -->
    <div id="tab-access" class="tab-pane active">
        <h2>AWaRe Category: Access</h2>
        <table id="table_access" class="display" style="width:100%">
            <thead><tr><th>Assembly Accession</th><th>Isolate Accession</th><th>Antibiotic</th><th>Antibiotic Class</th><th>Phenotype</th></tr></thead>
        </table>
    </div>

    <!-- Watch -->
    <div id="tab-watch" class="tab-pane">
        <h2>AWaRe Category: Watch</h2>
        <table id="table_watch" class="display" style="width:100%">
            <thead><tr><th>Assembly Accession</th><th>Isolate Accession</th><th>Antibiotic</th><th>Antibiotic Class</th><th>Phenotype</th></tr></thead>
        </table>
    </div>

    <!-- Reserve -->
    <div id="tab-reserve" class="tab-pane">
        <h2>AWaRe Category: Reserve</h2>
        <table id="table_reserve" class="display" style="width:100%">
            <thead><tr><th>Assembly Accession</th><th>Isolate Accession</th><th>Antibiotic</th><th>Antibiotic Class</th><th>Phenotype</th></tr></thead>
        </table>
    </div>

    <!-- Access/Watch -->
    <div id="tab-accesswatch" class="tab-pane">
        <h2>AWaRe Category: Access/Watch</h2>
        <table id="table_accesswatch" class="display" style="width:100%">
            <thead><tr><th>Assembly Accession</th><th>Isolate Accession</th><th>Antibiotic</th><th>Antibiotic Class</th><th>Phenotype</th></tr></thead>
        </table>
    </div>

    <!-- Watch/Reserve -->
    <div id="tab-watchreserve" class="tab-pane">
        <h2>AWaRe Category: Watch/Reserve</h2>
        <table id="table_watchreserve" class="display" style="width:100%">
            <thead><tr><th>Assembly Accession</th><th>Isolate Accession</th><th>Antibiotic</th><th>Antibiotic Class</th><th>Phenotype</th></tr></thead>
        </table>
    </div>

    <!-- Unclassified -->
    <div id="tab-unclassified" class="tab-pane">
        <h2>AWaRe Category: Unclassified</h2>
        <table id="table_unclassified" class="display" style="width:100%">
            <thead><tr><th>Assembly Accession</th><th>Isolate Accession</th><th>Antibiotic</th><th>Antibiotic Class</th><th>Phenotype</th></tr></thead>
        </table>
    </div>

</div>

        </div>
    </div>
</div>

<!-- JS LIBRARIES & SCRIPT -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {
    var tables = {};
    var currentPhenotypeFilter = 'All'; // To store the state of the active filter

    function initializeDataTable(tableSelector, category) {
        if (tables[tableSelector]) return tables[tableSelector];
        
        var newTable = $(tableSelector).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "datahandler_Pseudomonas_aeruginosa.php",
                "type": "POST",
                // UPDATED: 'data' is now a function to send the latest filter state
                "data": function(d) {
                    d.category = category;
                    d.phenotypeFilter = currentPhenotypeFilter;
                }
            },
            "columns": [
                { "data": "Assembly_Accession" }, { "data": "Isolate_acession" },
                { "data": "Antibiotic" }, { "data": "Antibiotic_Class" }, { "data": "Phenotype" }
            ],
            "createdRow": function(row, data, dataIndex) {
                var phenotype = data.Phenotype ? data.Phenotype.toLowerCase() : 'unknown';
                $(row).addClass('phenotype-' + phenotype);
            },
            "pageLength": 10,
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
        });
        tables[tableSelector] = newTable;
        return newTable;
    }

    var activeTable = initializeDataTable('#table_access', 'Access');

    // NEW: Click handler for the phenotype filter buttons
    $('.phenotype-filters button').on('click', function() {
        var newFilter = $(this).data('phenotype');
        
        // Update the global filter state
        currentPhenotypeFilter = newFilter;

        // Update the active button style
        $('.phenotype-filters button').removeClass('active');
        $(this).addClass('active');

        // Reload the currently active table with the new filter
        if (activeTable) {
            activeTable.ajax.reload();
        }
    });

    // UPDATED: Tab switching logic now resets the phenotype filter
    $('.tab-nav li').on('click', function () {
        var tab = $(this);
        if (tab.hasClass('active')) return;

        // Reset the phenotype filter to 'All' when switching tabs
        currentPhenotypeFilter = 'All';
        $('.phenotype-filters button').removeClass('active');
        $('.phenotype-filters .filter-all').addClass('active');
        
        $('.tab-nav li').removeClass('active');
        tab.addClass('active');
        
        var tableSelector = tab.data('target-table');
        $('.tab-content .tab-pane').removeClass('active');
        $(tableSelector).closest('.tab-pane').addClass('active');
        
        var category = tab.data('category');
        activeTable = initializeDataTable(tableSelector, category);
        
        // Redraw table, which will now use the reset filter
        activeTable.columns.adjust().draw();
    });
});
</script>

<?php // include('footer.php'); ?>