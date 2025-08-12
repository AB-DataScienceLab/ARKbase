<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug-Target Insights for Haemophilus influenzae</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

    <!-- Cytoscape.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.23.0/cytoscape.min.js"></script>
    <script src="https://unpkg.com/popper.js@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://unpkg.com/tippy.js@6.3.7/dist/tippy-bundle.umd.min.js"></script>

  <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --warning-color: #fd7e14;
            --dark-color: #212529;
            --blue-color: #0d6efd;
            --green-color: #198754;
        }

        body {
            font-family: 'Roboto';
            background-color: #f8f9fa;
        }

        .section-title {
            color: var(--dark-color);
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        /* --- Alignment for Top Row --- */
        .chart-container, .abstract-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            height: 550px; 
            display: flex;      
            flex-direction: column; 
        }

        .abstract-content {
             overflow-y: auto; 
             margin-bottom: 1rem;
        }
        
        .graphical-abstract-placeholder {
             flex-grow: 1; 
             background-color: #e9ecef;
             border: 1px dashed #ccc;
             border-radius: 5px;
             display: flex;
             align-items: center;
             justify-content: center;
             text-align: center;
             color: #6c757d;
             font-style: italic;
             min-height: 150px;
        }
        
        .go-chart-wrapper {
            position: relative; 
            flex-grow: 1;      
            min-height: 300px;
        }
        
        .go-chart-wrapper > div {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- Alignment for Bottom Row --- */
        .table-container, .network-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            height: 650px;
            display: flex;
            flex-direction: column;
        }

        .data-table {
            flex-grow: 1;
            overflow-y: auto;
            position: relative;
        }

        .network-graph-wrapper {
            position: relative;
            flex-grow: 1;
        }

        #cy {
            width: 100%;
            height: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .network-graph-wrapper > .text-center {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- General Styles --- */
        #drug-image-popup {
            position: absolute;
            display: none;
            padding: 5px;
            background: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }

        #drug-image-popup img {
            width: 150px;
            height: 150px;
        }

        .search-box {
            margin-bottom: 1rem;
        }

        .data-table th:first-child, .data-table td:first-child {
            width: 30px;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
        }
        
         .pagination .page-item.active .page-link {
             z-index: 3;
             color: #fff;
             background-color: var(--primary-color);
             border-color: var(--primary-color);
         }
         .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
         }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1>Drug-Target Interactions for <em> Haemophilus influenzae<em></h1>
                    <br>
<!--                    <p class="lead mb-0">Pathogen DTI Database - Comprehensive Drug-Target Analysis</p>-->
                </div>
<!--                <div class="col-md-4 d-flex align-items-center justify-content-end">-->
<!--                    <button class="btn btn-download" onclick="downloadData()">-->
<!--                        <i class="bi bi-download"></i> Download Current Page (.csv)-->
<!--                    </button>-->
<!--                </div>-->
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Overview Dashboard -->
        <section class="mb-5">
<!--            <h2 class="section-title">Overview Dashboard</h2>-->
            <div class="row">
                <!-- Abstract and Graphical Abstract Section -->
                <div class="col-lg-6">
                    <div class="abstract-container">
<!--                        <h4>Abstract</h4>-->
                        <div class="abstract-content">
<!--                             <p>This section contains a brief textual abstract summarizing the drug-target interactions for Escherichia coli. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>-->
                        </div>
                        <div class="graphical-abstract-placeholder">
<!--                            Graphical Abstract Placeholder Area-->
                            	<img src="images/ga/5-ARKbase.png" style="width:100%">
                            
                             
                        </div>
                    </div>
                </div>
                <!-- GO Chart Section (now for All Data) -->
                <div class="col-lg-6">
                    <div class="chart-container">
                        <h4 class="mb-3">Top 10 GO Entries (All Data)</h4>
                        <div class="btn-group mb-3" role="group" id="go-toggle-buttons" style="display: none;">
                            <input type="radio" class="btn-check" name="go-type" id="btn-molecular" autocomplete="off" checked>
                            <label class="btn btn-outline-primary" for="btn-molecular">Molecular Function</label>

                            <input type="radio" class="btn-check" name="go-type" id="btn-biological" autocomplete="off">
                            <label class="btn btn-outline-primary" for="btn-biological">Biological Process</label>

                            <input type="radio" class="btn-check" name="go-type" id="btn-cellular" autocomplete="off">
                            <label class="btn btn-outline-primary" for="btn-cellular">Cellular Component</label>
                        </div>
                         <!-- Loading indicator or placeholder -->
                        <div id="go-chart-loading" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>
                        <canvas id="goChart" style="display: none;"></canvas>
                        <div id="go-chart-error" class="text-danger text-center" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Explorer -->
        <section class="mb-5">
            <h2 class="section-title">Interactive Explorer</h2>
            <div class="row">
                <div class="col-lg-7">
                    <div class="table-container">
                        <h4 class="mb-3">Searchable Data Table (Current Page)</h4>
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput"
                                   placeholder="Search by Target, Drug, Protein, etc. (on current page)" disabled> <!-- Disable until data loads -->
                        </div>
                         <!-- Loading indicator or placeholder -->
                        <div id="table-loading" class="text-center mb-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>
                        <div class="data-table" style="display: none;">
                            <table class="table table-striped table-hover" id="dataTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th><input type="checkbox" id="selectAllCheckbox" disabled></th> <!-- Disable until data loads -->
                                        <th>Target ID</th>
                                        <th>DrugBank ID</th>
                                        <th>Drug Name</th>
                                        <th>Score</th>
                                        <th>Drug Type</th>
                                        <th>Protein Names</th>
                                        <th>Pathway</th>
                                        <th>Toxicity Safe</th> <!-- ADDED NEW COLUMN HEADER -->
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                     <!-- Table rows will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                         <div id="table-error" class="text-danger text-center" style="display: none;"></div>

                         <!-- Pagination Controls -->
                        <div class="pagination-container" id="pagination-controls" style="display: none;">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                    <li class="page-item" id="prev-page">
                                        <a class="page-link" href="#" aria-label="Previous">
                                            <span aria-hidden="true">Ãƒâ€šÃ‚Â«</span>
                                        </a>
                                    </li>
                                    <!-- Page numbers will be inserted here by JS - Optional: simple info used for now -->
                                     <li class="page-item disabled" id="page-info"><span class="page-link">Page <span id="current-page-span"></span> of <span id="total-pages-span"></span> (<span id="total-records-span"></span> records)</span></li>
                                    <li class="page-item" id="next-page">
                                        <a class="page-link" href="#" aria-label="Next">
                                            <span aria-hidden="true">Ãƒâ€šÃ‚Â»</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                             <div class="ms-3">
                                <select class="form-select form-select-sm" id="items-per-page-select">
                                    <option value="20" selected>20 items per page</option>
                                    <option value="30">30 items per page</option>
                                    <option value="50">50 items per page</option>
                                    <!-- Removed 100 as requested -->
                                </select>
                             </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="network-container">
                        <h4 class="mb-3">Target-Drug Interaction Network (Current Page)</h4>
                         <!-- Loading indicator or placeholder -->
                        <div id="network-loading" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>
                        <div id="cy" style="display: none;"></div>
                         <div id="network-error" class="text-danger text-center" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Floating image popup -->
    <div id="drug-image-popup"></div>

    <!-- Footer -->
