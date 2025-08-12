<?php
include 'header.php';
include 'conn.php'; // Make sure this path is correct

// Get the pathogen from the URL, with a default fallback
$pathogen = isset($_GET['pathogen']) ? htmlspecialchars($_GET['pathogen']) : 'Default Pathogen';

// Fetch unique antibiotics and phenotypes for the dropdowns
$antibiotics = [];
$phenotypes = [];

if ($pathogen !== 'Default Pathogen') {
    $antibiotic_query = $conn->prepare("SELECT DISTINCT Antibiotic FROM Genome_section WHERE Pathogen = ? ORDER BY Antibiotic");
    $antibiotic_query->bind_param("s", $pathogen);
    $antibiotic_query->execute();
    $antibiotic_result = $antibiotic_query->get_result();
    while ($row = $antibiotic_result->fetch_assoc()) {
        $antibiotics[] = $row['Antibiotic'];
    }
    $antibiotic_query->close();

    $phenotype_query = $conn->prepare("SELECT DISTINCT Phenotype FROM Genome_section WHERE Pathogen = ? ORDER BY Phenotype");
    $phenotype_query->bind_param("s", $pathogen);
    $phenotype_query->execute();
    $phenotype_result = $phenotype_query->get_result();
    while ($row = $phenotype_result->fetch_assoc()) {
        $phenotypes[] = $row['Phenotype'];
    }
    $phenotype_query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curated Genomes for <?php echo $pathogen; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        @media (min-width: 1200px) { .container-wide { width: 90%; max-width: none; } }
        .filter-section { background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .nav-tabs .nav-link { color: #495057; }
        .nav-tabs .nav-link.active { color: #000; background-color: #fff; border-color: #dee2e6 #dee2e6 #fff; }
        .table-responsive { margin-top: 10px; }
        .loader { border: 5px solid #f3f3f3; border-radius: 50%; border-top: 5px solid #3498db; width: 50px; height: 50px; animation: spin 1s linear infinite; margin: 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        #results-info { margin-bottom: 15px; font-size: 1.1em; color: #6c757d; }
    </style>
</head>
<body>

<div class="container container-wide mt-4">
    <h1 class="mb-4">Curated Genome Data for <em><?php echo $pathogen; ?></em></h1>

    <div class="filter-section">
        <form id="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="antibiotic-select" class="form-label">Filter by Antibiotic:</label>
                    <select id="antibiotic-select" name="antibiotic" class="form-select">
                        <option value="">All Antibiotics</option>
                        <?php foreach ($antibiotics as $antibiotic): ?>
                            <option value="<?php echo htmlspecialchars($antibiotic); ?>"><?php echo htmlspecialchars($antibiotic); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="phenotype-select" class="form-label">Filter by Phenotype:</label>
                    <select id="phenotype-select" name="phenotype" class="form-select">
                        <option value="">All Phenotypes</option>
                        <?php foreach ($phenotypes as $phenotype): ?>
                            <option value="<?php echo htmlspecialchars($phenotype); ?>"><?php echo htmlspecialchars($phenotype); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
                <div class="col-md-auto">
                    <a id="download-btn" class="btn btn-success" href="#" style="display: none;">
                        <i class="bi bi-download"></i> Download
                    </a>
            </div>
        </form>
    </div>

    <div>
        <ul class="nav nav-tabs" id="table-tabs" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" data-table="Pan_Genome_Curated">All</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-table="RGI">RGI</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-table="AMRFinderPlus">AMRFinderPlus</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-table="MEGARes">MEGARes</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" data-table="ResFinder">ResFinder</button></li>
            
        </ul>
        
        <div id="results-info" class="mt-3"></div>

        <div class="table-responsive" id="data-table-container">
            <div class="loader"></div>
        </div>
        <div id="pagination-controls" class="mt-4"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    const pathogen = '<?php echo $pathogen; ?>';
    let activeTable = 'Pan_Genome_Curated';
    let currentPage = 1;

    function fetchData() {
        const antibiotic = $('#antibiotic-select').val();
        const phenotype = $('#phenotype-select').val();
        
        $('#download-btn').hide();
        $('#data-table-container').html('<div class="loader"></div>');
        $('#pagination-controls').html('');
        $('#results-info').html('');

        $.ajax({
            url: 'fetch_curated_data.php',
            type: 'POST',
            dataType: 'json',
            data: {
                pathogen: pathogen,
                table: activeTable,
                antibiotic: antibiotic,
                phenotype: phenotype,
                page: currentPage
            },
            success: function(response) {
                if(response.error) {
                    $('#data-table-container').html(`<p class="text-danger">Error: ${response.error}</p>`);
                } else {
                    const startRecord = (currentPage - 1) * 25 + 1;
                    const endRecord = Math.min(startRecord + 24, response.hits);
                    if (response.hits > 0) {
                        $('#results-info').html(`Showing ${startRecord} - ${endRecord} of <strong>${response.hits}</strong> results.`);
                        
                        let downloadUrl = new URL('download_csv.php', window.location.href);
                        downloadUrl.searchParams.set('pathogen', pathogen);
                        downloadUrl.searchParams.set('table', activeTable);
                        downloadUrl.searchParams.set('antibiotic', antibiotic);
                        downloadUrl.searchParams.set('phenotype', phenotype);
                        
                        $('#download-btn').attr('href', downloadUrl.toString()).show();
                    } else {
                         $('#results-info').html(`<strong>0</strong> results found.`);
                         $('#download-btn').hide();
                    }
                    
                    $('#data-table-container').html(response.table);
                    $('#pagination-controls').html(response.pagination);
                }
            },
            error: function() {
                $('#data-table-container').html('<p class="text-danger">An error occurred while fetching the data.</p>');
            }
        });
    }

    $('#table-tabs button').on('click', function() {
        $('#table-tabs button').removeClass('active');
        $(this).addClass('active');
        activeTable = $(this).data('table');
        currentPage = 1;
        fetchData();
    });

    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        fetchData();
    });

    $('#pagination-controls').on('click', '.page-link', function(e){
        e.preventDefault();
        const newPage = $(this).data('page');
        if (newPage && newPage !== currentPage) {
            currentPage = newPage;
            fetchData();
        }
    });

    fetchData();
});
</script>

</body>
</html>
<?php 'footer.php'?>