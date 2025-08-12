<?php include 'conn2.php'; ?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Transcriptomics Antibiotics Exposure</title>
    
    <!-- STYLING ADDED: Bootstrap and Bootstrap Icons for a modern look -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- DataTables CSS (already present) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <!-- STYLING ADDED: CSS styles copied from drug_targets.php for consistency -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --dark-color: #212529;
            --light-gray: #f8f9fa;
        }

        body { /* Changed from .body to body for global application */
            font-family: 'Arial', sans-serif;
            background-color: var(--light-gray);
        }

        .header h1 {
            font-weight: 300;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            padding: 2rem; /* Increased padding */
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            margin-bottom: 2rem;
        }

        /* Keep existing DataTables customization */
        .dataTables_paginate .next {
            display: none !important;
        }
    </style>
</head>
<body>

<!-- STRUCTURE ADDED: A consistent header like in drug_targets.php -->
<header class="header py-3 bg-white shadow-sm mb-4">
    <div class="container text-center">
        <h1 class="display-6">Transcriptomics Antibiotics Exposure</h1>
    </div>
</header>

<!-- STRUCTURE ADDED: Main content wrapper -->
<div class="container-fluid px-4">
    <section>
        <!-- STRUCTURE ADDED: The key table container for the white box effect -->
        <div class="table-container">

            <!-- FORM RESTYLED: Using Bootstrap's grid system and form controls -->
            <form method="GET" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <label for="antibiotic" class="form-label">Antibiotic:</label>
                        <select id="antibiotic" name="antibiotic" class="form-select">
                            <option value="">-- All --</option>
                            <?php
                            $antibiotics =["Cefotaxime", "Cefixime", "Cefoperazone", "Cefsulodin", "Ceftazidime", "Ceftibuten", "Ceftriaxone", "Ciprofloxacin", "Doripenem", "Ertapenem", "Erythromycin", "Imipenem", "Meropenem", "Methicillin", "Oxacillin", "Spiromycin"];
                            foreach ($antibiotics as $ab) {
                                $selected = (isset($_GET['antibiotic']) && $_GET['antibiotic'] == $ab) ? 'selected' : '';
                                echo "<option value=\"$ab\" $selected>$ab</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label for="organism" class="form-label">Organism:</label>
                        <select id="organism" name="organism" class="form-select">
                            <option value="">-- All --</option>
                            <?php
                            $pathogens = ['Pseudomonas aeruginosa', 'Escherichia coli', 'Salmonella enterica', 'Streptococcus pneumoniae', 'Klebsiella pneumoniae', 'Acinetobacter baumannii', 'Neisseria gonorrhoeae', 'Staphylococcus aureus'];
                            foreach ($pathogens as $p) {
                                $selected = (isset($_GET['organism']) && $_GET['organism'] == $p) ? 'selected' : '';
                                echo "<option value=\"$p\" $selected>$p</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <input type="submit" value="Filter" class="btn btn-primary w-100">
                    </div>
                </div>
            </form>

            <?php
            // --- PHP logic remains the same ---
            $antibiotic = isset($_GET['antibiotic']) ? $conn->real_escape_string($_GET['antibiotic']) : '';
            $organism = isset($_GET['organism']) ? $conn->real_escape_string($_GET['organism']) : '';

            $filter_condition = "WHERE 1=1";
            if (!empty($antibiotic)) {
                $filter_condition .= " AND Antibiotic_Name = '$antibiotic'";
            }
            if (!empty($organism)) {
                $filter_condition .= " AND Organism = '$organism'";
            }

            $table_name = "RNA_seq";
            $sql = "SELECT * FROM $table_name $filter_condition ORDER BY Antibiotic_Name";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):
            ?>
            
            <!-- TABLE RESTYLED: Added Bootstrap classes for styling -->
            <table id="table1" class="table table-striped table-hover" style="width:100%">
                <thead class="table-dark"> <!-- Added dark header class -->
                <tr>
                    <th>Organism</th>
                    <th>Project Title</th>
                    <th>Project Database</th>
                    <th>Project Accession</th>
                    <th>Antibiotic Name</th>
                    <th>Release Date</th>
                    <th>Samples</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $organism_links = [
                    'Acinetobacter baumannii'     => 'ab1.php',
                    'Escherichia coli'            => 'Ecoli.php',
                    'Salmonella enterica'         => 'se.php',
                    'Streptococcus pneumoniae'    => 'sp.php',
                    'Klebsiella pneumoniae'       => 'kp.php',
                    'Neisseria gonorrhoeae'       => 'ng.php',
                    'Staphylococcus aureus'       => 'sa.php',
                    'Pseudomonas aeruginosa'      => 'pa.php'
                ];

                while ($row = $result->fetch_assoc()):
                    $org = htmlspecialchars($row['Organism']);
                    $org_link = $organism_links[$org] ?? '#';
                    
                    $accession = htmlspecialchars($row['Project_Accession']);
                    $accession_num = preg_replace('/[^0-9]/', '', $accession);
                    $bioproject_link = "https://www.ncbi.nlm.nih.gov/bioproject/?term=$accession";
                    $biosample_link = "https://www.ncbi.nlm.nih.gov/biosample?Db=biosample&DbFrom=bioproject&Cmd=Link&LinkName=bioproject_biosample&LinkReadableName=BioSample&ordinalpos=1&IdsFromResult=$accession_num";
                ?>
                    <tr>
                        <td><a href="<?= $org_link ?>" target="_blank"><em><?= $org ?></em></a></td>
                        <td><?= htmlspecialchars($row['Project_Title']) ?></td>
                        <td><?= htmlspecialchars($row['Project_Database']) ?></td>
                        <td><a href="<?= $bioproject_link ?>" target="_blank"><?= $accession ?></a></td>
                        <td><?= htmlspecialchars($row['Antibiotic_Name']) ?></td>
                        <td><?= htmlspecialchars($row['Release_Date']) ?></td>
                        <td><a href="<?= $biosample_link ?>" target="_blank"><?= htmlspecialchars($row['Samples']) ?></a></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>

            <?php else: ?>
                <!-- "NO RESULTS" RESTYLED: Using a Bootstrap alert for better visibility -->
                <div class="alert alert-warning text-center mt-4" role="alert">
                    No combination found matching your filters.
                </div>
            <?php endif; ?>
        </div> <!-- end of .table-container -->
    </section>
</div> <!-- end of .container-fluid -->


<script>
    $(document).ready(function () {
        $('#table1').DataTable({
            language: {
                search: "Browse:" // This customization is kept
            }
        });
    });
</script>

</body>
</html>

<?php include 'footer.php'; ?>