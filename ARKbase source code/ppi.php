<?php include 'header.php'?>

<!-- Page-specific styles for the PPI layout -->
<style>
    /*
       Note: The .body class from your original file targets the <body> tag.
       The body tag is now controlled by header.php. This rule might not be needed
       if the body styling in header.php is sufficient. I've kept it for now.
    */
    .body {
        font-family: 'Arial';
        background-color: var(--arkbase-light-gray);
    }
    
    .back-button {
        background: rgba(255,255,255,0.2);
        color: white;
        border: 2px solid rgba(255,255,255,0.3);
        padding: 10px 20px;
        border-radius: 25px;
        text-decoration: none;
        display: inline-block;
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-2px);
    }

    .content-wrapper {
        display: flex;
        min-height: 600px;
    }

    .sidebar {
        width: 350px;
        background: #f8f9ff;
        padding: 30px;
        border-right: 1px solid #e1e5e9;
    }

    
    .data-summary {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }

    .data-summary h3 {
        margin: 0 0 20px 0;
        color: #2c3e50;
        font-size: 1.3em;
        text-align: center;
    }

    .stats-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .stats-list li {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        color: #555;
    }

    .stats-list li:last-child {
        border-bottom: none;
    }

    .chart-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: conic-gradient(
            #ffd700 0deg 120deg,
            #4fc3f7 120deg 200deg,
            #81c784 200deg 280deg,
            #f06292 280deg 360deg
        );
        margin: 20px auto;
        position: relative;
        cursor: pointer;
    }

    .chart-placeholder::after {
        content: '';
        position: absolute;
        top: 30px;
        left: 30px;
        width: 60px;
        height: 60px;
        background: white;
        border-radius: 50%;
    }

    .chart-note {
        text-align: center;
        font-size: 0.9em;
        color: #666;
        margin-top: 15px;
    }

    /*
      Overriding the .main-content from header.php for this specific page if needed.
      The background from header.php is a gradient, this makes it a solid color.
    */
    .main-content {
        background: #f9f9f9; /* A simple light gray background */
        padding: 2rem 0;
    }

    .priority-section {
        margin-bottom: 30px;
    }

    .priority-section h2 {
        color: #2c3e50;
        margin-bottom: 20px;
        font-size: 1.8em;
    }

    .pathogen-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .pathogen-card {
        background: white;
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid #ddd;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .pathogen-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.12);
        text-decoration: none;
        border-color: var(--arkbase-primary);
    }
    
    .pathogen-card img {
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .pathogen-card.critical {
        background: #fff5f5;
        border-color: #ffb8b8;
    }
    .pathogen-card.critical:hover {
        border-color: #ff6b6b;
    }

    .pathogen-card.high {
        background: #f0f7ff;
        border-color: #b3d4fc;
    }
    .pathogen-card.high:hover {
        border-color: #4a90e2;
    }

    .pathogen-card.medium {
        background: #fffaf0;
        border-color: #ffe0b3;
    }
    .pathogen-card.medium:hover {
        border-color: #ffa726;
    }

    .priority-label {
        display: inline-block;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 0.9em;
        margin-top: auto; /* Pushes the label to the bottom */
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .priority-label.critical {
        background: rgba(255, 107, 107, 0.2);
        color: #d32f2f;
    }

    .pathogen-name {
        font-size: 1.2em;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    /* Removed the explicit italic style from CSS for better control in HTML */
    .pathogen-name em {
        font-style: italic;
    }

    @media (max-width: 768px) {
        .pathogen-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- This script tag was incomplete in the original file. -->
<!-- <script src="script.js?v=<?php echo time(); ?>"></script> -->

<div class="main-content">
    <div class="container">
        <h2 class="text-center mb-5">Interactome Landscape of WHO-Bacterial Priority Pathogens</h2>

        <div class="priority-section">
            <h2 class="text-center text-md-start">Critical Priority Pathogens</h2>
            <div class="pathogen-grid">
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Acinetobacter_baumannii&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=&page=1" class="pathogen-card critical">
                    <img src="/anshu/arkbase/PPI_GA/AB.png" style="width:100%" alt="Acinetobacter baumannii PPI">
                    <div>
                        <div class="pathogen-name"><em>Acinetobacter baumannii</em></div>
                        <div class="priority-label critical">2103 Proteins | 31280 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Klebsiella_pneumoniae&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card critical">
                    <img src="/anshu/arkbase/PPI_GA/KP.png" style="width:100%" alt="Klebsiella pneumoniae PPI">
                    <div>
                        <div class="pathogen-name"><em>Klebsiella pneumoniae</em></div>
                        <div class="priority-label critical">3708 Proteins | 43544 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Escherichia_coli&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card critical">
                    <img src="/anshu/arkbase/PPI_GA/EC.png" style="width:100%" alt="Escherichia coli PPI">
                    <div>
                        <div class="pathogen-name"><em>Escherichia coli</em></div>
                        <div class="priority-label critical">3670 Proteins | 44486 Interactions</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="priority-section">
            <h2 class="text-center text-md-start">High Priority Pathogens</h2>
            <div class="pathogen-grid">
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Shigella_flexneri&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card high">
                    <img src="/anshu/arkbase/PPI_GA/SF.png" style="width:100%" alt="Shigella flexneri PPI">
                    <div>
                        <div class="pathogen-name"><em>Shigella flexneri</em></div>
                        <div class="priority-label critical">3142 Proteins | 51857 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Shigella_sonnei&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card high">
                    <img src="/anshu/arkbase/PPI_GA/SS.png" style="width:100%" alt="Shigella sonnei PPI">
                    <div>
                        <div class="pathogen-name"><em>Shigella sonnei</em></div>
                        <div class="priority-label critical">3326 Proteins | 56674 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Enterococcus_faecium&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card high">
                    <img src="/anshu/arkbase/PPI_GA/EF.png" style="width:100%" alt="Enterococcus faecium PPI">
                    <div>
                        <div class="pathogen-name"><em>Enterococcus faecium</em></div>
                        <div class="priority-label critical">1964 Proteins | 28408 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Pseudomonas_aeruginosa&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card high">
                    <img src="/anshu/arkbase/PPI_GA/PA.png" style="width:100%" alt="Pseudomonas aeruginosa PPI">
                    <div>
                        <div class="pathogen-name"><em>Pseudomonas aeruginosa</em></div>
                        <div class="priority-label critical">4478 Proteins | 50582 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Salmonella_enterica&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card high">
                    <img src="/anshu/arkbase/PPI_GA/SE.png" style="width:100%" alt="Salmonella enterica PPI">
                    <div>
                        <div class="pathogen-name"><em>Salmonella enterica</em></div>
                        <div class="priority-label critical">3558 Proteins | 43126 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Neisseria_gonorrhoeae&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card high">
                    <img src="/anshu/arkbase/PPI_GA/NG.png" style="width:100%" alt="Neisseria gonorrhoeae PPI">
                    <div>
                        <div class="pathogen-name"><em>Neisseria gonorrhoeae</em></div>
                        <div class="priority-label critical">1161 Proteins | 20348 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Staphylococcus_aureus&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card high">
                    <img src="/anshu/arkbase/PPI_GA/SA.png" style="width:100%" alt="Staphylococcus aureus PPI">
                    <div>
                        <div class="pathogen-name"><em>Staphylococcus aureus</em></div>
                        <div class="priority-label critical">2244 Proteins | 34094 Interactions</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="priority-section">
            <h2 class="text-center text-md-start">Medium Priority Pathogens</h2>
            <div class="pathogen-grid">
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Streptococcus_pyogenes&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card medium">
                    <img src="/anshu/arkbase/PPI_GA/Spy.png" style="width:100%" alt="Streptococcus pyogenes PPI">
                    <div>
                        <div class="pathogen-name"><em>Streptococcus pyogenes</em></div>
                        <div class="priority-label critical">1399 Proteins | 24780 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Streptococcus_pneumoniae&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card medium">
                    <img src="/anshu/arkbase/PPI_GA/SP.png" style="width:100%" alt="Streptococcus pneumoniae PPI">
                    <div>
                        <div class="pathogen-name"><em>Streptococcus pneumoniae</em></div>
                        <div class="priority-label critical">1524 Proteins | 30018 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Haemophilus_influenzae&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card medium">
                    <img src="/anshu/arkbase/PPI_GA/HI.png" style="width:100%" alt="Haemophilus influenzae PPI">
                    <div>
                        <div class="pathogen-name"><em>Haemophilus influenzae</em></div>
                        <div class="priority-label critical">1328 Proteins | 25416 Interactions</div>
                    </div>
                </a>
                <a href="https://datascience.imtech.res.in/anshu/arkbase/ppi_viewer.php?table=ppi_central&records=25&filter_pathogen=Streptococcus_agalactiae&filter_protien1_id=&filter_bcq=&filter_dcq=&filter_ccq=&filter_COG_category=" class="pathogen-card medium">
                    <img src="/anshu/arkbase/PPI_GA/Sagl.png" style="width:100%" alt="Streptococcus agalactiae PPI">
                    <div>
                        <div class="pathogen-name"><em>Streptococcus agalactiae</em></div>
                        <div class="priority-label critical">1525 Proteins | 28994 Interactions</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'?>