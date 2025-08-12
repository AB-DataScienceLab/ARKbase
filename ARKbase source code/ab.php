<?php
// BLASTN Search Interface - PHP 5.3 Compatible
// Process form submission
include 'header.php'?>

	<script src="script.js?v=<?php echo time(); ?>"></script>

<!DOCTYPE html>
<html lang="en">
<title>ARKbase -Acinetobacter baumannii</title>
<link rel="stylesheet" href="css/style1.css" type="text/css">
<head>

	
   
</head>
<body>
    <div class="container">
        <div class="header">
             <h1 style="font-style: italic;">Acinetobacter baumannii</h1>
              <h2>Host Species: <em> Homo sapiens</em></h2>
        </div>
        
<!--        <div class="style-selector">-->
<!--            <button class="style-btn active" onclick="showLayout('card')">Card Layout</button>-->
<!---->
<!--            <button class="style-btn" onclick="showLayout('accordion')">Accordion</button>-->
<!--            <button class="style-btn" onclick="showLayout('timeline')">Timeline</button>-->
<!--            <button class="style-btn" onclick="showLayout('table')">Table View</button>-->
<!--        </div>-->
        
        <!-- Card Layout -->
        <div id="card-layout" class="card-layout">
            <div class="card">
                <h2 class="card-title">Genome Summary</h2>
              

                <div class="card-content">
                    <div class="card-item">
                        <span class="card-label">Organism Name:</span>
                        <span class="card-value"><em>Acinetobacter baumannii </em> strain ATCC 17978-VU</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Species:</span>
                        <span class="card-value"><em>Acinetobacter baumannii</em></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Strain</span>
                        <span class="card-value">ATCC 17978-VU</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">NCBI Biosample Accession</span>
                        <span class="card-value">SAMN04273153</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">NCBI Taxonomy</span>
                        <span class="card-value">400667</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">ST Type</span>
                        <span class="card-value">437</span>
                    </div>
                    
                   
                  </div>
            </div>
            
            <div class="card">
                <h3 class="card-title">Assembly Information </h3>
                <div class="card-content">
                    <div class="card-item">
                        <span class="card-label">Assembly Accession</span>
                        <span class="card-value">GCF_001593425.2</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Assembly Accession Status</span>
                        <span class="card-value">Complete</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">GC Percentage</span>
                        <span class="card-value">39</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Contigs</span>
                        <span class="card-value">1</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Genome Size</span>
                        <span class="card-value">3.9 Mb</span>
                    </div>
                    
                </div>
            </div>
           
            
            <div class="card">
                <h3 class="card-title">Pan-genome summary</h3>
                <div class="card-content">
                    <div class="card-item">
                        <span class="card-label"> Curated Genomes</span>
                        <span class="card-value"></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Pan genome status</span>
                        <span class="card-value">Open</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label"> Core genes</span>
                        <span class="card-value"></span>
                    </div>
                     <div class="card-item">
                        <span class="card-label">Cloud genes</span>
                        <span class="card-value"></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Unique genes</span>
                        <span class="card-value"></span>
                    </div>
                </div>
            </div>
        </div>
         <br>

        <div id="card-layout" class="card-layout">
            <div class="card">
                <h3 class="card-title">Annotation</h3>
                <div class="card-content">
                    <div class="card-item">
                        <span class="card-label">Genes</span>
                        <span class="card-value">3836</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">CDS</span>
                        <span class="card-value">3742</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">tRNA</span>
                        <span class="card-value">72</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">pseudo</span>
                        <span class="card-value">66</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">ncRNA</span>
                        <span class="card-value">4</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">rRNA</span>
                        <span class="card-value">6</span>
                    </div>
                </div>
            </div>
            
           <div id="card-layout" class="card-layout">
            <div class="card">
                <h3 class="card-title">Genomics Features</h3>
                <h1><center> Circos plot </center> </h1>
                </div>
            </div>
            
    
        


      <div class="card">
                <h3 class="card-title">Operons</h3>
                    <div class="card-content">
                    
                    <div class="card-item">
                        <span class="card-label">Total predicted operons</span>
                        <span class="card-value"></span>
                    </div>
                                  
                   

                    <div class="card-item">
                        <span class="card-label">Number of AMR associated operons</span>
                        <span class="card-value"></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Number of virulence factors associated operons</span>
                        <span class="card-value"></span>
                    </div>
                     
			</div> 
		</div>
		</div><br>
		   <div id="card-layout" class="card-layout">
            <div class="card">
                <h3 class="card-title">BGCs</h3>
                    <div class="card-content">
                    
                    <div class="card-item">
                        <span class="card-label">Total BGCs</span>
                        <span class="card-value">8</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Core Biosynthetic genes</span>
                        <span class="card-value">16</span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Transport-related genes</span>
                        <span class="card-value">18</span>
                    </div>
                   
                    <div class="card-item">
                        <span class="card-label">Regulatory genes</span>
                        <span class="card-value">13</span>
                    </div>

                    <div class="card-item">
                        <span class="card-label">Number of GCFs</span>
                        <span class="card-value"></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Average number of BGCs per family</span>
                        <span class="card-value"></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Max number of BGCs in a family</span>
                        <span class="card-value"></span>
                    </div>
                     
			</div> 
		</div>

		     <div class="card">
                <h3 class="card-title">ML models</h3>
                    <div class="card-content">
                    
                    <div class="card-item">
                        <span class="card-label">Reported ML models for phenotype/MIC prediction</span>
                        <span class="card-value"></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Number of antibiotics coverd in ML models</span>
                        <span class="card-value"></span>
                    </div>
                    <div class="card-item">
                        <span class="card-label">Feature types for available ML models</span>
                        <span class="card-value"></span>
                    </div>
                             
                     
			</div> 
		</div>
     

                             
                     
			</div> 
		</div>
                    
                    
                    </div>

				
			
                
         


        
        <!-- Accordion Layout -->
        <div id="accordion-layout" class="accordion-layout">
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <h3>Strain Overview</h3>
                </div>
                <div class="accordion-content">
                    <div class="card-content">
                        <div class="card-item">
                            <span class="card-label">Organism Name</span>
                            <span class="card-value">Pseudomonas sp. URHB0015</span>
                        </div>
                        <div class="card-item">
                            <span class="card-label">Species</span>
                            <span class="card-value">Pseudomonas sp.</span>
                        </div>
                        <div class="card-item">
                            <span class="card-label">Strain</span>
                            <span class="card-value">URHB0015</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <h3>Assembly Information</h3>
                </div>
                <div class="accordion-content">
                    <div class="card-content">
                        <div class="card-item">
                            <span class="card-label">Assembly DB</span>
                            <span class="card-value">RefSeq</span>
                        </div>
                        <div class="card-item">
                            <span class="card-label">Assembly Accession</span>
                            <span class="card-value">GCF_000620245.1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Timeline Layout -->
        <div id="timeline-layout" class="timeline-layout">
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <h3>Strain Overview</h3>
                    <p><strong>Organism:</strong> Pseudomonas sp. URHB0015</p>
                    <p><strong>Annotated Genes:</strong> 5640</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <h3>Assembly Data</h3>
                    <p><strong>Database:</strong> RefSeq</p>
                    <p><strong>Accession:</strong> GCF_000620245.1</p>
                </div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <h3>Cross-References</h3>
                    <p><strong>BioSample:</strong> SAMN02952997</p>
                    <p><strong>Taxonomy ID:</strong> 1380376</p>
                </div>
            </div>
        </div>
        
        <!-- Table Layout -->
        <div id="table-layout" class="table-layout">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Property</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Strain Overview</td>
                        <td>Organism Name</td>
                        <td>Pseudomonas sp. URHB0015</td>
                    </tr>
                    <tr>
                        <td>Strain Overview</td>
                        <td>Species</td>
                        <td>Pseudomonas sp.</td>
                    </tr>
                    <tr>
                        <td>Assembly</td>
                        <td>Assembly DB</td>
                        <td>RefSeq</td>
                    </tr>
                    <tr>
                        <td>Assembly</td>
                        <td>Assembly Accession</td>
                        <td>GCF_000620245.1</td>
                    </tr>
                    <tr>
                        <td>Cross-References</td>
                        <td>NCBI BioSample</td>
                        <td>SAMN02952997</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function showLayout(layoutType) {
            // Hide all layouts
            document.querySelectorAll('[id$="-layout"]').forEach(layout => {
                layout.style.display = 'none';
            });
            
            // Show selected layout
            document.getElementById(layoutType + '-layout').style.display = 
                layoutType === 'card' ? 'grid' : 
                layoutType === 'timeline' ? 'block' : 
                layoutType === 'table' ? 'block' : 'block';
            
            // Update active button
            document.querySelectorAll('.style-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }
        
        function toggleAccordion(header) {
            const content = header.nextElementSibling;
            const isActive = header.classList.contains('active');
            
            // Close all accordion items
            document.querySelectorAll('.accordion-header').forEach(h => {
                h.classList.remove('active');
                h.nextElementSibling.classList.remove('active');
            });
            
            // Toggle current item
            if (!isActive) {
                header.classList.add('active');
                content.classList.add('active');
            }
        }
    </script>
    <br><br><br>
</body>
<?php include 'footer.php'?>
</html>

<?php