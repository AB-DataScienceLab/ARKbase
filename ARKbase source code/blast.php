<?php include 'header.php'; ?>

<!-- Custom CSS for the BLAST page styling -->
<style>
    .blast-form-section {
        border: 1px solid #ddd;
        border-radius: 0.25rem;
        padding: 2rem 1.5rem 1.5rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
    }
    .blast-form-section .section-title {
        position: absolute;
        top: -0.8em;
        left: 1rem;
        background-color: #fff;
        padding: 0 0.5rem;
        font-weight: 500;
        color: #495057;
    }
    .blast-form-section .form-label { font-weight: 500; }
</style>

<!-- Main Content -->
<main class="main-content">
    <div class="container">
        <h3 class="mb-4 mt-2" style="font-weight: 600;">
            <i class="bi bi-diagram-3" style="margin-right: 8px;"></i>Protein BLAST Search
        </h3>
        <p class="text-muted">Search a protein sequence against the custom AMR, Virulence Factor, or Drug Target databases.</p>

        <!-- The form action points to the new backend script -->
        <form action="blast_results.php" method="post" enctype="multipart/form-data">

            <!-- Query and Database Selection Section -->
            <div class="blast-form-section">
                <div class="section-title">Query and Database</div>
                
                <div class="row mb-3">
                    <label for="query-sequence" class="col-md-3 col-form-label">Enter Query Sequence(s):</label>
                    <div class="col-md-9">
                        <textarea id="query-sequence" name="query_sequence" class="form-control" rows="8" placeholder="Enter one or more sequences in FASTA format..."></textarea>
                        <!-- New: Link to load the example sequence -->
                        <div class="form-text">
                            Not sure what to enter? <a href="#" id="load-example-link">Load an example sequence.</a>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="upload-file" class="col-md-3 col-form-label">Or Upload a File:</label>
                    <div class="col-md-9">
                        <input type="file" id="upload-file" name="query_file" class="form-control">
                        <div class="form-text">Uploading a file will override any text entered above.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="blast-db" class="col-md-3 col-form-label">Select Target Database:</label>
                    <div class="col-md-9">
                        <select id="blast-db" name="blast_db" class="form-select" required>
                            <option value="" disabled selected>-- Please choose a database --</option>
                            <option value="dt">Drug Target (DT) Proteins</option>
                            <option value="amr">Antimicrobial Resistance (AMR) Proteins</option>
                            <option value="vf">Virulence Factor (VF) Proteins</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Search Parameters Section -->
            <div class="blast-form-section">
                <div class="section-title">Search Parameters</div>

                <div class="row mb-3">
                    <label for="expect-value" class="col-md-3 col-form-label">E-value Threshold:</label>
                    <div class="col-md-9">
                        <select id="expect-value" name="expect_value" class="form-select">
                            <option value="10">10</option>
                            <option value="1">1</option>
                            <option value="0.1">0.1</option>
                            <option value="0.01">0.01</option>
                            <option value="1e-5" selected>1e-5</option>
                            <option value="1e-10">1e-10</option>
                            <option value="1e-25">1e-25</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="matrix" class="col-md-3 col-form-label">Scoring Matrix:</label>
                    <div class="col-md-9">
                        <select id="matrix" name="matrix" class="form-select">
                            <option selected>BLOSUM62</option>
                            <option>BLOSUM45</option>
                            <option>BLOSUM80</option>
                            <option>PAM30</option>
                            <option>PAM70</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-md-3 col-form-label">Filtering:</label>
                    <div class="col-md-9">
                         <div class="form-check pt-2">
                            <input class="form-check-input" type="checkbox" id="low-complexity-filter" name="filter_low_complexity" checked>
                            <label class="form-check-label" for="low-complexity-filter">Filter for Low Complexity Regions</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5">BLAST</button>
                    <button type="reset" class="btn btn-secondary px-4">Reset Form</button>
                </div>
            </div>
        </form>
    </div>
</main>

<!-- New: JavaScript to handle loading the example sequence -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // The example FASTA sequence, stored in a template literal for easy multi-line formatting
    const exampleSequence = `>NP_310165.2 | multiple antibiotic resistance transcriptional regulator | e_coli
MSRRNTDAITIHSILDWIEDNLESPLSLEKVSERSGYSKWHLQRMFKKETGHSLGQYIRSRKMTEIAQKLKESNEPILYL
AERYGFESQQTLTRTFKNYFDVPPHKYRMTNMQGESRFLHPLNHYNS`;

    // Get the link and the textarea elements by their IDs
    const loadExampleLink = document.getElementById('load-example-link');
    const querySequenceTextarea = document.getElementById('query-sequence');

    // Ensure the link exists before adding an event listener
    if (loadExampleLink) {
        loadExampleLink.addEventListener('click', function(event) {
            // Prevent the default link action (e.g., navigating to '#')
            event.preventDefault();

            // Set the textarea's value to our example sequence
            querySequenceTextarea.value = exampleSequence;
        });
    }
});
</script>


<?php include 'footer.php'; ?>