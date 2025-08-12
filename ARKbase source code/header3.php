<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARKbase - Antimicrobial Resistance Knowledgebase</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
   
</head>
<style>
        :root {
            --arkbase-primary: #456997;
            --arkbase-secondary: #10428d;
            --arkbase-light-blue: #e6f0ff;
            --arkbase-light-gray: #f9f9f9;
        }

        body {
            font-family: 'Arial';
            color: var(--arkbase-secondary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: auto
        }

        /* Header Styles */
        .arkbase-header {
            background-color: var(--arkbase-light-gray);
            border-bottom: 2px solid #ccc;
            padding: 15px 0;
        }

        .arkbase-logo {
            height: 60px;
            width: auto;
        }

        .arkbase-title h1 {
            color: var(--arkbase-primary);
            font-weight: bold;
            font-size: 2.5rem;
            margin: 0;
        }

        .arkbase-title h2 {
            color: var(--arkbase-primary);
            font-weight: 600;
            font-size: 1.2rem;
            margin: 0;
        }

        .arkbase-search {
            max-width: 300px;
        }

        /* Navigation Styles */
        .arkbase-nav {
            background-color: var(--arkbase-light-blue);
            padding: ;
            border-bottom: 1px solid #ccc;
        }

        .navbar-nav .nav-link {
            color: var(--arkbase-secondary) !important;
            font-size: 20px;
            padding: 4px 10px !important;
            border-radius: 4px;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(16, 66, 141, 0.1);
            transform: translateY(-1px);
        }

        .navbar-nav .nav-item {
            margin: 0 -3px  ;
        }

        @media (max-width: 1200px) {
            .navbar-nav .nav-link {
                font-size: 18px;
                padding: 4px 8px !important;
            }
        }

        @media (max-width: 992px) {
            .navbar-nav .nav-link {
                font-size: 16px;
                padding: 4px 6px !important;
            }
        }

        .dropdown-menu {
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 6px;
        }

        .dropdown-item {
            color: var(--arkbase-secondary);
            padding: 10px 20px;
            transition: background-color 0.3s ease;
            white-space: nowrap;
        }

        .dropdown-item:hover {
            background-color: #e0e0e0;
            color: var(--arkbase-secondary);
        }
        
        .dropdown-menu li {
            position: relative;
        }

        .dropdown-menu .submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: -7px;
        }
        
        .dropdown-menu > li:hover > .submenu {
            display: block;
        }

        .dropdown-item.dropdown-toggle::after {
            display: inline-block;
            margin-left: .255em;
            vertical-align: .255em;
            content: "";
            border-top: .3em solid transparent;
            border-right: 0;
            border-bottom: .3em solid transparent;
            border-left: .3em solid;
        }


        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .arkbase-description {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 40px;
            border-left: 4px solid var(--arkbase-primary);
        }

        .feature-cards {
            margin: 40px 0;
        }
        
        .feature-card-link, .feature-card-link:hover {
            text-decoration: none;
        }

        .feature-card {
            background: linear-gradient(135deg, var(--arkbase-primary), var(--arkbase-secondary));
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        .feature-card:hover::before {
            top: -25%;
            right: -25%;
            width: 150px;
            height: 150px;
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        /* Footer Styles */
        .arkbase-footer {
            background-color: var(--arkbase-light-gray);
            border-top: 2px solid #ccc;
            margin-top: auto;
        }

        .footer-nav {
            background-color: var(--arkbase-light-blue);
            padding: 20px 0;
        }

        .footer-copyright {
            background-color: #e9ecef;
            padding: 20px 0;
            border-top: 1px solid #ccc;
            text-align: center;
        }

        .footer-link {
            color: var(--arkbase-secondary);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 5px;
        }

        .footer-link:hover {
            background-color: rgba(16, 66, 141, 0.1);
            color: var(--arkbase-secondary);
            text-decoration: none;
        }

        .footer-dropdown {
            position: relative;
            display: inline-block;
            margin: 5px;
        }

        .footer-dropdown .dropdown-toggle {
            color: var(--arkbase-secondary);
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            background: none;
            cursor: pointer;
        }

        .footer-dropdown .dropdown-toggle:hover {
            background-color: rgba(16, 66, 141, 0.1);
            color: var(--arkbase-secondary);
        }

        .footer-dropdown .dropdown-toggle::after {
            content: ' \25B2';
            font-size: 0.8em;
            margin-left: 5px;
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
            max-height: 300px;
            overflow-y: auto;
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
            color: var(--arkbase-secondary);
        }

        @media (max-width: 768px) {
            .arkbase-title h1 {
                font-size: 1.8rem;
            }
            .arkbase-title h2 {
                font-size: 1rem;
            }
            .feature-card {
                margin-bottom: 20px;
            }
            .navbar-nav {
                text-align: center;
            }
            .navbar-nav .nav-link {
                font-size: 18px;
                padding: 8px 12px !important;
                white-space: normal;
            }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

		.slideshow-container {
			max-width: 960px;
			position: relative;
			margin: auto;
		}
		.mySlides { display: none; }
		.prev, .next {
			cursor: pointer;
			position: absolute;
			top: 50%;
			width: auto;
			margin-top: -22px;
			padding: 16px;
			color: white;
			font-weight: bold;
			font-size: 18px;
			transition: 0.6s ease;
			border-radius: 0 3px 3px 0;
			user-select: none;
			background-color: rgba(0,0,0,0.4);
		}
		.next { right: 0; border-radius: 3px 0 0 3px; }
		.prev:hover, .next:hover { background-color: rgba(0,0,0,0.8); }
		.dot {
			cursor: pointer;
			height: 15px;
			width: 15px;
			margin: 0 2px;
			background-color: #bbb;
			border-radius: 50%;
			display: inline-block;
			transition: background-color 0.6s ease;
		}
		.slideshow-container .active, .slideshow-container .dot:hover { background-color: #10428d; }

		.fade { animation-name: fade; animation-duration: 1.5s; }
		@keyframes fade { from {opacity: .4} to {opacity: 1} }

        .dti-view-all-container { padding: 0; }
        .dti-view-all-trigger { width: 100%; font-weight: 500; }
        .dti-view-all-trigger .bi { transition: transform 0.3s ease; font-size: 0.8em; }
        .dti-extra-items {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out;
        }
        .dti-view-all-container:hover .dti-extra-items {
            max-height: 800px;
        }
        .dti-view-all-container:hover .dti-view-all-trigger .bi {
            transform: rotate(180deg);
        }

        .logo img {
            height: 80px; 
            width: auto;  
        }

        .navbar-nav .dropdown, .footer-dropdown {
            position: relative;
            z-index: 9999;
        }
        .dropdown-menu {
            z-index: 9999;
            position: absolute;
        }
        .dropdown-menu .submenu {
            display: none;
            position: absolute;
      
             left: 100%;     top: -7px;
            z-index: 10000;
        }
        .footer-dropdown-menu {
            z-index: 9999;
        }
        .arkbase-nav, .navbar {
            z-index: 9998;
        }
        .main-content, .container {
            position: relative;
            z-index: 1;
        }
        
        .navbar-nav .nav-item {
            position: relative;
        }
        .insight-label {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            bottom: 100%;
           
            transform: translateX(-50%) translateY(10px);
            z-index: 10001;
            margin-bottom: 8px;
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
        .insight-label::after {
            content: '';
            position: absolute;
            top: 100%;
            
            transform: translateX(-50%);
            border-width: 5px;
            border-style: solid;
            border-color: var(--arkbase-secondary) transparent transparent transparent;
        }
        .navbar-nav .nav-item:hover > .insight-label {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    </style>
<body>
    <!-- Header -->
    <header class="arkbase-header">
        <div class="container">
            <!-- ========== MODIFIED HEADER ROW ========== -->
            <div class="row align-items-center justify-content-between">
                
                <!-- Column 1: Left Side Logo -->
                <div class="col-auto logo">
                    <img src="https://datascience.imtech.res.in/anshu/arkbase/images/Logo_Arkbase.png" alt="CSIR-IMTECH Logo">
                </div>
                
                <!-- Column 2: ARKbase Title Area -->
<!-- Column 2: ARKbase Title Area -->
<div class="col-auto arkbase-title text-center">
    <a href="https://datascience.imtech.res.in/anshu/arkbase/index.php" title="Go to ARKbase Home" style="text-decoration: none; color: inherit;">
        <h1>ARKbase</h1>
        <h2>Antimicrobial Resistance Knowledgebase</h2>
    </a>
</div>


                <!-- Column 3: Right Side Logo -->
                <div class="col-auto logo">
                    <img src="https://datascience.imtech.res.in/anshu/arkbase/images/IMTECH_logo_bgclear.png" alt="CSIR-IMTECH Logo">
                </div>

            </div>
            <!-- ========== END OF MODIFIED HEADER ROW ========== -->
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg arkbase-nav">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
              <ul class="navbar-nav">
    <!-- Home Dropdown -- MODIFIED HERE -->
    <li class="nav-item dropdown">
      
        <!-- Add id="home-link-toggle" and ensure href="index.php" -->
        <a class="nav-link dropdown-toggle" href="https://datascience.imtech.res.in/anshu/arkbase/index.php" id="home-link-toggle" role="button" data-bs-toggle="dropdown">
            Home
        </a>
        <ul class="dropdown-menu">
            <!-- The first item is now a bit redundant, but can be kept or removed -->
            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/index.php">About ARKbase</a></li> 
            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/home.php">Reference Genomes</a></li>
            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_Acinetobacter_baumannii.php">Curated Genomes</a></li>
            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/comparative_analysis.php">Comparative Analysis</a></li>
            
        </ul>
    </li>

                    <!-- Pan AMR Dropdown -->
                    <li class="nav-item dropdown">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown">
                        Pan AMR
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=a_baumannii"><em>Acinetobacter baumannii</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=k_pneumoniae"><em>Klebsiella pneumoniae</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=e_coli"><em>Escherichia coli</em></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="dti-view-all-container">
                                <a class="dropdown-item text-center dti-view-all-trigger" href="#">
                                    View All Pathogens <i class="bi bi-chevron-down"></i>
                                </a>
                                <ul class="dti-extra-items">
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=s_flexneri"><em>Shigella flexneri</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=s_sonnei"><em>Shigella sonnei</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=e_faecium"><em>Enterococcus faecium</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=p_aeruginosa"><em>Pseudomonas aeruginosa</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=s_enterica"><em>Salmonella enterica</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=n_gonorrhoeae"><em>Neisseria gonorrhoeae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=s_aureus"><em>Staphylococcus aureus</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=s_pyogenes"><em>Streptococcus pyogenes</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=s_pneumoniae"><em>Streptococcus pneumoniae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=h_influenzae"><em>Haemophilus influenzae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_amr.php?pathogen=s_agalactiae"><em>Streptococcus agalactiae</em></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Pan Virulence Dropdown -->
                    <li class="nav-item dropdown">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown">
                        Pan Virulence
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Acinetobacter%20baumannii"><em>Acinetobacter baumannii</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Klebsiella%20pneumoniae"><em>Klebsiella pneumoniae</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Escherichia%20coli"><em>Escherichia coli</em></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="dti-view-all-container">
                                <a class="dropdown-item text-center dti-view-all-trigger" href="#">
                                    View All Pathogens <i class="bi bi-chevron-down"></i>
                                </a>
                                <ul class="dti-extra-items">
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Shigella%20flexneri"><em>Shigella flexneri</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Shigella%20sonnei"><em>Shigella sonnei</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Enterococcus%20faecium"><em>Enterococcus faecium</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Pseudomonas%20aeruginosa"><em>Pseudomonas aeruginosa</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Salmonella%20enterica"><em>Salmonella enterica</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Neisseria%20gonorrhoeae"><em>Neisseria gonorrhoeae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Staphylococcus%20aureus"><em>Staphylococcus aureus</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Streptococcus%20pyogenes"><em>Streptococcus pyogenes</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Streptococcus%20pneumoniae"><em>Streptococcus pneumoniae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Haemophilus%20influenzae"><em>Haemophilus influenzae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pan_virulence.php?pathogen=Streptococcus%20agalactiae"><em>Streptococcus agalactiae</em></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- Drug Targets Dropdown -->
                    <li class="nav-item dropdown">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown">
                        Drug Targets
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=a_baumannii"><em>Acinetobacter baumannii</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=k_pneumoniae"><em>Klebsiella pneumoniae</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=e_coli"><em>Escherichia coli</em></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="dti-view-all-container">
                                <a class="dropdown-item text-center dti-view-all-trigger" href="#">
                                    View All Pathogens <i class="bi bi-chevron-down"></i>
                                </a>
                                <ul class="dti-extra-items">
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=s_flexneri"><em>Shigella flexneri</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=s_sonnei"><em>Shigella sonnei</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=e_faecium"><em>Enterococcus faecium</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=p_aeruginosa"><em>Pseudomonas aeruginosa</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=s_enterica"><em>Salmonella enterica</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=n_gonorrhoeae"><em>Neisseria gonorrhoeae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=s_aureus"><em>Staphylococcus aureus</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=s_pyogenes"><em>Streptococcus pyogenes</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=s_pneumoniae"><em>Streptococcus pneumoniae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=h_influenzae"><em>Haemophilus influenzae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/drug_targets.php?pathogen=s_agalactiae"><em>Streptococcus agalactiae</em></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                        
                    <!-- PPI Link -->
                    <li class="nav-item">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link" href="https://datascience.imtech.res.in/anshu/arkbase/ppi.php">PPI</a>
                    </li>

                    <!-- HPI Link -->
                    <li class="nav-item">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link" href="https://datascience.imtech.res.in/anshu/arkbase/HPI_wireframe3.php">HPI</a>
                    </li>
                
                    <!-- DTI Dropdown -->
                    <li class="nav-item dropdown">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown">
                        DTI
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/DTI.php?pathogen=Escherichia_coli"><em>Escherichia coli</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/DTI.php?pathogen=Shigella_flexneri"><em>Shigella flexneri</em></a></li>
    
                            <li class="dti-view-all-container">
                                <a class="dropdown-item text-center dti-view-all-trigger" href="#">
                                    View All Pathogens <i class="bi bi-chevron-down"></i>
                                </a>
                                <ul class="dti-extra-items">
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/pathogen_explorer.php?pathogen=Pseudomonas_aeruginosa"><em>Pseudomonas aeruginosa</em></a></li>
     
     
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/DTI.php?pathogen=Staphylococcus_aureus"><em>Staphylococcus aureus</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/DTI.php?pathogen=Streptococcus_pneumoniae"><em>Streptococcus pneumoniae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/DTI.php?pathogen=Haemophilus_influenzae"><em>Haemophilus influenzae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/DTI.php?pathogen=Salmonella_enterica"><em>Salmonella enterica</em></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <!-- Structural Annotation Dropdown -->
                    <li class="nav-item dropdown">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown">
                        Structural Annotation
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=a_baumannii"><em>Acinetobacter baumannii</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=k_pneumoniae"><em>Klebsiella pneumoniae</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=e_coli"><em>Escherichia coli</em></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="dti-view-all-container">
                                <a class="dropdown-item text-center dti-view-all-trigger" href="#">
                                    View All Pathogens <i class="bi bi-chevron-down"></i>
                                </a>
                                <ul class="dti-extra-items">
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=s_flexneri"><em>Shigella flexneri</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=s_sonnei"><em>Shigella sonnei</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=e_faecium"><em>Enterococcus faecium</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=p_aeruginosa"><em>Pseudomonas aeruginosa</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=s_enterica"><em>Salmonella enterica</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=n_gonorrhoeae"><em>Neisseria gonorrhoeae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=s_aureus"><em>Staphylococcus aureus</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=s_pyogenes"><em>Streptococcus pyogenes</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=s_pneumoniae"><em>Streptococcus pneumoniae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=h_influenzae"><em>Haemophilus influenzae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/struct_arg.php?pathogen=s_agalactiae"><em>Streptococcus agalactiae</em></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                
                    <!-- ========== MODIFIED SECTION START ========== -->
                    <!-- Small Molecule Dropdown (replaces BGC Link) -->
                    <li class="nav-item dropdown">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Small Molecule
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/bgc.php">BGC</a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/#">Known Antibiotics</a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/#">Structure Search</a></li>
                        </ul>
                    </li>
                    <!-- ========== MODIFIED SECTION END ========== -->
                    
                    <!-- Genome Browser Dropdown (Replaces ML Models) -->
                    <li class="nav-item dropdown">
                        <span class="insight-label">INSIGHT MODULE</span>
                        <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown">
                        Genome Browser
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/ab_browser/?session=local-i3bfTiJaCS3rfSMeFSs99"><em>Acinetobacter baumannii</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/kp_browser"><em>Klebsiella pneumoniae</em></a></li>
                            <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/ecoli_browser"><em>Escherichia coli</em></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="dti-view-all-container">
                                <a class="dropdown-item text-center dti-view-all-trigger" href="#">
                                    View All Pathogens <i class="bi bi-chevron-down"></i>
                                </a>
                                <ul class="dti-extra-items">
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=s_flexneri"><em>Shigella flexneri</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=s_sonnei"><em>Shigella sonnei</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=e_faecium"><em>Enterococcus faecium</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=p_aeruginosa"><em>Pseudomonas aeruginosa</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=s_enterica"><em>Salmonella enterica</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=n_gonorrhoeae"><em>Neisseria gonorrhoeae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=s_aureus"><em>Staphylococcus aureus</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=s_pyogenes"><em>Streptococcus pyogenes</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=s_pneumoniae"><em>Streptococcus pneumoniae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=h_influenzae"><em>Haemophilus influenzae</em></a></li>
                                    <li><a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/genome_browser.php?pathogen=s_agalactiae"><em>Streptococcus agalactiae</em></a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Search Dropdown -->
                    <li class="nav-item dropdown">
<!--                        <span class="insight-label">INSIGHT MODULE</span>-->
                        <a class="nav-link dropdown-toggle" href="https://datascience.imtech.res.in/anshu/arkbase/#" role="button" data-bs-toggle="dropdown">
                            Search
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/search.php">Search by Pathogen</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="https://datascience.imtech.res.in/anshu/arkbase/blast.php">Search by Sequence</a>
                            </li>
                        </ul>
                    </li>

                    <!-- Help Link -->
                    <li class="nav-item">
<!--                        <span class="insight-label">INSIGHT MODULE</span>-->
                        <a class="nav-link" href="https://datascience.imtech.res.in/anshu/arkbase/faq.php">FAQs & Help</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    
    <script>
    // This script is not strictly necessary with the pure CSS hover solution, 
    // but it is kept in case you want to revert to a click-based toggle on mobile.
    // The CSS solution `.dti-view-all-container:hover .dti-extra-items` will handle the expansion on desktop.
    $(document).ready(function(){
      $('.pathogen-extra-items').hide();
      $('.pathogen-view-all-trigger').on('click', function(e){
        e.preventDefault();
        $('.pathogen-extra-items').slideToggle();
        $(this).find('i').toggleClass('bi-chevron-down bi-chevron-up');
      });
    });

		document.addEventListener("DOMContentLoaded", function() {
        // Find the Home link by its ID
        const homeToggle = document.getElementById('home-link-toggle');
        
        if (homeToggle) {
            homeToggle.addEventListener('click', function(event) {
                // Manually navigate to the link's href attribute
                window.location.href = this.href;
            });
        }
    });
    
    </script>

</body>
</html>