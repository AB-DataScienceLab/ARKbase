<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Assuming head content from your previous file -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARKbase Footer Example</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- ========== MODIFIED STYLE BLOCK START ========== -->
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
            font-size: 20px; 
            padding: 4px 12px;
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
            margin: 0 4px; 
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
         .footer-dropdown-menu a {
            color: var(--arkbase-secondary);
            padding: 8px 20px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
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

        /****************************************************************/
        /********** CSS FOR FOOTER "INSIGHT MODULE" HOVER LABEL *********/
        /****************************************************************/
        
        .footer-item,
        .footer-dropdown {
            position: relative;
            display: inline-block;
        }

        .insight-label-footer {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            
            /* [MODIFIED] Position the label's TOP edge at the BOTTOM of its parent */
            top: 100%; 
            
            left: 50%;
            
            /* [MODIFIED] Start the animation from slightly above to create a "drop in" effect */
            transform: translateX(-50%) translateY(-10px); 
            
            z-index: 1001; 
            
            /* [MODIFIED] Change margin from bottom to top for spacing */
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

            /* [MODIFIED] Position the pointer at the TOP of the label */
            bottom: 100%; 

            left: 50%;
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;

            /* [MODIFIED] Flip the border to make the triangle point UPWARDS */
            border-color: transparent transparent var(--arkbase-secondary) transparent;
        }

        .footer-item:hover > .insight-label-footer,
        .footer-dropdown:hover > .insight-label-footer {
            visibility: visible;
            opacity: 1;

            /* [MODIFIED] Final animation position remains the same */
            transform: translateX(-50%) translateY(0);
        }
    </style>
    <!-- ========== MODIFIED STYLE BLOCK END ========== -->

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
            <!-- First three pathogens -->
            <a href="operons.php?pathogen=a_baumannii"><em>Acinetobacter baumannii</em></a>
            <a href="operons.php?pathogen=e_coli"><em>Escherichia coli</em></a>
            <a href="operons.php?pathogen=k_pneumoniae"><em>Klebsiella pneumoniae</em></a>

            <!-- Rest of the pathogens -->
            <a href="operons.php?pathogen=n_gonorrhoeae"><em>Neisseria gonorrhoeae</em></a>
            <a href="operons.php?pathogen=p_aeruginosa"><em>Pseudomonas aeruginosa</em></a>
            <a href="operons.php?pathogen=s_aureus"><em>Staphylococcus aureus</em></a>
            <a href="operons.php?pathogen=s_enterica"><em>Salmonella enterica</em></a>
            <a href="operons.php?pathogen=h_influenza"><em>Haemophilus influenzae</em></a>
            <a href="operons.php?pathogen=s_agalactiae"><em>Streptococcus agalactiae</em></a>
            <a href="operons.php?pathogen=s_pneumoniae"><em>Streptococcus pneumoniae</em></a>
            <a href="operons.php?pathogen=s_sonnei"><em>Shigella sonnei</em></a>
            <a href="operons.php?pathogen=s_flexneri"><em>Shigella flexneri</em></a>
            <a href="operons.php?pathogen=s_pyogenes"><em>Streptococcus pyogenes</em></a>
            <a href="operons.php?pathogen=e_faecium"><em>Enterococcus faecium</em></a>

            <!-- Optional: View all link -->
            <a href="operons.php"><strong>View All Pathogens</strong></a>
        </div>
    </div>
                                
                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a class="footer-link" href="https://datascience.imtech.res.in/anshu/arkbase/genome_Acinetobacter_baumannii.php">Antimicrobial Susceptibility</a>
                                </div>

                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a href="transcriptomic.php" class="footer-link">Transcriptomics Antibiotics Exposure</a>
                                </div>
                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a href="ml_model.php" class="footer-link">ML Models</a>
                                </div>
                                <div class="footer-item">
                                    <span class="insight-label-footer">INSIGHT MODULE</span>
                                    <a href="data_summary.php" class="footer-link">Data Summary</a>
                                </div>

                                <!-- ========== MODIFIED SECTION START ========== -->
                              
                                <!-- ========== MODIFIED SECTION END ========== -->

                                <div class="footer-item">
<!--                                    <span class="insight-label-footer">INSIGHT MODULE</span>-->
                                    <a href="team.php" class="footer-link">Team</a>
                                </div>

<!--                                 <div class="footer-item">-->
<!--<!--                                    <span class="insight-label-footer">INSIGHT MODULE</span>-->
<!--                                    <a href="team.php" class="footer-link">Citation</a>-->
<!--                                </div>-->
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
                        <p class="mb-1"><strong> ©2025  ARKbase | <a href="https://www.imtech.res.in/">CSIR-IMTECH</a></strong></p>
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