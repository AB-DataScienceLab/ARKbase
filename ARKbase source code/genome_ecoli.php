<?php include('header.php')?>

<style>

    /* Tab Styling */
    .tab-panel { margin: 40px 0; }
    .tab-nav { display: flex; list-style: none; padding: 0; margin: 0 0 -2px 0; border-bottom: 2px solid #ccc; }
    
    /* Base style for all tab buttons */
    .tab-nav li {
        padding: 10px 20px;
        cursor: pointer;
        font-size: 1.1em;
        font-weight: 500;
        color: #333;
        border: 2px solid transparent;
        border-radius: 6px 6px 0 0;
        margin-right: 5px;
        transition: filter 0.2s ease-in-out;
    }
.header {
    position: relative; 
    z-index: .

*   **If the dropdown now works:** This proves **100%** that the problem is inside your page1050; /* Problematic */
}
    /* Assign background colors to each specific tab button */
    .tab-nav li[data-category='Access'] { background-color: #c9e1cf; border-color: #b8d0be; }
    .tab-nav li[data-category='Watch'] { background-color: #fcf0cb; border-color: #ebdca9; }
    .tab-nav li[data-category='Reserve'] { background-color: #f8d7da; border-color: #e7c6c9; }
    .tab-nav li[data-category='Access/Watch'] { background-color: #edbe80; border-color: #dcae70; }
    .tab-nav li[data-category='Watch/Reserve'] { background-color: #afc3f0; border-color: #9eafda; }
    .tab-nav li[data-category='Unclassified'] { background-color: #e2e3e5; border-color: #d1d2d4; }

    /* Style for the currently active tab */
   /* Style for the currently active tab */
/* Style for the currently active tab */
.tab-nav li.active {
    color: var(--arkbase-primary);
   
    font-weight: bold;
}
    .tab-nav li:not(.active):hover {
        filter: brightness(95%);
    }
    
    .tab-content { background-color: white; padding: 25px; border: 2px solid #ccc; border-radius: 0 8px 8px 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .tab-content .tab-pane { display: none; }
    .tab-content .tab-pane.active { display: block; }
    
    /* Category Heading Style */
    .tab-content h2 { background-color: var(--arkbase-secondary); color: white; font-size: 1.2rem; font-weight: 500; padding: 8px 15px; margin-bottom: 20px; border-radius: 4px; }
    
    /* Table Header Style */
    table.display thead th { background-color: var(--arkbase-primary); color: white; }

    /* --- THE DEFINITIVE FIX --- */
    
    /* STEP 1: Reset the default DataTables striping. This makes all rows neutral. */
    table.display tbody tr.odd td,
    table.display tbody tr.even td {
        background-color: white !important; /* Force a plain white background first */
    }

    /* STEP 2: Apply our category colors. They will now look identical because they start from the same white base. */
    #tab-access .display tbody tr td { background-color: #c9e1cf !important; }
    #tab-watch .display tbody tr td { background-color: #fcf0cb !important; }
    #tab-reserve .display tbody tr td { background-color: #f8d7da !important; }
    #tab-accesswatch .display tbody tr td { background-color: #edbe80 !important; }
    #tab-watchreserve .display tbody tr td { background-color: #afc3f0 !important; }
    #tab-unclassified .display tbody tr td { background-color: #e2e3e5 !important; }
    
    /* A single hover effect for all rows */
    table.display tbody tr:hover td { filter: brightness(90%) !important; }

    /* Neutral Phenotype Filter Buttons */
    .phenotype-filters {
        display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;
        padding: 10px; background-color: #f8f9fa; border-radius: 6px;
    }
    .phenotype-filters button {
        border: 1px solid #ccc; border-radius: 5px; padding: 5px 15px;
        font-weight: 500; transition: all 0.2s ease-in-out; cursor: pointer;
        background-color: #fff; color: #333;
    }
    .phenotype-filters button.filter-all {
        background-color: #6c757d; color: white; border-color: #6c757d;
    }
    .phenotype-filters button:hover { background-color: #f0f0f0; }
    .phenotype-filters button.active {
        transform: scale(1.05); box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.4);
        border-color: #007bff;
    }
</style>

<div class="main-content">
    <div class="container">
        
        <!-- Phenotype Filter Controls -->
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
                <!-- DataTables will be inserted here by the script -->
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
<link rel="manifest" href="/site.webmanifest">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- JS LIBRARIES & SCRIPT -->



<script>
$(document).ready(function () {
    var tables = {};
    var currentPhenotypeFilter = 'All';

    function initializeDataTable(tableSelector, category) {
        if (tables[tableSelector]) return tables[tableSelector];
        
        var newTable = $(tableSelector).DataTable({
            "processing": true,
            "serverSide": true,
            "paging": true,
            "pagingType": "numbers",
            "lengthChange": true,
            "info": true,
            "ajax": {
                "url": "datahandler_ecoli.php",
                "type": "POST",
                "data": function(d) {
                    d.category = category;
                    d.phenotypeFilter = currentPhenotypeFilter;
                }
            },
            "columns": [
                { "data": "Assembly_Accession" }, { "data": "Isolate_acession" },
                { "data": "Antibiotic" }, { "data": "Antibiotic_Class" }, { "data": "Phenotype" }
            ],
        });
        tables[tableSelector] = newTable;
        return newTable;
    }

    var activeTable = initializeDataTable('#table_access', 'Access');

    $('.phenotype-filters button').on('click', function() {
        var newFilter = $(this).data('phenotype');
        currentPhenotypeFilter = newFilter;
        $('.phenotype-filters button').removeClass('active');
        $(this).addClass('active');
        if (activeTable) {
            activeTable.ajax.reload();
        }
    });

    $('.tab-nav li').on('click', function () {
        var tab = $(this);
        if (tab.hasClass('active')) return;

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
        
        activeTable.columns.adjust().draw();
    });
});
</script>

<?php  include('footer.php'); ?>