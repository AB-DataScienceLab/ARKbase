<!DOCTYPE html>
<?php include 'header.php'; ?>
<html>
<head>
    <meta charset="UTF-8">
    <title>NRCoInt</title>
    <!-- Include 3Dmol.js -->
    <script src="https://3Dmol.csb.pitt.edu/build/3Dmol-min.js"></script>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #10428d;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
        }

        .container {
            flex: 1; /* Allows the container to grow and fill the available space */
            text-align: center; /* Center the content inside */
            background-color: #fff;
            padding: 50px; /* Add some padding */
            border-radius: 20px; /* Rounded corners */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            

        }

        .footer {
            background-color: #d4edc9; /* Example footer background color */
            text-align: center;
            padding: 10px 0;
            font-size: 1.0em;
            font-weight: bold;
        }

        .header {
            background-color: #d4edc9;
            text-align: center;
            padding: 2px 0;
            font-size: 1.0em;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 30px;
            margin: 0 auto;
        }

        .header1 {
            background-color: #dfffc9;
            text-align: center;
            padding: 1px 0;
            font-size: 1.5em;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 40px;
            margin: 0 auto;
        }

        .header2 {
            background-color: #ffffff;
            text-align: center;
            padding: 10px 0;
            font-size: 1.0em;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: left;
            height: 30px;
            margin: 0 auto;
        }

        .box {
/*            width: 48%;*/
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            text-align: center;
/*            min-width: 10px;*/
            margin: 10px;
            
        }

        .box1 {
            width: 90%;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 50px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: 20px 10px;
        }

        .arrow {
            font-size: 2.2em;
            margin: 10px 0;
        }

        .viewer-container {
            margin: 10px 0;
            width: 100%;
            height: 350px;
            border: 1px solid #ddd;
            background-color: #f7f7f7;
            border-radius: 5px;
            position: relative;
        }

        .viewer {
            width: 100%;
            height: 100%;
        }

        .score-table {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            gap: 20px;
        }

        .score-column {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

 		.score-header {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .score-value {
            padding: 10px 0;
            font-size: 1.2em;
            color: #333;
        }

        .highlight1 {
            color: red;
            font-weight: bold;
        }

        table, th, td {
            border: 2px solid black; 
            border-collapse: collapse;
        }

        th, td {
            padding: 5px;
        }

        .section-header {
            background-color: #d4edc9;
            padding: 15px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s;
             font-size: 1.3em;
             font-color: #10428d;
        }

        .section-header:hover {
            background-color: #c1e0b4;
            font-size: 1.5em;
        }

        .instruction-text {
            font-size: 1.3em;
            color: #FF0000;
            text-align: center;
            margin: 10px 0;
            font-weight: bold;
            }

            .instruction-text1 {
            font-size: 1.3em;
            color: #FF0000;
            text-align: left;
            margin: 10px 0;
            font-weight: bold;
            padding: 15px;
            }
            .container .highlight {
    text-align: left; /* Align text to the left */
    display: block; /* Ensure the span behaves as a block element */
    padding: 10px; /* Add some padding for better readability */
}
            
    </style>
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>
<body>
    <div class="container">
        <br>
        <div class="instruction-text">Click on the section below to view detailed results.</div>
        <br>
        
        <div class="section-header" id="toggleContent">Docking result of coregulator & NR (with or without modification)</div>
        
        <div class="main-container" id="mainContent">
            <div class="header2">RAR&#945 is an important nuclear receptor that modulates the development of immune cells as well as other cellular functions like apoptosis. Phosphorylation at its AF2 domain, specifically at the serine 369 residue, can enhance RAR activity by improving the effectiveness of DNA binding. We docked several coregulators to RAR&#945 with or without phosphorylation in order to examine this molecular mechanism.</div><br>
            
            <div class="section-container">
                
                
                <div class="header">RAR&#945 with RIP140</div>
                
                <div class="container">
                    
                    <div class="box">
                        <p><strong>Complex of RAR&#945 with agonist (3KMR)</strong></p>
                        <div class="arrow">&#8595;</div>
                        <p>Delete water and minimize the structure</p>
                        <div class="arrow">&#8595;</div>
                        <p>Docking with RIP140</p>
                        <div class="arrow">&#8595;</div>
                        <div class="viewer-container">
                            <div id="viewer5" class="viewer"></div>
                      </div>
                        <span>NOTE: Use your mouse to drag, rotate, and zoom in and out of the structure</span>
                        <div class="score-table">
                            <div class="score-column">
                                <div class="score-header">ZDOCK</div>
                                <div class="score-value">960.637</div>
                            </div>
                            <div class="score-column">
                                <div class="score-header">HDOCK</div>
                                <div class="score-value">-515.70</div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <p><strong>Complex of <span class="highlight1">p369</span> RAR&#945 with agonist (3KMR phosphorylation added in pyMol)</strong></p>
                        <div class="arrow">&#8595;</div>
                        <p>Delete water and minimize the structure</p>
                        <div class="arrow">&#8595;</div>
                        <p>Docking with RIP140</p>
                        <div class="arrow">&#8595;</div>
                        <div class="viewer-container">
                            <div id="viewer6" class="viewer"></div>
                        </div>
                        <span>NOTE: Use your mouse to drag, rotate, and zoom in and out of the structure</span>
                        
                        <div class="score-table">
                            <div class="score-column">
                                <div class="score-header">ZDOCK</div>
                                <div class="score-value">1193.588</div>
                            </div>
                            <div class="score-column">
                                <div class="score-header">HDOCK</div>
                                <div class="score-value">-607.80</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=" box1">
                    <center><img src="images/image3.png" alt=""; ></center>
                </div>

                <div class="container">
                    <span class="highlight">We validated the interaction between the retinoic acid receptor (RAR) and receptor-interacting protein 140 (RIP140), a versatile regulator known to function as both a co-activator and a co-repressor, depending on the cellular context and signaling pathways. In the case of retinoic acid receptors, RIP140 has been shown to predominantly act as a co-activator, leveraging its interaction with the activation function-2 (AF-2) domain, a crucial region for transcriptional activation. Our analysis demonstrated that RIP140 binds more efficiently to RAR when the receptor is phosphorylated at serine 369 (S369). This enhanced interaction suggests that phosphorylation induces a structural rearrangement in RAR, likely optimizing the AF-2 domain for binding. Such a configuration may facilitate the recruitment of transcriptional co-activators, reinforcing the activating role of RIP140. This was further corroborated by co-immunoprecipitation (CoIP) experiments, which provided direct evidence of increased RIP140-RAR complex formation in the phosphorylated state.</span>
                </div>
            </div>

            <div class="header">RAR&#945 with NCOA2</div>
            <div class="container">
                <div class="box">
                    <p><strong>Complex of RAR&#945 with agonist (3KMR)</strong></p>
                    <div class="arrow">&#8595;</div>
                    <p>Delete water and minimize the structure</p>
                    <div class="arrow">&#8595;</div>
                    <p>Docking with NCOA2</p>
                    <div class="arrow">&#8595;</div>
                    <div class="viewer-container">
                        <div id="viewer1" class="viewer"></div>
                    </div>
                    <span>NOTE: Use your mouse to drag, rotate, and zoom in and out of the structure</span>
                    <div class="score-table">
                        <div class="score-column">
                            <div class="score-header">ZDOCK</div>
                            <div class="score-value">945.649</div>
                        </div>
                        <div class="score-column">
                            <div class="score-header">HDOCK</div>
                            <div class="score-value">-569.5</div>
                        </div>
                    </div>
                </div>

                <div class="box">
                    <p><strong>Complex of <span class="highlight1">p369</span> RAR&#945 with agonist (3KMR phosphorylation added in pyMol)</strong></p>
                    <div class="arrow">&#8595;</div>
                    <p>Delete water and minimize the structure</p>
                    <div class="arrow">&#8595;</div>
                    <p>Docking with NCOA2</p>
                    <div class="arrow">&#8595;</div>
                    <div class="viewer-container">
                        <div id="viewer2" class="viewer"></div>
                    </div>
                    <span>NOTE: Use your mouse to drag, rotate, and zoom in and out of the structure</span>
                    <div class="score-table">
                        <div class="score-column">
                            <div class="score-header">ZDOCK</div>
                            <div class="score-value">1269.608</div>
                        </div>
                        <div class="score-column">
                            <div class="score-header">HDOCK</div>
                            <div class="score-value">-607.8</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <span class="highlight">The docking studies provided crucial insights into the molecular interactions of the retinoic acid receptor (RAR) with its co-activator, nuclear receptor coactivator 2 (NCOA2). NCOA2 is a key player in the transcriptional regulation of genes targeted by nuclear receptors, acting as a bridge to recruit other transcriptional machinery. By comparing the binding affinities of NCOA2 to both the phosphorylated and unphosphorylated states of RAR, the analysis revealed a marked improvement in binding efficiency when RAR was phosphorylated at serine 369 (S369).</span>
            </div>



			<div class="section-container">
                
                
                <div class="header">RAR&#945 with NCOR1</div>
                
                <div class="container">
                    
                    <div class="box">
                        <p><strong>Complex of RAR&#945 with agonist (3KMR)</strong></p>
                        <div class="arrow">&#8595;</div>
                        <p>Delete water and minimize the structure</p>
                        <div class="arrow">&#8595;</div>
                        <p>Docking with RIP140</p>
                        <div class="arrow">&#8595;</div>
                        <div class="viewer-container">
                            <div id="viewer3" class="viewer"></div>
                      </div>
                        <span>NOTE: Use your mouse to drag, rotate, and zoom in and out of the structure</span>
                        <div class="score-table">
                            <div class="score-column">
                                <div class="score-header">ZDOCK</div>
                                <div class="score-value">1238.649</div>
                            </div>
                            <div class="score-column">
                                <div class="score-header">HDOCK</div>
                                <div class="score-value">-282.38</div>
                            </div>
                        </div>
                    </div>
                

                <div class="box">
                    <p><strong>Complex of <span class="highlight1">p369</span> RAR&#945 with agonist (3KMR phosphorylation added in pyMol)</strong></p>
                    <div class="arrow">&#8595;</div>
                    <p>Delete water and minimize the structure</p>
                    <div class="arrow">&#8595;</div>
                    <p>Docking with NCOR1</p>
                    <div class="arrow">&#8595;</div>
                    <div class="viewer-container">
                        <div id="viewer4" class="viewer"></div>
                    </div>
                    <span>NOTE: Use your mouse to drag, rotate, and zoom in and out of the structure</span>
                    <div class="score-table">
                        <div class="score-column">
                            <div class="score-header">ZDOCK</div>
                            <div class="score-value">1218.14</div>
                        </div>
                        <div class="score-column">
                            <div class="score-header">HDOCK</div>
                            <div class="score-value">-271.92</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <span class="highlight">The validation of docking results with nuclear receptor co-repressor 1 (NCOR1), a primary co-repressor of the retinoic acid receptor (RAR), offers critical insights into the regulatory mechanism of RAR activity. NCOR1 is integral to suppressing gene transcription by forming complexes that recruit histone deacetylases and other repressive machinery. When comparing the binding affinities of NCOR1 to both phosphorylated and unphosphorylated RAR, it was observed that the phosphorylated form of RAR (phosphorylation at serine 369) exhibited significantly reduced binding efficiency with NCOR1. This reduced binding suggests that phosphorylation at S369 disrupts the interaction interface between RAR and NCOR1, likely due to conformational changes induced by the post-translational modification.</span>
            </div>
        </div>
        </div>
        <br>

        <div class="section-header" id="toggleContent2">Minimized structures of some major NRs along with PTMs and their coregulators</div>
        
        <div class="main-container2" id="mainContent2">
            <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; margin-top: 10px;">
                <table style="width: 80%;">
                    <tr>
                        <th>NRs</th>
                        <th>PTMs</th>
                        <th>Residue Numbers</th>
                        <th>Co-activator</th>
                        <th>Co-repressor</th>
                    </tr>
                    <tr>
                        <td rowspan="2"><a href="Min_Structures/NRs/LXRB.pdb" download="LXRB.pdb">LXRB</a></td>
                        <td>Acetylation</td>
                        <td><a href="Min_Structures/NRs/aK447_LXRB.pdb" download="aK447_LXRB.pdb">K447</a></td>
                        <td rowspan="2"><a href="Min_Structures/Coregulators/SRC-1.pdb" download="SRC-1.pdb">SRC1</a></td>
                        <td rowspan="2"><span><a href="Min_Structures/Coregulators/rip140_minimized.pdb" download="rip140_minimized.pdb">RIP140</a><br><a href="Min_Structures/Coregulators/NCOR_minimized.pdb" download=" NCOR_minimized.pdb">NCOR</a><br><a href="Min_Structures/Coregulators/SHP.pdb" download="SHP.pdb">SHP</a></span></td>
                    </tr>
                    <tr>
                        <td>Phosphorylation</td>
                        <td><a href="Min_Structures/NRs/pT442_LXRB.pdb" download="pT442_LXRB.pdb">T442</a></td>
                    </tr>
                    <tr>
                        <td><a href="Min_Structures/NRs/FXR.pdb" download="FXR.pdb">FXR</a></td>
                        <td>Phosphorylation</td>
                        <td><a href="Min_Structures/NRs/pS327_FXRA.pdb" download="pS327_FXRA.pdb">S327</a></td>
                        <td></td>
                        <td><a href="Min_Structures/Coregulators/SHP.pdb" download="SHP.pdb">SHP</a></td>
                    </tr>
                    <tr>
                        <td><a href="Min_Structures/NRs/RARA.pdb" download="RARA.pdb">RARA</a></td>
                        <td>Phosphorylation</td>
                        <td><a href="Min_Structures/NRs/pS369_RARA.pdb" download="pS369_RARA.pdb">S369</a></td>
                        <td><a href="Min_Structures/Coregulators/ncoa2_minimized.pdb" download="ncoa2_minimized.pdb">NCOA2</a></td>
                        <td><span><a href="Min_Structures/Coregulators/rip140_minimized.pdb" download="rip140_minimized.pdb">RIP140</a><br><a href="Min_Structures/Coregulators/NCOR_minimized.pdb" download="NCOR_minimized.pdb">NCOR</a></span></td>
                    </tr>
                </table>
                  
            </div>
            <br>
            <div class="instruction-text1">Note: Click on the link to download the .pdb file</div>
        </div>
</div>
        
    </div>
<?php include 'footer.php'; ?>
    <script>
        // Set initial display state for main content
        document.getElementById('mainContent').style.display = 'none';
        document.getElementById('mainContent2').style.display = 'none';

        // Function to toggle the visibility of the main content
        document.getElementById('toggleContent').addEventListener('click', function() {
            const mainContent = document.getElementById('mainContent');
            mainContent.style.display = (mainContent.style.display === "none" || mainContent.style.display === "") ? "block" : "none";
        });

        // Function to toggle the visibility of the second main content
        document.getElementById('toggleContent2').addEventListener('click', function() {
            const mainContent2 = document.getElementById('mainContent2');
            mainContent2.style.display = (mainContent2.style.display === "none" || mainContent2.style.display === "") ? "block" : "none";
        });

        // Function to load a PDB file into a viewer
        function loadPDB(viewerId, pdbPath) {
            const viewerElement = document.getElementById(viewerId);
            if (!viewerElement) {
                console.error(`Viewer element with ID ${viewerId} not found.`);
                return;
            }
            
            const viewer = $3Dmol.createViewer(viewerElement, { backgroundColor: "white" });

            fetch(pdbPath)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Failed to load PDB file: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then(data => {
                    viewer.addModel(data, "pdb");
                    viewer.setStyle({}, { cartoon: { color: "spectrum" } });
                    viewer.zoomTo();
                    viewer.render();
                })
                .catch(error => {
                    console.error(`Error loading PDB for ${viewerId}:`, error);
                });
        }

        // Load PDB files into viewers
        loadPDB("viewer1", "RARA_NCOA2.pdb");
        loadPDB("viewer2", "RARA_PHOSPO_P369_NCOA2.pdb");
        loadPDB("viewer3", "RARA_NCOR1.pdb");
        loadPDB("viewer4", "RARA_NCOR1.pdb");
        loadPDB("viewer5", "RARA_RIP140.pdb");
        loadPDB("viewer6", "RARA_PHOSPHO_369_RIP140.pdb");
    </script>
</body>
</html>