<?php
// ========== FORM PROCESSING LOGIC (MUST BE AT THE TOP) ==========
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure the vendor autoload exists for PHPMailer
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$c_name = $c_email = $c_type = $c_description = ""; // For Contribution Form
$errors = [];
$contribution_success_message = '';

// Check if the contribution form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contribution_form_submit'])) {

    // ========== HANDLE CONTRIBUTION FORM SUBMISSION ==========
    // Sanitize contribution form data
    $c_name = htmlspecialchars(strip_tags(trim($_POST['c_name'] ?? '')));
    $c_email = htmlspecialchars(strip_tags(trim($_POST['c_email'] ?? '')));
    $c_type = htmlspecialchars(strip_tags(trim($_POST['c_type'] ?? '')));
    $c_description = htmlspecialchars(strip_tags(trim($_POST['c_description'] ?? '')));

    // Validation for Contribution Form
    if (empty($c_name)) $errors[] = "Your Name is required for the contribution.";
    if (empty($c_email)) {
        $errors[] = "Email is required for the contribution.";
    } elseif (!filter_var($c_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required for the contribution.";
    }
    if (empty($c_type)) $errors[] = "Type of Contribution is required.";
    
    // File upload validation
    $file_uploaded = false;
    if (isset($_FILES['c_file']) && $_FILES['c_file']['error'] == UPLOAD_ERR_OK) {
        $file_uploaded = true;
    }

    if (empty($errors)) {
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $mail = new PHPMailer(true);
            try {
                // SMTP Configuration
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'arkbase2025@gmail.com'; // Your email
                $mail->Password   = 'xluh hxbn luoi gmaz';   // Your app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Email Details
                $mail->setFrom('arkbase2025@gmail.com', 'ARKbase Contributions');
                $mail->addAddress('arkbase2025@gmail.com', 'ARKbase Curation Team');
                $mail->addReplyTo($c_email, $c_name);
                
                // Attach file if uploaded
                if ($file_uploaded) {
                    $mail->addAttachment($_FILES['c_file']['tmp_name'], $_FILES['c_file']['name']);
                }

                // Email Content
                $mail->isHTML(true);
                $mail->Subject = "New Community Contribution from {$c_name}";
                $mail->Body    = "<p>A new community contribution has been submitted to ARKbase.</p><hr>" .
                                 "<p><strong>Contributor Name:</strong> {$c_name}</p>" .
                                 "<p><strong>Contributor Email:</strong> {$c_email}</p>" .
                                 "<p><strong>Contribution Type:</strong> {$c_type}</p>" .
                                 "<p><strong>Description/Notes:</strong></p><p>" . nl2br($c_description) . "</p><hr>" .
                                 "<p><strong>File Attached:</strong> " . ($file_uploaded ? 'Yes' : 'No') . "</p>";

                $mail->send();
                $_SESSION['contribution_success_message'] = "Thank you for your contribution to ARKbase! Your submission has been received and will be reviewed by our curation team.";
            } catch (Exception $e) {
                $errors[] = "Contribution could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
             $errors[] = "Email functionality is not configured correctly.";
        }
    }
    $_SESSION['errors_contribution'] = $errors;
    $_SESSION['form_data_contribution'] = ['c_name' => $c_name, 'c_email' => $c_email, 'c_type' => $c_type, 'c_description' => $c_description];
    header("Location: " . basename(__FILE__) . "#contribution-form");
    exit();
}

