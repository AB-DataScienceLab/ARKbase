<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARKbase Footer with Antimicrobial Susceptibility Dropdown</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { /* Adding variables for consistency */
            --arkbase-primary: #456997;
            --arkbase-secondary: #10428d;
            --arkbase-light-blue: #e6f0ff;
        }
        body {
            font-family: 'Arial';
        }
        .arkbase-footer {
            background-color: #f9f9f9;
            border-top: 2px solid #ccc;
        }
        .footer-nav {
            background-color: var(--arkbase-light-blue);
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
        }
        .footer-menu-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .footer-menu-line {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .footer-menu-line + .footer-menu-line {
            margin-top: 1rem;
        }
        
        .footer-link,
        .footer-dropdown .dropdown-toggle {
            font-size: 17.5px; /* Reduced from 20px */
            padding: 4px 8px; /* Reduced horizontal padding */
            white-space: nowrap;
            color: var(--arkbase-secondary);
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .footer-link:hover,
        .footer-dropdown .dropdown-toggle:hover {
             background-color: rgba(16, 66, 141, 0.1);
        }

        .footer-item,
        .footer-dropdown {
            margin: 0 2px; /* Reduced margin between items */
        }

        .footer-dropdown {
            position: relative;
        }
        .footer-dropdown .dropdown-toggle {
            border: none;
            background: none;
        }
        .footer-dropdown-menu {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 250px;
            box-shadow: 0px -8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1000;
            left: 50%;
            transform: translateX(-50%);
            bottom: 100%;
            margin-bottom: 5px;
            border-radius: 6px;
            padding: 10px 0;
            border: 1px solid #ccc;
        }
        .footer-dropdown:hover .footer-dropdown-menu {
            display: block;
        }
        /* ========== MODIFIED CSS RULE START ========== */
         .footer-dropdown-menu a {
            color: black; /* Changed from var(--arkbase-secondary) to black */
            padding: 8px 20px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        /* ========== MODIFIED CSS RULE END ========== */
        .footer-dropdown-menu a:hover {
            background-color: #f0f0f0;
        }
        .footer-copyright {
            background-color: #e9ecef;
            padding: 20px 0;
            border-top: 1px solid #ccc;
            text-align: center;
            color: var(--arkbase-secondary);
        }
        .footer-copyright a {
             color: var(--arkbase-secondary);
        }
        .footer-item,
        .footer-dropdown {
            position: relative;
            display: inline-block;
        }
        .insight-label-footer {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(-10px);
            z-index: 1001;
            margin-top: 10px;
            background-color: var(--arkbase-secondary);
            color: white;
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 0.8rem;
            font-weight: bold;
            white-space: nowrap;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .insight-label-footer::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent var(--arkbase-secondary) transparent;
        }
        .footer-item:hover > .insight-label-footer,
        .footer-dropdown:hover > .insight-label-footer {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>

    <!-- Footer -->
    <footer class="arkbase-footer">
        <!-- Single Footer Menu Section -->
        <div class="footer-nav">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="footer-menu-container">
                            <!-- FIRST LINE OF LINKS -->
                            <div class="footer-menu-line">

                                 <div class="footer-dropdown dropup">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <button class="footer-link dropdown-toggle">Operons</button>
                                    <div class="footer-dropdown-menu">
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=a_baumannii"><em>Acinetobacter baumannii</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=e_coli"><em>Escherichia coli</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=k_pneumoniae"><em>Klebsiella pneumoniae</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=n_gonorrhoeae"><em>Neisseria gonorrhoeae</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=p_aeruginosa"><em>Pseudomonas aeruginosa</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=s_aureus"><em>Staphylococcus aureus</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=s_enterica"><em>Salmonella enterica</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=h_influenza"><em>Haemophilus influenzae</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=s_agalactiae"><em>Streptococcus agalactiae</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=s_pneumoniae"><em>Streptococcus pneumoniae</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=s_sonnei"><em>Shigella sonnei</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=s_flexneri"><em>Shigella flexneri</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=s_pyogenes"><em>Streptococcus pyogenes</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php?pathogen=e_faecium"><em>Enterococcus faecium</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/operons.php"><strong>View All Pathogens</strong></a>
                                    </div>
                                </div>

                                <div class="footer-dropdown dropup">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <button class="footer-link dropdown-toggle">Antimicrobial Susceptibility</button>
                                    <div class="footer-dropdown-menu">
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/genome_Acinetobacter_baumannii.php"><em>Acinetobacter baumannii</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/genome_Enterococcus _faecium.php"><em>Enterococcus faecium</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/genome_Escherichia_coli.php"><em>Escherichia coli</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/genome_Klebsiella_pneumoniae.php"><em>Klebsiella pneumoniae</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/genome_Neisseria_gonorrhoeae.php"><em>Neisseria gonorrhoeae</em></a>
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/genome_Pseudomonas_aeruginosa.php"><em>Pseudomonas aeruginosa</em></a>
                                        <!-- This is a hypothetical link for a summary page -->
                                        <a href="https://datascience.imtech.res.in/anshu/arkbase/antimicrobial_susceptibility.php"><strong>View All Pathogens</strong></a>
                                    </div>
                                </div>

                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a href="https://datascience.imtech.res.in/anshu/arkbase/transcriptomic.php" class="footer-link">Expression Data</a>
                                </div>
                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a href="https://datascience.imtech.res.in/anshu/arkbase/ml_model.php" class="footer-link">ML Models</a>
                                </div>
                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a href="https://datascience.imtech.res.in/anshu/arkbase/data_summary.php" class="footer-link">Data Summary</a>
                                </div>
                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_2&records=25&filter_pathogen=&filter_protien1_id=&filter_bcq1=&filter_protien2_id=&filter_bcq2=" class="footer-link">Co-Target</a>
                                </div>
                                <div class="footer-item">
                                    <a href="https://datascience.imtech.res.in/anshu/arkbase/connect.php" class="footer-link">Connect</a>
                                </div>

                                <div class="footer-item">
                                    <a href="https://datascience.imtech.res.in/anshu/arkbase/faq.php" class="footer-link">FAQs & Help</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="footer-copyright">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <p class="mb-1"><strong> &copy 2025  ARKbase | <a href="https://www.imtech.res.in/">CSIR-IMTECH</a></strong></p>
                        <p class="mb-1">All data are freely accessible to all users, including commercial users.</p>
                        <p class="mb-0">This website doesn't use any cookies.</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>