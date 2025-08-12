<!DOCTYPE HTML>
<?php include 'header.php'?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ARK-Base Pathogen List</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400&display=swap" rel="stylesheet">
	
	<style>
        /* General Body and Theme Setup */
        :root {
            --critical-color: #d9534f;
            --high-color: #337ab7;
            --medium-color: #f0ad4e;
            --text-dark: #34495e;
            --text-light: #7f8c8d;
            --bg-light: #f4f7f9;
            --border-color: #e4e9ed;
            --white-color: #ffffff;
        }

        body {
            font-family: 'Arial';
            background-color: var(--bg-light);
            color: var(--text-dark);
            margin: 0;
        }

        /* Main Content Container */
        .page-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Section Styling */
        .priority-section {
            margin-bottom: 50px;
        }

        .priority-section h2 {
            font-size: 2em;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        /* Responsive Grid Layout */
        .pathogen-grid {
    display: flex;
    flex-direction: row;
    gap: 25px;
    flex-wrap: wrap; /* This keeps rows if too many items for one line */
    justify-content: flex-start;
}


  .pathogen-card {
    background: var(--white-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 4px 6px rgba(0,0,0,0.04);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left-width: 5px;
    border-left-style: solid;

    width: 23%;
    max-width: 280px;
    min-width: 240px;
    height: 180px;
    box-sizing: border-box;
}



        .pathogen-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            text-decoration: none;
            color: inherit;
        }

        /* Priority-specific card styles */
        .pathogen-card.critical { border-left-color: var(--critical-color); }
        .pathogen-card.high { border-left-color: var(--high-color); }
        .pathogen-card.medium { border-left-color: var(--medium-color); }

        /* Priority Label inside the card */
        .priority-label {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 0.8em;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            align-self: flex-start;
        }

        .priority-label.critical {
            background-color: color-mix(in srgb, var(--critical-color) 15%, transparent);
            color: var(--critical-color);
        }
        .priority-label.high {
            background-color: color-mix(in srgb, var(--high-color) 15%, transparent);
            color: var(--high-color);
        }
        .priority-label.medium {
            background-color: color-mix(in srgb, var(--medium-color) 15%, transparent);
            color: var(--medium-color);
        }

        /* Text content inside the card */
        .pathogen-name {
            font-size: 1.25em;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .pathogen-details {
            font-size: 1em;
            color: var(--text-light);
        }

        .scientific-name {
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="page-container">
        <div class="priority-section">
            <h2>Critical Priority Pathogens</h2>
            <!-- CORRECT STRUCTURE: Grid container wraps all the links -->
            <div class="pathogen-grid">
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ab1.php" class="pathogen-card critical">
                    <div class="priority-label critical">Critical</div>
                    <div class="pathogen-name scientific-name">Acinetobacter baumannii</div>
                    <div class="pathogen-details">Carbapenem-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/kp.php" class="pathogen-card critical">
                    <div class="priority-label critical">Critical</div>
                    <div class="pathogen-name scientific-name">Klebsiella pneumoniae</div>
                    <div class="pathogen-details">Carbapenem & 3rd-Gen Cephalosporin-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/Ecoli.php" class="pathogen-card critical">
                    <div class="priority-label critical">Critical</div>
                    <div class="pathogen-name scientific-name">Escherichia coli</div>
                    <div class="pathogen-details">Carbapenem & 3rd-Gen Cephalosporin-resistant</div>
                </a>
            </div>
        </div>
        
        <div class="priority-section">
            <h2>High Priority Pathogens</h2>
            <!-- CORRECT STRUCTURE: Grid container wraps all the links -->
            <div class="pathogen-grid">
                 <a href="https://datascience.imtech.res.in/anshu/arkbase/Shigella_flexneri.php" class="pathogen-card high">
                    <div class="priority-label high">High</div>
                    <div class="pathogen-name scientific-name">Shigella flexneri</div>
                    <div class="pathogen-details">Fluoroquinolone-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/Shigella_sonnei.php" class="pathogen-card high">
                    <div class="priority-label high">High</div>
                    <div class="pathogen-name scientific-name">Shigella sonnei</div>
                    <div class="pathogen-details">Fluoroquinolone-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ef.php" class="pathogen-card high">
                    <div class="priority-label high">High</div>
                    <div class="pathogen-name scientific-name">Enterococcus faecium</div>
                    <div class="pathogen-details">Vancomycin-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/pa.php" class="pathogen-card high">
                    <div class="priority-label high">High</div>
                    <div class="pathogen-name scientific-name">Pseudomonas aeruginosa</div>
                    <div class="pathogen-details">Carbapenem-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/se.php" class="pathogen-card high">
                    <div class="priority-label high">High</div>
                    <div class="pathogen-name scientific-name">Salmonella enterica</div>
                    <div class="pathogen-details">Fluoroquinolone-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ng.php" class="pathogen-card high">
                    <div class="priority-label high">High</div>
                    <div class="pathogen-name scientific-name">Neisseria gonorrhoeae</div>
                    <div class="pathogen-details">Cephalosporin & Fluoroquinolone-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/Staphylococcus_aureus.php" class="pathogen-card high">
                    <div class="priority-label high">High</div>
                    <div class="pathogen-name scientific-name">Staphylococcus aureus</div>
                    <div class="pathogen-details">Methicillin-resistant</div>
                </a>
            </div>
        </div>
        
        <div class="priority-section">
            <h2>Medium Priority Pathogens</h2>
            <!-- CORRECT STRUCTURE: Grid container wraps all the links -->
            <div class="pathogen-grid">
                <a href="https://datascience.imtech.res.in/anshu/arkbase/Streptococcus_pyogenes.php" class="pathogen-card medium">
                    <div class="priority-label medium">Medium</div>
                    <div class="pathogen-name scientific-name">Streptococcus pyogenes</div>
                    <div class="pathogen-details">Macrolide-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/Streptococcus_pneumoniae.php" class="pathogen-card medium">
                    <div class="priority-label medium">Medium</div>
                    <div class="pathogen-name scientific-name">Streptococcus pneumoniae</div>
                    <div class="pathogen-details">Macrolide-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/hi.php" class="pathogen-card medium">
                    <div class="priority-label medium">Medium</div>
                    <div class="pathogen-name scientific-name">Haemophilus influenzae</div>
                    <div class="pathogen-details">Ampicillin-resistant</div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/sa.php" class="pathogen-card medium">
                    <div class="priority-label medium">Medium</div>
                    <div class="pathogen-name scientific-name">Streptococcus agalactiae</div>
                    <div class="pathogen-details">Penicillin-resistant</div>
                </a>
            </div>
        </div>
    </div>
    
	<?php include 'footer.php'?>	
</body>
</html>