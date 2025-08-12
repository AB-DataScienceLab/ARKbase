<?php 
include 'header.php';
include 'conn3.php'; 

// Change this to view another organism
$organism = 'Neisseria gonorrhoeae';

// Fetch reference genome data
$sql = "SELECT * FROM reference_genome WHERE Organism_Name LIKE '%$organism%' LIMIT 1";
$result = $conn->query($sql);

// Fetch pan genome data
$pan_sql = "SELECT * FROM pan_genome_distribution WHERE Organism_Name LIKE '%$organism%' LIMIT 1";
$pan_result = $conn->query($pan_sql);
$pan_data = ['Core_genes' => 0, 'Soft_core_genes' => 0, 'Shell_genes' => 0, 'Cloud_genes' => 0];

if ($pan_result->num_rows > 0) {
    $pan_row = $pan_result->fetch_assoc();
    $pan_data = [
        'Core_genes' => (int)$pan_row['Core_genes'],
        'Soft_core_genes' => (int)$pan_row['Soft_core_genes'],
        'Shell_genes' => (int)$pan_row['Shell_genes'],
        'Cloud_genes' => (int)$pan_row['Cloud_genes']
    ];
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $organism; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style2.css" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
<div class="page-content-wrapper container">
    <div class="header">
        <h1><em><?php echo $organism; ?></em></h1>
    </div>

    <div class="tabs">
        <button class="tab-button active" onclick="showTab('overview')">Overview</button>
        <a class="tab-button" href="genome_Neisseria_gonorrhoeae.php">Genomes</a>
<!--        <button class="tab-button" onclick="showTab('genes-proteins')">Genes/Proteins</button>-->
       <a class="tab-button" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=n_gonorrhoeae">Structural Annotation</a>
  
        <a class="tab-button" href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Neisseria_gonorrhoeae&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=">PPI</a>
      <a class="tab-button" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=n_gonorrhoeae">Drug Targets</a>
        
      
    </div>

    <div class="content">
        <div id="overview" class="tab-content active">
            <div class="dashboard-grid">
                <div class="left-section">
                    <!-- Pathogen Overview -->
                    <div class="card-row">
                        <div class="card tabular-style">
                            <h3>Pathogen Overview</h3>
                            <div class="card-content">
                                <table class="tabular-data">
                                    <tr><td>Organism Name:</td><td><em><?php echo $row['Organism_Name']; ?></em></td></tr>
                                    <tr><td>Gram Stain:</td><td><?php echo $row['Gram_strain']; ?></td></tr>
                                    <tr><td>Taxon ID:</td><td><?php echo $row['Taxon_ID']; ?></td></tr>
                                    <tr><td>Country of Isolation:</td><td><?php echo $row['Country_of_isolation']; ?></td></tr>
                                    <tr><td>Isolation Source:</td><td><?php echo $row['Isolation_Source']; ?></td></tr>
                                </table>
                            </div>
                        </div>

                        <!-- Genome Summary -->
                        <div class="card tabular-style">
                            <h3>Genome Summary</h3>
                            <div class="card-content">
                                <table class="tabular-data">
                                    <tr><td>Assembly Accession:</td><td><?php echo $row['Assembly_Accession']; ?></td></tr>
                                    <tr><td>Genome Size:</td><td><?php echo $row['Genome_size']; ?></td></tr>
                                    <tr><td>GC Content:</td><td><?php echo ($row['GC_Content'] !== null ? $row['GC_Content'] . '%' : 'NA'); ?></td></tr>
                                    <tr><td>Total Genes:</td><td><?php echo number_format($row['Total_Genes']); ?></td></tr>
                                    
                                    <tr><td>Protein Coding Genes:</td><td><?php echo number_format($row['Protein_Coding_Genes']); ?></td></tr>
                                    <tr><td>tRNA:</td><td><?php echo $row['tRNA']; ?></td></tr>
                                    <tr><td>rRNA:</td><td><?php echo $row['rRNA']; ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Phenotype Classification Chart (iframe) -->
                    <div class="card-row" style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <div class="card" style="flex: 1;">
                            <h3>COG Classification</h3>
                            <div class="chart-container" style="height: 400px;">
                                <iframe src="/anshu/arkbase/data/COG_plots_all_15/COG_distribution_horizontal_final_NG.html" 
                                        width="100%" height="100%" frameborder="0" style="border: none;"></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Drug Resistance Profile Image -->
                    <div class="card full-width-card">
                        <h3>Drug Resistance Profile</h3>
                        <div class="image-container">
                            <img src="images/Drug_resistance_profile/NG.png" alt="Genome Classification" style="max-width: 100%; height:auto;">
                        </div>
                    </div>

                    <!-- Circos Plot Placeholder -->
                  
                </div>

                <!-- Right Section -->
                <div class="right-section">
                    <!-- Curated Genomes Info -->
                   <div class="card tabular-style">
                        <h3>ARGs and mechanism</h3>
                       <div class="circos-container">
        <img src="images/Ref_RGI_graphs/N_gonorrhoeae_rgi.jpg" 
             alt="Circos Plot" 
             class="circos-zoomable" 
             id="circosImage">
    </div>
                    </div>
                    

                    <!-- Pan Genome Donut Chart -->
                    <!-- Pan Genome Donut Chart -->
<div class="card">
    <h3>Pan Genome Distribution</h3>
    <div class="chart-container" style="width: 350px; height: 350px; margin: auto;">
        <canvas id="panGenomeChart" width="400" height="400"></canvas>
    </div>
</div>
<div class="card full-width-card">
    <h3>Genomic Representation</h3>

    <div class="circos-container">
        <img src="images/Circos_static/N_gonorrhoeae_circos_static.png" 
             alt="Circos Plot" 
             class="circos-zoomable" 
             id="circosImage">
    </div>

    
</div>



                </div>
            </div> <!-- dashboard-grid -->
        </div> <!-- overview -->
    </div> <!-- content -->
</div> <!-- container -->

<!-- Donut Chart Script -->
<script>
const ctx = document.getElementById('panGenomeChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['Core Genes', 'Soft Core Genes', 'Shell Genes', 'Cloud Genes'],
        datasets: [{
            data: [
                <?php echo $pan_data['Core_genes']; ?>,
                <?php echo $pan_data['Soft_core_genes']; ?>,
                <?php echo $pan_data['Shell_genes']; ?>,
                <?php echo $pan_data['Cloud_genes']; ?>
            ],
            backgroundColor: [
                '#3e95cd', '#8e5ea2', '#3cba9f', '#e8c3b9'
            ],
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
</body>
<?php 
} else {
    echo "<p>No data found for <em>$organism</em></p>";
}
$conn->close();
include 'footer.php';
?>
</html>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const img = document.querySelector('.circos-zoomable');
    let zoomed = false;

    img.addEventListener('click', (event) => {
        zoomed = !zoomed;

        // Get the position of the click relative to the image
        const rect = img.getBoundingClientRect();
        const offsetX = event.clientX - rect.left;
        const offsetY = event.clientY - rect.top;

        // Convert to percentages for transform-origin
        const originX = (offsetX / rect.width) * 100;
        const originY = (offsetY / rect.height) * 100;

        // Apply transform origin based on click position
        img.style.transformOrigin = `${originX}% ${originY}%`;

        // Toggle zoom
        img.style.transform = zoomed ? 'scale(2)' : 'scale(1)';
        img.style.cursor = zoomed ? 'zoom-out' : 'zoom-in';
    });
});
</script>


</script>
