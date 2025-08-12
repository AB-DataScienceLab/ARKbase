<?php
// Include the site header
include '/var/www/html/anshu/arkbase/header.php';
?>

<!--
======================================================================
 JAVASCRIPT FUNCTIONS FOR THIS PAGE
======================================================================
-->
<script language="JavaScript">
  function getSmiles1() {
    if (document.JME) {
      var drawing = document.JME.smiles();
      document.form1.smi.value = drawing;
    }
  }

  function readMolecule() {
    if (document.JME) {
      var jme = "45 47 S 6.45 5.57 S 17.48 4.77 O 10.61 9.13 O 2.26 7.99 O 5.06 11.21 O 7.85 11.21 O 12.59 7.19 O 11.33 2.52 N 7.85 7.99 N 10.61 5.22 N 12.89 2.94 N 15.45 6.41 N 18.08 7.58 C 7.85 6.37 C 9.48 6.37 C 9.48 7.99 C 6.45 8.79 C 5.06 7.99 C 5.06 6.37 C 3.66 8.79 C 6.45 10.40 C 12.17 5.64 C 13.31 4.49 C 14.87 4.91 C 0.87 8.79 C 16.12 3.89 C 17.06 6.33 C 10.91 0.97 H 8.20 5.05 H 10.40 6.75 H 4.07 6.55 H 4.71 5.43 H 10.35 4.26 H 4.30 9.56 H 3.02 9.56 H 5.06 12.21 H 1.37 9.66 H 0.00 9.29 H 0.37 7.93 H 16.07 2.89 H 19.07 7.42 H 17.72 8.51 H 9.95 1.23 H 10.65 0.00 H 11.88 0.70 1 14 1 1 19 1 2 26 1 2 27 1 3 16 2 4 20 1 4 25 1 5 21 1 5 36 1 6 21 2 7 22 2 8 11 1 8 28 1 9 14 1 9 16 1 9 17 1 15 10 -1 10 22 1 10 33 1 11 23 2 12 24 1 12 27 2 13 27 1 13 41 1 13 42 1 14 15 1 14 29 -2 15 16 1 15 30 1 17 18 2 17 21 1 18 19 1 18 20 1 19 31 1 19 32 1 20 34 1 20 35 1 22 23 1 23 24 1 24 26 2 25 37 1 25 38 1 25 39 1 26 40 1 28 43 1 28 44 1 28 45 1";
      document.JME.readMolecule(jme);
    }
  }
  
  function clearEditor() {
    if (document.JME) {
      document.JME.reset();
    }
  }

  function loadExampleSmiles() {
    const smilesString = '[H][C@]12SC/C(COC)=C(C(=O)O)\\N1C(=O)[C@H]2NC(=O)/C(=N\\OC)c3csc(N)n3';
    if (document.form1 && document.form1.smil) {
        document.form1.smil.value = smilesString;
    }
  }

  window.addEventListener('load', function() {
    setTimeout(function() {
      if (document.JME) {
        document.JME.repaint();
      }
    }, 100);
  });
</script>

<!--
======================================================================
 MAIN CONTENT FOR THE PAGE
======================================================================
-->
<div class="container my-5">
    <form name="form1" action="str_out.php" method="POST" enctype="multipart/form-data" class="border p-4 rounded-3 bg-light">

        <!-- ============================================= -->
        <!-- ===== NEW: DATABASE DOWNLOAD BUTTON ADDED ===== -->
        <!-- ============================================= -->
        <div class="d-flex justify-content-end mb-3">
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Download Database
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/anshu/arkbase/structure_search/database/database_drugs.csv" download="known_antibiotics_library.csv">
                            Known Antibiotics Library
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/anshu/arkbase/structure_search/database/bgc_known_structures.csv" download="bgc_metabolites_library.csv">
                            BGC Secondary Metabolites Library
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- ============================================= -->
        <!-- =========== END OF NEW SECTION ============ -->
        <!-- ============================================= -->

        <h2 class="text-center mb-4">Structure Search Form</h2>

        <p>Please choose only one option at a time:</p>
        <ol>
            <li>Draw using JSME editor</li>
            <li>Paste SMILES</li>
            <li>Upload SDF/MOL/MOL2 file</li>
        </ol>

        <hr>

        <div class="row g-4">
            <!-- Left Column: JSME Editor -->
            <div class="col-lg-6">
                <div class="text-center p-2 rounded" style="background-color: #D1FFA3;">
                    <b>Option 1. JSME (Draw Structures & Search)</b>
                </div>
                <div class="mt-3 text-center" style="min-height: 350px;">
                    <div code="JME.class" name="JME" archive="JME.jar" width="500" height="300" id="JME" style="margin: auto;">
                        You have to enable JavaScript in your browser to use JSME!
                    </div>
                    <input type="hidden" name="smi">
                    <div class="mt-3">
                        <input type="button" value="Clear Editor" onClick="clearEditor()" class="btn btn-outline-danger">
                    </div>
                </div>
            </div>

            <!-- Right Column: SMILES and File Upload -->
            <div class="col-lg-6">
                <div class="text-center p-2 rounded" style="background-color: #D1FFA3;">
                    <b>Option 2. Paste Structure in SMILES Format</b>
                </div>
                <textarea rows="8" name="smil" class="form-control mt-3"></textarea>
                <div class="text-start mt-2">
  		            <button type="button" onclick="loadExampleSmiles()" class="btn btn-outline-info">Load Example SMILES</button>
			    </div>

                <div class="text-center p-2 rounded mt-4" style="background-color: #D1FFA3;">
                    <b>Option 3. Upload File (MOL/SDF/MOL2)</b>
                </div>
                <input type="file" name="myfile" class="form-control mt-3">
                <div class="text-start mt-2">
                    <a href="example_structure.sdf" download="example_structure.sdf" class="btn-link">Download Example SDF File</a>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <!-- Database Selection and Submit Button -->
        <div class="text-center">
            <p><b>Please select the database to search against:</b></p>
            <select name="database_choice" class="form-select w-auto mx-auto mb-3" style="font-size:18px;">
                <option value="/var/www/html/anshu/arkbase/structure_search/database/database_drugs.csv" selected>Known Antibiotics</option>
                <option value="/var/www/html/anshu/arkbase/structure_search/database/bgc_known_structures.csv">BGC Secondary Metabolites</option>
            </select>

            <div class="mt-3">
                <button type="submit" name="search" onClick="getSmiles1()" class="btn btn-primary btn-lg">Similarity Structure Search</button>
                <button type="reset" onClick="clearEditor()" class="btn btn-outline-secondary btn-lg">Clear All</button>
            </div>
        </div>
    </form>
</div>

<?php
// Include the site footer
include '/var/www/html/anshu/arkbase/footer.php';
?>