// ========== RETRIEVE SESSION DATA FOR DISPLAY ==========
if (isset($_SESSION['contribution_success_message'])) {
    $contribution_success_message = $_SESSION['contribution_success_message'];
    unset($_SESSION['contribution_success_message']);
}
$contribution_errors_exist = isset($_SESSION['errors_contribution']); 
if ($contribution_errors_exist) {
    $errors = $_SESSION['errors_contribution'];
    unset($_SESSION['errors_contribution']);
}
if (isset($_SESSION['form_data_contribution'])) {
    $form_data_c = $_SESSION['form_data_contribution'];
    $c_name = $form_data_c['c_name'];
    $c_email = $form_data_c['c_email'];
    $c_type = $form_data_c['c_type'];
    $c_description = $form_data_c['c_description'];
    unset($_SESSION['form_data_contribution']);
}
?>
<!DOCTYPE HTML>
<?php include 'header.php'; ?>
<html>
<head>
    <meta charset="UTF-8">
    <title>Contact & Team - ARKbase</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to top right, #f8f9fa, #ffffff);
        }

        .page-section {
            padding: 40px 15px;
        }

        /* ========== STYLES FOR LEFT COLUMN (CONTRIBUTIONS) ========== */
        .contribution-section h1, .contribution-section h2 {
            font-weight: 600;
            color: #0c3c78;
        }
        .contribution-section .form-container {
             background-color: #fff;
             padding: 30px;
             border-radius: 8px;
             box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        .contribution-section label {
            font-weight: bold;
            color: #222;
        }
        .contribution-section input, .contribution-section textarea, .contribution-section select {
            margin-top: 8px;
            margin-bottom: 15px;
            font-size: 15px;
        }
        .contribution-section button {
            background-color: #0c3c78;
            color: white;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        .contribution-section button:hover {
            background-color: #1250a2;
        }
        .contribution-section button:disabled {
            background-color: #5a82b5;
            cursor: not-allowed;
        }
        .contribution-section small {
            color: #777;
        }
        
        /* ========== STYLES FOR RIGHT COLUMN (TEAM) ========== */
        .team-section {
            padding: 40px 15px;
        }
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            color: #10428d;
            font-weight: bold;
        }
        .contact-card {
            text-align: center;
            padding: 25px 15px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .contact-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        .contact-card img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #e9ecef;
            margin-bottom: 20px;
        }
        .contact-card h3 {
            font-size: 1.1rem;
            color: #343a40;
            margin-bottom: 5px;
        }
        .contact-card h3 a {
            color: inherit;
            text-decoration: none;
        }
         .contact-card h3 a:hover {
            color: #10428d;
        }
        .contact-card .title {
            font-size: 0.9rem;
            color: #10428d;
            font-weight: 500;
            margin-bottom: 5px;
            min-height: 40px; /* Helps align titles */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .contact-card .affiliation {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }
        
        /* ========== STYLES FOR MAP SECTION ========== */
        .map-container {
            padding: 60px 15px;
            text-align: center;
            background-color: #e9ecef;
        }
        .map-container iframe {
            max-width: 900px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <!-- =========== MAIN TWO-COLUMN LAYOUT ROW =========== -->
    <div class="row">

        <!-- =========== LEFT COLUMN: COMMUNITY CONTRIBUTIONS =========== -->
        <div id="contribution-form" class="col-lg-6 page-section contribution-section">
            <div class="container">
                <div class="contribution-info mb-5">
                    <h1>Community Contributions</h1>
                    <p>We invite researchers, clinicians, and domain experts to contribute to <strong>ARKbase</strong>, an open-access Antimicrobial Resistance Knowledgebase. Your contributions are vital in helping the global community accelerate discoveries.</p>
                    <h2 class="mt-4">What You Can Contribute</h2>
                    <ul>
                        <li>Annotated genomes, pan-genomes, operon structures</li>
                        <li>Host-pathogen interaction (HPI) or Protein-protein interaction (PPI) data</li>
                        <li>Small molecules, drug-target interaction (DTI) data, drug targets, or co-targets</li>
                        <li>Gene expression datasets, ARGs, virulence factors, or BGCs</li>
                        <li>Machine learning (ML) models for AMR prediction or prioritization</li>
                        <li>Relevant literature (PubMed ID or DOI)</li>
                    </ul>
                </div>

                <div class="form-container">
                    <h2>Submit Your Contribution</h2>
                    
                    <?php if (!empty($contribution_success_message)): ?>
                        <div class="alert alert-success" role="alert"><?php echo $contribution_success_message; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($errors) && $contribution_errors_exist): ?>
                         <div class="alert alert-danger" role="alert">
                            <ul class="mb-0" style="padding-left: 1.2rem;"><?php foreach ($errors as $error) echo "<li>$error</li>"; ?></ul>
                        </div>
                    <?php endif; ?>

                    <form id="contributionForm" method="post" action="<?= basename(__FILE__) ?>#contribution-form" enctype="multipart/form-data" novalidate>
                        <div class="form-group">
                            <label for="c_name">Your Name</label>
                            <input type="text" class="form-control" id="c_name" name="c_name" value="<?= htmlspecialchars($c_name) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="c_email">Email</label>
                            <input type="email" class="form-control" id="c_email" name="c_email" value="<?= htmlspecialchars($c_email) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="c_type">Type of Contribution</label>
                            <select id="c_type" name="c_type" class="form-control" required>
                                <option value="">-- Please select --</option>
                                <option value="genome" <?= $c_type == 'genome' ? 'selected' : '' ?>>Genome</option>
                                <option value="pangenome" <?= $c_type == 'pangenome' ? 'selected' : '' ?>>Pangenome</option>
                                <option value="gene_annotation" <?= $c_type == 'gene_annotation' ? 'selected' : '' ?>>Gene Annotation</option>
                                <option value="hpi_module" <?= $c_type == 'hpi_module' ? 'selected' : '' ?>>HPI Module</option>
                                <option value="ppi_module" <?= $c_type == 'ppi_module' ? 'selected' : '' ?>>PPI Module</option>
                                <option value="small_molecules" <?= $c_type == 'small_molecules' ? 'selected' : '' ?>>Small Molecules</option>
                                <option value="dti" <?= $c_type == 'dti' ? 'selected' : '' ?>>DTI (Drug-Target Interactions)</option>
                                <option value="drug_targets" <?= $c_type == 'drug_targets' ? 'selected' : '' ?>>Drug Targets</option>
                                <option value="expression_datasets" <?= $c_type == 'expression_datasets' ? 'selected' : '' ?>>Expression Datasets</option>
                                <option value="ml_models" <?= $c_type == 'ml_models' ? 'selected' : '' ?>>ML Models</option>
                                <option value="args" <?= $c_type == 'args' ? 'selected' : '' ?>>ARGs</option>
                                <option value="virulence_factors" <?= $c_type == 'virulence_factors' ? 'selected' : '' ?>>Virulence Factors</option>
                                <option value="operons" <?= $c_type == 'operons' ? 'selected' : '' ?>>Operons</option>
                                <option value="bgcs" <?= $c_type == 'bgcs' ? 'selected' : '' ?>>BGCs</option>
                                <option value="literature" <?= $c_type == 'literature' ? 'selected' : '' ?>>Literature Reference</option>
                                <option value="other" <?= $c_type == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="c_file">Upload File (CSV, TSV, or TXT)</label>
                            <input type="file" class="form-control-file" id="c_file" name="c_file" accept=".csv,.tsv,.txt">
                        </div>
                        <div class="form-group">
                            <label for="c_description">Short Description or Notes</label>
                            <textarea class="form-control" id="c_description" name="c_description" rows="5" placeholder="Describe your contribution, data source, or supporting literature..."><?= htmlspecialchars($c_description) ?></textarea>
                        </div>
                        <input type="hidden" name="contribution_form_submit" value="1">
                        <button type="submit" id="contributionSubmitBtn">Submit Contribution</button>
                    </form>
                    <p class="mt-4"><small>All submissions will be reviewed by the ARKbase curation team. Contributors retain authorship, and accepted contributions will be credited (with consent). By submitting, you agree to share data under a CC-BY 4.0 or equivalent open license.</small></p>
                </div>
            </div>
        </div>

        <!-- =========== RIGHT COLUMN: OUR TEAM (4 COLUMNS ON DESKTOP) =========== -->
        <div class="col-lg-6">
            <div class="container team-section">
                <h1 class="section-title">Our Team</h1>
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/ankita.jpg" alt="Ankita Das"><h3><a href="#">Ankita Das</a></h3><p class="title">Project Associate I</p><p class="affiliation">CSIR-IMTech</p></div></div>
                   
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/dr_bhupender.png" alt="Bhupender Singh"><h3><a href="#">Bhupender Singh</a></h3><p class="title">Project Scientist I</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/harsh.jpeg" alt="Harsh Bajetha"><h3><a href="#">Harsh Bajetha</a></h3><p class="title">Project Associate I</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/jasleen.jpg" alt="Jasleen Kaur"><h3><a href="#">Jasleen Kaur</a></h3><p class="title">Project Candidate</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/mayur.jpg" alt="Mayur Zarkar"><h3><a href="#">Mayur Zarkar</a></h3><p class="title">Project Associate I</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/pranav.jpg" alt="Pranavathiyani G."><h3><a href="#">Pranavathiyani G.</a></h3><p class="title">Project Scientist I</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/raghav.jpg" alt="Raghav Sankhdher"><h3><a href="#">Raghav Sankhdher</a></h3><p class="title">Sr. Project Associate</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/rupali.jpg" alt="Rupali Aggrawal"><h3><a href="#">Rupali Aggrawal</a></h3><p class="title">Dissertation Student</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/shivani.jpg" alt="Shivani Seth"><h3><a href="#">Shivani Seth</a></h3><p class="title">Junior Research Fellow</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/spd.jpg" alt="Shweta Pandey"><h3><a href="#">Shweta Pandey</a></h3><p class="title">Senior Research Fellow</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/simran.jpg" alt="Simran Gambhir"><h3><a href="#">Simran Gambhir</a></h3><p class="title">Project Associate I</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/suhani .jpg" alt="Suhani Dange"><h3><a href="#">Suhani Dange</a></h3><p class="title">Project Associate I</p><p class="affiliation">CSIR-IMTech</p></div></div>
                    <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/upasana.jpg" alt="Upasana Maity"><h3><a href="#">Upasana Maity</a></h3><p class="title">Project Associate II</p><p class="affiliation">CSIR-IMTech</p></div></div>
                     <div class="col-lg-3 col-md-6 col-6 mb-4"><div class="contact-card"><img src="photos/dr_anshu.png" alt="Anshu Bhardwaj"><h3><a href="#">Anshu Bhardwaj</a></h3><p class="title">Principal Scientist</p><p class="affiliation">CSIR-IMTech</p></div></div>
                </div>
            </div>
        </div>
    </div> <!-- End of the two-column row -->
</div>

<!-- =========== GOOGLE MAPS SECTION (FULL-WIDTH) =========== -->
<div class="container-fluid map-container">
    <h2 class="section-title">Find Us At CSIR-IMTech, Chandigarh</h2>
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3429.0075085464823!2d76.73144557527894!3d30.746290584977775!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390fede2a3e1498f%3A0xe9e249a6b5b8e967!2sCSIR%20%E2%80%93%20Institute%20Of%20Microbial%20Technology%20(IMTECH)!5e0!3m2!1sen!2sin!4v1719998734071!5m2!1sen!2sin" 
        height="450" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>


<script>
    // Listen for the form submission event
    document.getElementById('contributionForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('contributionSubmitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...`;
    });
</script>

</body>
<?php include 'footer.php'; ?>
</html>