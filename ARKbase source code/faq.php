<?php include 'header.php'; ?>

<style>
  /* Page-specific styles for the FAQ content */
  .faq-container {
    max-width: 1700px;
    margin: 40px auto; /* Add vertical spacing */
    background: #fff;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 10px;
  }
  .faq-container h1 {
    text-align: center;
    color: var(--arkbase-secondary); /* Use theme color */
    margin-bottom: 40px;
  }
  .faq-item {
    margin-bottom: 25px;
    border-bottom: 1px solid #eee;
    padding-bottom: 25px;
  }
  .faq-item:last-child {
      border-bottom: none;
  }
  .faq-item h3 {
    margin-bottom: 10px;
    color: var(--arkbase-primary); /* Use theme color */
    font-size: 1.5rem;
  }
  .faq-item p {
    margin: 0;
    line-height: 1.6;
    color: #333; /* Darker text for readability */
  }
  .download-btn {
    display: block;
    margin: 30px auto 0;
    padding: 12px 24px;
    background-color: var(--arkbase-primary); /* Use theme color */
    color: white;
    border: none;
    border-radius: 6px;
    text-align: center;
    font-size: 16px;
    text-decoration: none;
    transition: background-color 0.3s;
    width: max-content;
  }
  .download-btn:hover {
    background-color: var(--arkbase-secondary); /* Use theme color */
    color: white; /* Ensure text remains white on hover */
    text-decoration: none;
  }
  .faq-item a {
    color: var(--arkbase-primary);
    text-decoration: underline;
    font-weight: 600;
  }
  .faq-item a:hover {
    color: var(--arkbase-secondary);
  }
</style>

<div class="faq-container">
  <h1>Frequently Asked Questions (FAQs)</h1>

  <?php
  $faq = [
    [
      'question' => 'What is ARKbase?',
      'answer' => 'ARKbase is an integrated, curated, value-added knowledgebase developed with the objective of providing a dedicated resource for deep annotation of Bacterial Priority Pathogens as identified by WHO (<a href="https://www.who.int/publications/i/item/9789240093461" target="_blank">link</a>).'
    ],
    [
      'question' => 'What are different modules available in ARKbase?',
      'answer' => 'ARKbase offers three core modules - Database module, Insights module and Comparative Genomics module. Insights module is a composite module and is further divided into 14 modules, namely, AMR, Virulence Factors, Operons, COG classification, AST profiles, Biosynthetic gene clusters, pangenomes, drug targets, drug target interactions, host-pathogen interactions, protein-protein interactions, co-target discovery, transcriptomic datasets and machine learning models.'
    ],
    [
      'question' => 'How are data curated in ARKbase?',
      // MODIFIED LINE: Removed the "More details can be read at..." sentence.
      'answer' => 'Genome datasets are obtained from publicly available resources and then quality checked primarily on two criteria - The MICs reported for these strains are quality checked against CLSI breakpoints and the corresponding genome datasets were checked as per <a href="https://pubmed.ncbi.nlm.nih.gov/36817105/" target="_blank">EUCAST</a> criteria of good quality genomes.'
    ],
    [
      'question' => 'What is the need of ARKbase?',
      'answer' => 'To the best of our knowledge, there is no dedicated resource for WHO Bacterial Priority Pathogen. Moreover, given that AMR is considered a silent pandemic, it is imperative that dedicated and curated resources are made available to the wider scientific community. It is also important to highlight that most of the modules in ARKbase offer data from custom-built pipelines to provide deeper insights towards understanding and addressing AMR.'
    ],
    [
      'question' => 'What are the unique features of ARKbase?',
      'answer' => 'ARKbase is a unique resource linking curated datasets with multi omics, systems biology, AI/ML methods for predicting AMR determinants and Foldseek/AlphaFold based approaches towards understanding molecular basis of resistance, identification of novel drug resistance determinants, and prioritizing potential therapeutic targets. It offers several unique features like, implementation of CLSI/EUCAST standards for reporting AST and genome datasets, identification of co-targets, providing a structure database for ARGs, offering a curated list of AI/ML models as per AWaRe classification, operon mapping of AMR and virulence genes, systems level data for host-pathogen interactions and PPIs for WHO priority pathogens.'
    ],
    [
      'question' => 'Does ARKbase require registration?',
      'answer' => 'No, ARKbase is an open database.'
    ]
  ];

  foreach ($faq as $item) {
    echo "<div class='faq-item'>";
    // Use htmlspecialchars for the question to prevent XSS attacks
    echo "<h3>" . htmlspecialchars($item['question']) . "</h3>";
    // The answer contains HTML, so it is output directly
    echo "<p>" . $item['answer'] . "</p>";
    echo "</div>";
  }
  ?>

  <!-- Help File Download Button -->
  <a href="data/ARKbase_helpfile.pdf" class="download-btn" download>
    Download Help File (PDF)
  </a>
</div>

<?php include 'footer.php'; ?>