<!--    <footer class="footer">-->
<!--        <div class="container text-center">-->
<!--            <p class="mb-1">ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¾ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¾ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¾ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬ÃƒÂ¢Ã¢â‚¬Å¾Ã‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã‚Â¦ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â© 2024 Bioinformatics Research Institute. This database is for research purposes only.</p>-->
<!--            <a href="#" class="text-light me-3">Contact Us</a>-->
<!--            <a href="#" class="text-light">Privacy Policy</a>-->
<!--        </div>-->
<!--    </footer>-->

    <script>
        // Global variables for fetched data and pagination state
        let dti_data = []; // Data for the *current page* only
        let filteredData = []; // Filtered data (subset of dti_data) - still filter on current page
        let currentPage = 1;
        let itemsPerPage = 20; // Default items per page
        let totalRecords = 0; // Total records matching the pathogen
        let totalPages = 0;

        // Global variable for GO data for *all* records
        let go_data_all_records = null; // Will store the aggregated GO counts

        // Chart and network instances
        let goChart;
        // summaryChart variable is no longer needed as the chart is removed
        let cy; // Cytoscape instance for the network graph

        // Color scheme
        const drugTypeColors = {
            'small molecule': 'var(--blue-color)',
            'peptide': 'var(--green-color)',
            'biotech': 'var(--warning-color)',
            'unknown': '#6c757d' // Default for unknown types
        };

        // URLs for fetching data
        const DATA_FETCH_URL = 'fetch_dti_hi.php'; // UPDATED FILENAME
        const GO_FETCH_URL = 'fetch_go_hi.php'; // For all GO data

        // Initialize the page - starts the data fetching process for the first page
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial items per page from the select dropdown
             const itemsPerPageSelect = document.getElementById('items-per-page-select');
             itemsPerPage = parseInt(itemsPerPageSelect.value);
             itemsPerPageSelect.addEventListener('change', handleItemsPerPageChange);

            // Fetch necessary data on load
            fetchInitialData();

            setupSearch(); // Set up search listener (will filter current page)
            setupTableCheckboxes(); // Set up checkbox listeners (will filter current page)
            setupGoToggle(); // Set up GO toggle listener (uses go_data_all_records)
        });

        // Function to fetch all necessary data on initial load
        async function fetchInitialData() {
            // Show loading for all sections initially
             showLoadingIndicators();
             hideErrorMessages();

            // Fetch GO data (for all records) - This should be fetched only once
            await fetchGoData();

            // Fetch table data (first page)
            await fetchDataPage(currentPage, itemsPerPage);

            // Hide all loading indicators after both fetches complete (or fail)
             hideLoadingIndicators();
             showContent(); // Adjusts visibility based on data presence

            // If data fetched successfully, enable controls
             if (totalRecords > 0) {
                 enableControls();
             } else {
                  // If no data, ensure controls remain disabled
                 disableControls();
             }
        }


        // --- Data Fetching ---

        // Fetch paginated table data
        async function fetchDataPage(page, limit) {
             console.log(`Fetching page ${page} with ${limit} items...`);
            // Only show loading for the table and network graph when changing page
             if (currentPage !== page) {
                 document.getElementById('table-loading').style.display = 'block';
                 document.querySelector('.data-table').style.display = 'none';
                 document.getElementById('network-loading').style.display = 'block';
                 document.getElementById('cy').style.display = 'none';
                  hideErrorMessages('table-error', 'network-error'); // Hide errors specific to table/network
                 disableControls(); // Disable controls during fetch
             }


            try {
                const response = await fetch(`${DATA_FETCH_URL}?page=${page}&limit=${limit}`);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json(); // Result is an object { total_records: ..., data: [...] }

                if (result.error) {
                     console.error("Server error fetching table data:", result.error);
                    throw new Error(`Server error fetching table data: ${result.error}`);
                }
                 // Removed the result.message check here, as an empty data array is valid for a page
                 // if the total_records is 0 or the page is beyond the total.
                 // updatePaginationControls and renderTable will handle showing the 'no data' message.


                // Data fetched successfully
                dti_data = result.data || []; // Assign fetched data (only for the current page), default to empty array
                filteredData = [...dti_data]; // Initialize filteredData with current page data
                totalRecords = result.total_records || 0; // Update total records count, default to 0
                itemsPerPage = limit; // Ensure itemsPerPage is updated to the requested limit
                totalPages = totalRecords > 0 ? Math.ceil(totalRecords / itemsPerPage) : 0; // Calculate total pages
                currentPage = page; // Update current page state

                console.log(`Fetched ${dti_data.length} items for page ${currentPage}. Total records: ${totalRecords}, Total pages: ${totalPages}`);

                // Update components with fetched data (for the current page)
                renderTable(); // Render table
                updatePaginationControls(); // Update pagination UI
                 // GO chart uses all data, but update triggers redraw based on global go_data_all_records
                createGoChart(document.querySelector('#go-toggle-buttons input:checked').id.replace('btn-', ''));
                createNetworkGraph(); // Create network graph for the current page's interactions


            } catch (error) {
                console.error("Failed to fetch table data:", error);
                showError(`Failed to load table data: ${error.message}`, 'table-error');
                showError(`Failed to load network graph: ${error.message}`, 'network-error');
                 // Clear data arrays and reset state on fetch error
                 dti_data = [];
                 filteredData = [];
                 totalRecords = 0;
                 totalPages = 0;
                 currentPage = 1;
                 renderTable(); // Render empty table
                 updatePaginationControls(); // Update controls for no data
                 createNetworkGraph(); // Destroy Cytoscape instance if it exists
            } finally {
                // Hide loading indicators for table and network graph
                 document.getElementById('table-loading').style.display = 'none';
                 document.querySelector('.data-table').style.display = 'block'; // Show table container even if empty
                 document.getElementById('network-loading').style.display = 'none';
                 if (dti_data.length > 0) {
                      document.getElementById('cy').style.display = 'block';
                 } else if (cy) { // If cy exists but no data on this page, destroy it
                     cy.destroy();
                      document.getElementById('cy').style.display = 'none';
                 }
                 enableControls(); // Enable controls after fetch attempt
            }
        }

         // Fetch GO data for ALL records
         async function fetchGoData() {
             console.log("Fetching GO data for all records...");
             document.getElementById('go-chart-loading').style.display = 'block';
             document.getElementById('goChart').style.display = 'none';
              document.getElementById('go-toggle-buttons').style.display = 'none';
              hideErrorMessages('go-chart-error'); // Hide GO specific error

             try {
                const response = await fetch(GO_FETCH_URL);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json(); // Result is the aggregated GO counts object

                if (result.error) {
                     console.error("Server error fetching GO data:", result.error);
                    throw new Error(`Server error fetching GO data: ${result.error}`);
                }

                go_data_all_records = result; // Store the fetched GO data

                console.log("GO data fetched successfully.");

                // Initialize/Update GO chart with all data
                 const selectedGoType = document.querySelector('#go-toggle-buttons input:checked').id.replace('btn-', '');
                 createGoChart(selectedGoType);


             } catch (error) {
                 console.error("Failed to fetch GO data:", error);
                 showError(`Failed to load GO chart data: ${error.message}`, 'go-chart-error');
                 go_data_all_records = null; // Ensure it's null on error
                 if (goChart) goChart.destroy(); // Destroy GO chart
                 const ctx = document.getElementById('goChart').getContext('2d');
                 ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); // Clear canvas
             } finally {
                 document.getElementById('go-chart-loading').style.display = 'none';
                 document.getElementById('go-toggle-buttons').style.display = 'flex'; // Always show buttons
                  if (go_data_all_records) { // Only show chart canvas if data was successfully loaded
                     document.getElementById('goChart').style.display = 'block';
                 } else {
                      document.getElementById('goChart').style.display = 'none';
                 }
             }
         }


        // --- Pagination Control Functions ---

        function updatePaginationControls() {
             const paginationContainer = document.getElementById('pagination-controls');
             if (!paginationContainer) return;

            // Display total records, current page, total pages
            document.getElementById('total-records-span').textContent = totalRecords;
            document.getElementById('current-page-span').textContent = totalRecords > 0 ? currentPage : 0; // Show 0 if no records
            document.getElementById('total-pages-span').textContent = totalPages;

            // Update Previous/Next button state
            const prevPageItem = document.getElementById('prev-page');
            const nextPageItem = document.getElementById('next-page');

            prevPageItem.classList.toggle('disabled', currentPage === 1 || totalRecords === 0);
            nextPageItem.classList.toggle('disabled', currentPage === totalPages || totalRecords === 0 || totalPages === 0); // Disable if total pages is 0

             // Handle visibility of pagination controls
             // Show controls if there are more records than items per page OR if total pages > 1 and total records > 0
             if (totalRecords > itemsPerPage || (totalRecords > 0 && totalPages > 1)) {
                 paginationContainer.style.display = 'flex';
            } else {
                paginationContainer.style.display = 'none'; // Hide if only one page or no data
            }
        }

         function handleItemsPerPageChange(event) {
             const newItemsPerPage = parseInt(event.target.value);
             if (newItemsPerPage !== itemsPerPage) { // Only fetch if items per page actually changed
                 itemsPerPage = newItemsPerPage;
                 currentPage = 1; // Reset to first page when items per page changes
                 fetchDataPage(currentPage, itemsPerPage);
             }
         }

        // Add event listeners for pagination buttons
        document.addEventListener('click', function(event) {
            const prevPageLink = event.target.closest('#prev-page .page-link');
            const nextPageLink = event.target.closest('#next-page .page-link');

            if (prevPageLink && !prevPageLink.parentElement.classList.contains('disabled')) {
                event.preventDefault(); // Prevent default link behavior
                fetchDataPage(currentPage - 1, itemsPerPage);
            } else if (nextPageLink && !nextPageLink.parentElement.classList.contains('disabled')) {
                 event.preventDefault(); // Prevent default link behavior
                fetchDataPage(currentPage + 1, itemsPerPage);
            }
        });


        // --- Loading and Error UI Management ---

        function showLoadingIndicators() {
             // Removed summary chart loading
             document.getElementById('go-chart-loading').style.display = 'block';
             document.getElementById('table-loading').style.display = 'block';
             document.getElementById('network-loading').style.display = 'block';

             // Removed summary chart canvas
             document.getElementById('goChart').style.display = 'none';
             document.getElementById('go-toggle-buttons').style.display = 'none'; // Hide GO buttons while loading GO data
             document.querySelector('.data-table').style.display = 'none';
             document.getElementById('cy').style.display = 'none';
             document.getElementById('pagination-controls').style.display = 'none'; // Hide pagination while loading
             disableControls(); // Disable search, select all
        }

         function hideLoadingIndicators() {
             // Removed summary chart loading
             // Loading for GO chart, table, and network graph are now handled within their specific fetch functions' finally blocks
             // This function might not be strictly necessary anymore after granular loading
         }

         function showContent() {
             // Show GO buttons regardless of data, as the chart might show 'No data' state
              document.getElementById('go-toggle-buttons').style.display = 'flex';

            // Only show table and network graph containers/content if there is data for the current page
            if (dti_data && dti_data.length > 0) {
                 // Removed summary chart canvas
                 // GO chart visibility handled within fetchGoData finally block
                 document.querySelector('.data-table').style.display = 'block'; // Show table container
                 document.getElementById('cy').style.display = 'block';
                 // Pagination visibility handled by updatePaginationControls

            } else {
                 // If no current page data, ensure content areas are hidden/show 'no data' messages
                 // Removed summary chart canvas
                 // GO chart visibility handled within fetchGoData finally block
                 document.querySelector('.data-table').style.display = 'block'; // Keep table div visible to show 'no data' message (will contain 'No data' message)
                 document.getElementById('cy').style.display = 'none';
                 document.getElementById('pagination-controls').style.display = 'none'; // Hide pagination
                 // Error messages might already be visible, keep them.
            }
            // Enable controls after showContent logic determines visibility
             enableControls();
        }

         function showError(message, elementId) {
             const errorElement = document.getElementById(elementId);
             if(errorElement) {
                 errorElement.textContent = message;
                 errorElement.style.display = 'block';
             }
         }

         function hideErrorMessages(...elementIds) {
             const idsToHide = elementIds.length > 0 ? elementIds : ['table-error', 'network-error', 'go-chart-error'];
              idsToHide.forEach(id => {
                  const element = document.getElementById(id);
                  if(element) {
                      element.style.display = 'none';
                  }
              });
         }


         function disableControls() {
              const searchInput = document.getElementById('searchInput');
              const selectAllCheckbox = document.getElementById('selectAllCheckbox');
              const itemsPerPageSelect = document.getElementById('items-per-page-select');
              if (searchInput) searchInput.disabled = true;
              if (selectAllCheckbox) selectAllCheckbox.disabled = true;
              if (itemsPerPageSelect) itemsPerPageSelect.disabled = true;
               // Also disable pagination buttons
               document.getElementById('prev-page').classList.add('disabled');
               document.getElementById('next-page').classList.add('disabled');
         }
          function enableControls() {
             // Only enable if there is data available (at least total records > 0)
              if (totalRecords > 0) {
                  const searchInput = document.getElementById('searchInput');
                  const selectAllCheckbox = document.getElementById('selectAllCheckbox');
                   const itemsPerPageSelect = document.getElementById('items-per-page-select');
                  if (searchInput) searchInput.disabled = false;
                   // selectAllCheckbox enable/disable is handled by setupTableCheckboxes based on rendered rows
                   if (itemsPerPageSelect) itemsPerPageSelect.disabled = false;
                  // Pagination buttons enabled/disabled by updatePaginationControls
              } else {
                  // Ensure they remain disabled if no data
                  disableControls();
              }
         }


        // SECTION 1 MODIFICATIONS (GO Chart uses all data)

        // createTargetSummaryChart function removed


        function setupGoToggle() {
             // Remove any previous listeners before adding
             const goToggleDiv = document.getElementById('go-toggle-buttons');
             const newGoToggleDiv = goToggleDiv.cloneNode(true);
             goToggleDiv.parentNode.replaceChild(newGoToggleDiv, goToggleDiv);

            newGoToggleDiv.addEventListener('click', (event) => {
                if (event.target.tagName === 'LABEL') {
                    const buttonId = event.target.getAttribute('for');
                    let type = 'molecular'; // default
                    if (buttonId === 'btn-biological') type = 'biological';
                    if (buttonId === 'btn-cellular') type = 'cellular';

                     // Create chart using the GO data for *all* records
                     createGoChart(type);
                }
            });
        }

        // createGoChart now uses go_data_all_records
        function createGoChart(goType) {
            // Use go_data_all_records
             if (!go_data_all_records || !go_data_all_records[goType] || Object.keys(go_data_all_records[goType]).length === 0) {
                 console.warn(`GO data for ${goType} (all records) not available or empty.`);
                 if (goChart) goChart.destroy();
                 const ctx = document.getElementById('goChart').getContext('2d');
                 ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height); // Clear canvas
                 showError(`No GO data available for ${goType}.`, 'go-chart-error');
                 return; // Don't create chart if no data
             }
             hideErrorMessages('go-chart-error'); // Hide error if data is available


            const goCounts = go_data_all_records[goType];

            const sortedTerms = Object.entries(goCounts)
                .sort((a, b) => b[1] - a[1])
                .slice(0, 10);

            const chartData = {
                labels: sortedTerms.map(([term, count]) => term),
                datasets: [{
                    label: 'Frequency (All Data)',
                    data: sortedTerms.map(([term, count]) => count),
                    backgroundColor: '#6610f2',
                    borderColor: '#6610f2', // Corrected typo from fuz
                    borderWidth: 1
                }]
            };

            const ctx = document.getElementById('goChart').getContext('2d');
            if (goChart) {
                // Update existing chart if it exists
                goChart.data = chartData;
                goChart.options.scales.y.title.text = `GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Term`;
                 goChart.options.plugins.title.text = `Top 10 GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Terms (All Data)`;
                goChart.update();
            } else {
                // Create new chart if it doesn't exist
                goChart = new Chart(ctx, {
                    type: 'bar',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y', // Horizontal bar chart
                        scales: {
                            x: {
                                beginAtZero: true,
                                title: { display: true, text: 'Frequency' },
                                ticks: { precision: 0 } // Ensure whole numbers
                            },
                            y: {
                                title: { display: true, text: `GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Term` }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                             title: {
                                display: true,
                                text: `Top 10 GO ${goType.charAt(0).toUpperCase() + goType.slice(1)} Terms (All Data)`,
                                padding: { bottom: 20 }
                             }
                        }
                    }
                });
            }
        }

        // SECTION 2 MODIFICATIONS (Table, Search, Network operate on current page's data)
        // These functions remain largely the same, operating on dti_data (current page)
        // and filteredData (search on current page)

        function initializeTable() {
            renderTable();
            setupTableRowHighlight();
        }

         function setupTableRowHighlight() {
             // Remove any previous listeners before adding
             document.querySelectorAll('#tableBody tr').forEach(row => {
                 // Simple way to remove listener - replace node with clone
                 const newRow = row.cloneNode(true);
                 row.parentNode.replaceChild(newRow, row);
             });

             // Add new listeners to the new nodes
             document.querySelectorAll('#tableBody tr').forEach(row => {
                row.addEventListener('click', (e) => {
                    // Don't trigger highlight if clicking checkbox or link
                    if (e.target.closest('input[type="checkbox"]') || e.target.closest('a')) {
                        return;
                    }
                    const interactionId = row.dataset.interactionId;
                    // Find the interaction in the *current page's* dti_data
                    const interaction = dti_data.find(d => d.interaction_id === interactionId);
                    if (interaction) {
                        highlightNetworkEdge(interaction.Target, interaction.Drug_Name);
                    }
                });
            });
         }


        function renderTable() {
             const tbody = document.getElementById('tableBody');
             if (!tbody) return;

            // Clear previous table rows
            tbody.innerHTML = '';

             // Determine which data to render: filteredData if search is active, otherwise dti_data
             const dataToRender = document.getElementById('searchInput').value ? filteredData : dti_data;


             if (dataToRender.length === 0) {
                 let message = 'No data available for this page.';
                 if (document.getElementById('searchInput').value) {
                     message = 'No results found on this page for your search term.';
                 } else if (totalRecords === 0) {
                      message = 'No data found for Escherichia coli in the database.';
                 } else if (totalRecords > 0 && dataToRender.length === 0) {
                      // This case happens if a page number is requested that has no data
                      message = `No data found on page ${currentPage}. Try navigating to another page.`;
                 }
                 tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">${message}</td></tr>`; // Colspan is 9 now with new column
             }
             else {
                // Render rows from dataToRender
                dataToRender.forEach(item => {
                     const row = document.createElement('tr');
                     row.dataset.interactionId = item.interaction_id; // Store ID on the row

                     row.innerHTML = `
                         <td><input type="checkbox" class="row-checkbox" data-interaction-id="${item.interaction_id}" ${dataToRender.length === 0 ? 'disabled' : ''}></td>
                         <td>${item.Target ? `<a href="https://www.uniprot.org/uniprot/${item.Target}" target="_blank">${item.Target}</a>` : 'N/A'}</td>
                         <td>${item['Drugbank.ID'] ? `<a href="https://go.drugbank.com/drugs/${item['Drugbank.ID']}" target="_blank">${item['Drugbank.ID']}</a>` : 'N/A'}</td>
                         <td>${item.Drug_Name || 'N/A'}</td>
                         <td>${item.Score !== null && item.Score !== undefined ? item.Score.toFixed(2) : 'N/A'}</td>
                         <td><span class="badge" style="background-color: ${drugTypeColors[item.Drug_Type] || drugTypeColors['unknown']}">${item.Drug_Type || 'N/A'}</span></td>
                         <td>${item['Protein.names'] || 'N/A'}</td>
                         <td>${item.Pathway ? item.Pathway.replace('PATHWAY: ', '') : 'N/A'}</td>
                         <td>${item.DeepPK_toxicity_Safe || 'N/A'}</td> <!-- ADDED NEW COLUMN DATA CELL -->
                     `;
                     tbody.appendChild(row);
                });
             }
             // Re-apply highlight listener after rendering
             setupTableRowHighlight();
             // Ensure checkboxes reflect current filtered state if any
             filterNetworkFromCheckboxes(); // This also updates checkbox state visually based on network filter

             // Enable/disable select all checkbox based on whether there are any rows to select
             document.getElementById('selectAllCheckbox').disabled = dataToRender.length === 0;
        }


         // Search only filters the data *currently loaded* (the current page)
        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
             if (!searchInput) return;

             // Remove previous listener to avoid duplicates
             const newSearchInput = searchInput.cloneNode(true);
             searchInput.parentNode.replaceChild(newSearchInput, searchInput);
             const currentSearchInput = newSearchInput; // Use the new element

            currentSearchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                if (!searchTerm) {
                    // If search term is empty, show all data for the current page
                    filteredData = [...dti_data];
                } else {
                    // Filter data based on search term - ONLY from the current page's data (dti_data)
                    filteredData = dti_data.filter(item =>
                        (item.Target && item.Target.toLowerCase().includes(searchTerm)) ||
                        (item['Drugbank.ID'] && item['Drugbank.ID'].toLowerCase().includes(searchTerm)) ||
                        (item.Drug_Name && item.Drug_Name.toLowerCase().includes(searchTerm)) ||
                        (item['Protein.names'] && item['Protein.names'].toLowerCase().includes(searchTerm)) ||
                        (item.Pathway && item.Pathway.toLowerCase().includes(searchTerm)) ||
                         // Include the new column in search
                         (item.DeepPK_toxicity_Safe && item.DeepPK_toxicity_Safe.toLowerCase().includes(searchTerm))
                    );
                }
                renderTable(); // Re-render table with filtered data
                // filterNetworkFromCheckboxes is called by renderTable internally
            });
        }


        function setupTableCheckboxes() {
            const dataTable = document.getElementById('dataTable');
            const selectAllCheckbox = document.getElementById('selectAllCheckbox');
             const tableBody = document.getElementById('tableBody');


             if (!dataTable || !selectAllCheckbox || !tableBody) return;

             // Remove previous listeners on selectAllCheckbox
             const newSelectAll = selectAllCheckbox.cloneNode(true);
             selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);
             const currentSelectAllCheckbox = newSelectAll;

             // Remove previous listeners on tableBody for delegation
             const newTableBody = tableBody.cloneNode(true);
             tableBody.parentNode.replaceChild(newTableBody, tableBody);
             const currentTableBody = newTableBody;


            // Listener for 'Select All' checkbox - now applies only to visible rows (current page)
            currentSelectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                // Get all checkboxes in the *currently rendered* table body (which is filteredData if search is active)
                // Use querySelector on tableBody to only get checkboxes within the visible rows
                const rowCheckboxes = currentTableBody.querySelectorAll('.row-checkbox');
                 rowCheckboxes.forEach(cb => {
                     cb.checked = isChecked;
                 });
                // Update network based on the new selection state of visible items
                 filterNetworkFromCheckboxes();
            });

            // Listener for individual row checkboxes (using event delegation on the table body)
            currentTableBody.addEventListener('change', function(e) {
                 if (e.target && e.target.matches('.row-checkbox')) {
                     // If any row checkbox state changes, update the network
                     filterNetworkFromCheckboxes();

                     // If a row checkbox is unchecked, uncheck the 'Select All' checkbox
                    if (!e.target.checked) {
                        currentSelectAllCheckbox.checked = false;
                    } else {
                        // Check if *all* currently visible (rendered) checkboxes are checked
                        const allVisibleCheckboxes = currentTableBody.querySelectorAll('.row-checkbox');
                        const allChecked = Array.from(allVisibleCheckboxes).length > 0 && Array.from(allVisibleCheckboxes).every(cb => cb.checked);
                        currentSelectAllCheckbox.checked = allChecked;
                    }
                 }
            });
        }


        function filterNetworkFromCheckboxes() {
            if (!cy || !dti_data) { // dti_data now contains only current page data
                console.warn("Cytoscape instance not ready or no data loaded.");
                // Ensure network is cleared if no data
                 if (cy) cy.elements().remove();
                return;
            }

            const checkedBoxes = document.querySelectorAll('#tableBody .row-checkbox:checked');
            // Get interaction IDs from the *currently checked* boxes in the rendered table
            const selectedInteractionIds = new Set(Array.from(checkedBoxes).map(cb => cb.dataset.interactionId));

             // Find the full interaction objects from the *current page's data* (dti_data)
            const selectedInteractions = dti_data.filter(d => selectedInteractionIds.has(d.interaction_id));

            // Identify all nodes (targets and drugs) involved in the selected interactions (on this page)
            const nodesToShow = new Set();
            selectedInteractions.forEach(interaction => {
                if (interaction.Target) nodesToShow.add(interaction.Target);
                if (interaction.Drug_Name) nodesToShow.add(interaction.Drug_Name);
            });

            // Control node/edge visibility in Cytoscape
            cy.elements().forEach(el => {
                if (el.isNode()) {
                    // Hide node if it's not in the set of nodes involved in selected interactions *on this page*
                    if (nodesToShow.has(el.id())) {
                         el.style('display', 'element');
                     } else {
                        el.style('display', 'none');
                     }
                } else if (el.isEdge()) {
                     // Hide edge if its interaction ID is NOT in the selected IDs (from this page)
                    if (selectedInteractionIds.has(el.data('id'))) {
                        el.style('display', 'element');
                    } else {
                        el.style('display', 'none');
                    }
                }
            });

             // If no checkboxes are checked, show all elements on the current page
             if (selectedInteractionIds.size === 0 && dti_data.length > 0) {
                 cy.elements().style('display', 'element');
             }

             // Optional: Re-layout visible elements if needed, but can be performance heavy
             // if (selectedInteractionIds.size > 0) {
             //     cy.layout({ name: 'cose', animate: true, padding: 10, fit: true, animateFilter: function( node, i ){ return true; } }).run();
             // } else if (dti_data.length > 0) {
             //      // If no checkboxes checked but data exists, show all current page elements
             //      cy.elements().style('display', 'element');
             //       cy.layout({ name: 'cose', animate: true, padding: 10 }).run(); // Re-layout the current page's data
             // }
        }


        function createNetworkGraph() {
             // Use dti_data (current page's data)
             if (!dti_data || dti_data.length === 0) {
                console.warn("No data available for network graph on current page.");
                 if (cy) {
                     cy.destroy(); // Destroy previous instance
                 }
                 document.getElementById('cy').style.display = 'none'; // Hide container
                 showError('No data available to build network graph for this page.', 'network-error');
                 return; // Don't initialize Cytoscape if no data
             }

             // Ensure network container is visible before initializing if data exists
             document.getElementById('cy').style.display = 'block';
             document.getElementById('network-error').style.display = 'none'; // Hide error if data is present


             // Destroy previous instance if it exists (important for re-initialization per page)
             if (cy) {
                 cy.destroy();
             }

            const nodes = [];
            const edges = [];
            const addedNodes = new Set();
            const drugDataMap = new Map(); // To store drug-specific info like type and image URL

            // Process data to build nodes and edges arrays - ONLY for dti_data (current page)
            dti_data.forEach(item => {
                 // Store drug type and image URL for quick lookup
                if (item.Drug_Name) { // Only map if drug name exists
                     drugDataMap.set(item.Drug_Name, { drugType: item.Drug_Type, imageUrl: item.drug_image_url });
                }

                // Add target node if not already added
                if (item.Target && !addedNodes.has(item.Target)) {
                    nodes.push({ data: { id: item.Target, label: item.Target, type: 'target' } });
                    addedNodes.add(item.Target);
                }
                // Add drug node if not already added
                if (item.Drug_Name && !addedNodes.has(item.Drug_Name)) {
                    nodes.push({ data: { id: item.Drug_Name, label: item.Drug_Name, type: 'drug' } });
                    addedNodes.add(item.Drug_Name);
                }
                // Add edge for the interaction
                 if (item.Target && item.Drug_Name && item.interaction_id) { // Only add edge if all necessary data is present
                    edges.push({ data: { id: item.interaction_id, source: item.Target, target: item.Drug_Name, score: item.Score || 0 } }); // Use score or 0 if null
                 }
            });

             // Add drugType and imageUrl to drug nodes data
            nodes.forEach(node => {
                if (node.data.type === 'drug' && node.data.id) { // Ensure node id exists
                    const drugInfo = drugDataMap.get(node.data.id);
                    if (drugInfo) {
                         node.data.drugType = drugInfo.drugType || 'unknown'; // Default to 'unknown'
                         node.data.imageUrl = drugInfo.imageUrl;
                    } else {
                         node.data.drugType = 'unknown'; // Default if drug info is missing
                         node.data.imageUrl = null;
                    }
                }
            });

             // Initialize Cytoscape
            cy = cytoscape({
                container: document.getElementById('cy'),
                elements: { nodes, edges }, // Use only elements from the current page
                style: [
                    { selector: 'node[type="target"]', style: {
                        'background-color': '#6c757d',
                        'label': 'data(label)',
                        'shape': 'ellipse',
                        'text-valign': 'bottom',
                        'text-halign': 'center',
                        'font-size': '10px',
                        'color': '#333',
                        'text-wrap': 'wrap',
                        'text-max-width': '80px'
                    } },
                    { selector: 'node[type="drug"]', style: {
                        'background-color': ele => drugTypeColors[ele.data('drugType')] || drugTypeColors['unknown'],
                        'label': 'data(label)',
                        'shape': 'rectangle',
                        'text-valign': 'bottom',
                        'text-halign': 'center',
                         'font-size': '10px',
                         'color': '#333',
                         'text-wrap': 'wrap',
                         'text-max-width': '80px'
                    } },
                    { selector: 'edge', style: {
                         'width': ele => {
                            const score = ele.data('score');
                            return Math.max(1, (score || 0) * 4 + 1); // Map score [0,1] to width [1,5]
                         },
                        'line-color': '#ccc',
                        'curve-style': 'bezier',
                         'opacity': 0.6
                    } },
                    // Styles for highlighting
                    { selector: '.highlighted', style: {
                        'background-color': '#ffc107',
                        'line-color': '#ffc107',
                        'transition-property': 'background-color, line-color, opacity',
                        'transition-duration': '0.3s',
                        'opacity': 1
                    } },
                     // Hide elements not selected by checkboxes (initial state: show all elements on this page)
                    { selector: ':hidden', style: { 'display': 'none' } }
                ],
                layout: {
                    name: 'cose',
                    animate: true,
                    animationDuration: 500,
                    padding: 10,
                    gravity: 1,
                    edgeElasticity: 0.45,
                    nodeRepulsion: 20000,
                    idealEdgeLength: 100
                 }
            });

            // Image pop-up on hover (using tippy.js) - Ensure cleanup logic is robust
            const popup = document.getElementById('drug-image-popup');
            let tippyInstance = null; // To keep track of the current tippy instance

            cy.on('mouseover', 'node[type="drug"]', function(evt) {
                const node = evt.target;
                const imageUrl = node.data('imageUrl');
                const drugName = node.data('label');

                // Destroy any existing tippy instance before creating a new one
                if (tippyInstance) {
                     tippyInstance.destroy();
                     tippyInstance = null;
                 }

                if (imageUrl) {
                    // Create a dummy element at the node's position for tippy reference
                    const dummy = document.createElement('div');
                    dummy.style.position = 'absolute';
                    // Use bounding box center for better positioning reference
                    const nodePos = node.renderedPosition();
                    dummy.style.left = `${nodePos.x}px`;
                    dummy.style.top = `${nodePos.y}px`;
                    document.body.appendChild(dummy);

                    tippyInstance = tippy(dummy, {
                        content: `<img src="${imageUrl}" alt="${drugName || 'Drug'} Image" style="max-width:150px; max-height:150px;">`,
                        trigger: 'manual', // Show manually
                        placement: 'right', // Position relative to the dummy element
                        arrow: true,
                        allowHTML: true,
                        interactive: true, // Allow interaction with the popup content (e.g., if it had links)
                        appendTo: document.body, // Append tippy to body to avoid clipping issues within Cytoscape container
                        onHidden: (instance) => { // Clean up dummy element and instance after hiding
                            instance.reference.remove();
                            // Do NOT set tippyInstance = null here, as hide might be triggered by mouseout,
                            // and a new mouseover might happen before onHidden completes, leading to race conditions.
                            // Instead, nullify it in the mouseout handler.
                        }
                    });
                     tippyInstance.show(); // Show the tippy
                }
            });

            cy.on('mouseout', 'node[type="drug"]', function(evt) {
                 // Hide the tippy instance after a small delay
                 setTimeout(() => {
                     if (tippyInstance) {
                         // Check if the mouse is still over the node or the tippy popup itself (complex check)
                         // For simplicity, we'll hide it after a delay. A more robust solution involves
                         // tracking mouse position relative to the tippy element.
                         tippyInstance.hide();
                         tippyInstance = null; // Nullify after initiating hide
                     }
                 }, 50); // Short delay
            });

            // Cleanup tippy on node position change or pan/zoom to prevent orphaned popups
             // This might be triggered frequently during layout animation
             cy.on('position', 'node[type="drug"]', function(evt) {
                // Update tippy position if it exists
                 if (tippyInstance) {
                     tippyInstance.popperInstance.update();
                 }
            });
             cy.on('zoom pan drag', function(evt) { // Hide tippy during interaction
                 if (tippyInstance) {
                     tippyInstance.hide();
                     tippyInstance = null; // Ensure it's nulled
                 }
             });

             // Initial filter based on table checkboxes (which should all be unchecked initially)
             // or if search was applied before network was created.
             filterNetworkFromCheckboxes();

        }

        // Helper function to recreate network elements from a data subset (not strictly needed anymore as createNetworkGraph uses dti_data)
        // function createNetworkElements(dataSubset) { /* ... logic ... */ }


        function highlightNetworkEdge(targetId, drugName) {
            if (!cy) {
                console.warn("Cytoscape instance not ready.");
                return;
            }

            // Remove previous highlights
            cy.elements().removeClass('highlighted');

            // Find the specific nodes and edge *within the current graph*
            const targetNode = cy.getElementById(targetId);
            const drugNode = cy.getElementById(drugName);
            // Find the edge(s) connecting these specific nodes
             const interactionEdge = cy.edges().filter(edge =>
                 (edge.source().id() === targetId && edge.target().id() === drugName) ||
                 (edge.source().id() === drugName && edge.target().id() === targetId)
             );

            // Apply highlight if nodes and edge(s) are found *in the current graph*
            if (targetNode.length > 0 && drugNode.length > 0 && interactionEdge.length > 0) {
                 targetNode.addClass('highlighted');
                 drugNode.addClass('highlighted');
                 interactionEdge.addClass('highlighted');

                // Center view on the highlighted elements (optional)
                 const elesToFit = targetNode.union(drugNode).union(interactionEdge);
                 if (elesToFit.length > 0) {
                     cy.animate({
                         fit: { eles: elesToFit, padding: 50 },
                         duration: 500
                     });
                 }


                // Remove highlight after a delay
                 setTimeout(() => {
                     targetNode.removeClass('highlighted');
                     drugNode.removeClass('highlighted');
                     interactionEdge.removeClass('highlighted');
                 }, 3000); // Highlight duration
            } else {
                 console.warn(`Nodes/Edge not found in current page network for Target: ${targetId}, Drug: ${drugName}.`);
                 // This means the selected interaction is not on the currently displayed page.
            }
        }

        // UTILITY FUNCTIONS

        function downloadData() {
             // NOTE: This function currently only downloads the data from the *current page*
             if (!dti_data || dti_data.length === 0) {
                 alert("No data available on the current page to download.");
                 return;
             }
             alert("Note: This download contains only the data from the current page."); // Inform the user

            // Define the headers based on the keys in your dti_data objects
            const headers = [
                'interaction_id', 'Drugbank.ID', 'Target', 'Score',
                'Drug_Name', 'Drug_Type', 'Protein.names', 'Pathway',
                'Gene.Ontology.(biological.process)', 'Gene.Ontology.(molecular.function)',
                'Gene.Ontology.(cellular.component)', 'DeepPK_toxicity_Safe', 'drug_image_url' // Included new column
            ];

            const csvContent = [
                headers.join(','), // CSV header row
                // Map each data row to a CSV row, handling potential commas/quotes in fields
                dti_data.map(row => headers.map(fieldName => {
                     let value = row[fieldName];
                     if (value === null || value === undefined) {
                         value = ''; // Replace null/undefined with empty string
                     } else if (typeof value === 'string') {
                         // Escape double quotes and wrap the field in double quotes
                         value = value.replace(/"/g, '""');
                         value = `"${value}"`;
                     } else {
                         // Convert other types to string
                         value = String(value);
                     }
                     return value;
                 }).join(',')
                ).join('\n') // Join rows with newline characters
            ].join('\n'); // Join header and rows

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `escherichia_coli_dti_data_page_${currentPage}.csv`; // Name includes page number
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url); // Clean up the URL object
        }
    </script>
</body>
<?php include 'footer.php'; ?>
</html>