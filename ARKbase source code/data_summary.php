<?php include 'header.php'; ?>

<style>
    /* --- FONT --- */
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
    body {
        font-family: 'Roboto', sans-serif;
    }

    .main-content {
        background-color: #f8f9fa;
    }
    .plot-card {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        background-color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        display: flex;
        flex-direction: column;
        height: 100%;
        min-height: 450px; /* ADDED: Ensures a minimum height for better visual balance */
    }
    .plot-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .plot-card-header {
        background-color: #e9ecef;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }
    .plot-card-header h5 {
        margin: 0;
        font-size: 1.1rem; /* ADJUSTED: Slightly larger font for the bigger card */
        font-weight: 600;
        color: #343a40;
    }
    .plot-image-container {
        padding: 1.5rem; /* ADJUSTED: More padding for a cleaner look in a larger card */
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .plot-image {
        max-width: 100%;
        height: auto;
        border-radius: 0.25rem;
    }
    .show-more-container {
        margin-top: 1rem;
        margin-bottom: 2rem;
    }
</style>

<main class="main-content">
    <div class="container py-5">
        <!-- =================================================================== -->
        <!-- SECTION 1: COG FUNCTIONAL CATEGORIES                              -->
        <!-- =================================================================== -->
        <div class="row">
            <div class="col-12 mb-4">
                <p class="h6 fw-bold">COG Functional Categories of BPPL 2024</p>
                <p>
                    Click any card to view more details about the pathogen genome and interactive COG Visualization.
                </p>
            </div>
        </div>
        <div class="row">
            <?php
            // --- MASTER DATA ARRAY (used by all sections) ---
            $cog_plot_path = '/anshu/arkbase/COG_PLOTS/';
            $pathogen_data = [
                'COG_AB.png'  => ['title' => '<em>Acinetobacter baumannii</em>', 'link' => 'ab1.php'],
                'COG_KP.png'  => ['title' => '<em>Klebsiella pneumoniae</em>', 'link' => 'kp.php'],
                'COG_EC.png'  => ['title' => '<em>Escherichia coli</em>', 'link' => 'Ecoli.php'],
                'COG_SF.png'  => ['title' => '<em>Shigella flexneri</em>', 'link' => 'Shigella_flexneri.php'],
                'COG_SS.png'  => ['title' => '<em>Shigella sonnei</em>', 'link' => 'Shigella_sonnei.php'],
                'COG_EF.png'  => ['title' => '<em>Enterococcus faecium</em>', 'link' => 'ef.php'],
                'COG_PA.png'  => ['title' => '<em>Pseudomonas aeruginosa</em>', 'link' => 'pa.php'],
                'COG_SE.png'  => ['title' => '<em>Salmonella enterica</em>', 'link' => 'se.php'],
                'COG_NG.png'  => ['title' => '<em>Neisseria gonorrhoeae</em>', 'link' => 'ng.php'],
                'COG_SAU.png' => ['title' => '<em>Staphylococcus aureus</em>', 'link' => 'Staphylococcus_aureus.php'],
                'COG_SP.png'  => ['title' => '<em>Streptococcus pyogenes</em>', 'link' => 'Streptococcus_pyogenes.php'],
                'COG_SPN.png' => ['title' => '<em>Streptococcus pneumoniae</em>', 'link' => 'Streptococcus_pneumoniae.php'],
                'COG_HI.png'  => ['title' => '<em>Haemophilus influenzae</em>', 'link' => 'hi.php'],
                'COG_SA.png'  => ['title' => '<em>Streptococcus agalactiae</em>', 'link' => 'sa.php'],
            ];
            $ordered_filenames = [
                'COG_AB.png', 'COG_KP.png', 'COG_EC.png', 'COG_SF.png', 'COG_SS.png',
                'COG_EF.png', 'COG_PA.png', 'COG_SE.png', 'COG_NG.png', 'COG_SAU.png',
                'COG_SP.png', 'COG_SPN.png', 'COG_HI.png', 'COG_SA.png',
            ];
            
            $index = 0;
            // Display first 2 cards before the button (changed from 3)
            $cards_before_collapse = 2; 

            foreach ($ordered_filenames as $filename) {
                if ($index === $cards_before_collapse) {
                    ?>
                    <div class="col-12 text-center show-more-container">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#morePlots" aria-expanded="false" aria-controls="morePlots" id="togglePlotsBtn">
                            Show More
                        </button>
                    </div>
                    <div class="collapse" id="morePlots"><div class="row">
                    <?php
                }
                $data = $pathogen_data[$filename];
                ?>
                <!-- **** CHANGE HERE **** -->
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="plot-card">
                        <a href="<?php echo $data['link']; ?>" class="text-decoration-none">
                            <div class="plot-card-header"><h5 class="text-dark"><?php echo $data['title']; ?></h5></div>
                            <div class="plot-image-container"><img src="<?php echo $cog_plot_path . $filename; ?>" class="plot-image img-fluid" alt="COG Plot for <?php echo strip_tags($data['title']); ?>" loading="lazy"></div>
                        </a>
                    </div>
                </div>
                <?php
                $index++;
            }
            if ($index > $cards_before_collapse) { echo '</div></div>'; }
            ?>
        </div>

        <!-- =================================================================== -->
        <!-- SECTION 2: MECHANISMS OF RESISTANCE (MoR)                         -->
        <!-- =================================================================== -->
        <div class="row mt-5">
            <div class="col-12 mb-4">
                <p class="h6 fw-bold">Total Number of Genes Involved in Various MoRs at Pan-level</p>
            </div>
        </div>
        <div class="row">
            <?php
            $amr_plot_path = '/anshu/arkbase/pan_amr_figs/';
            $amr_filenames = ['ab.png', 'kp.png', 'ec.png'];
            foreach ($amr_filenames as $amr_filename) {
                $base_name = strtoupper(basename($amr_filename, '.png'));
                $cog_key   = 'COG_' . $base_name . '.png';
                $data = $pathogen_data[$cog_key] ?? ['title' => 'Unknown', 'link' => '#'];
                ?>
                <!-- **** CHANGE HERE **** -->
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="plot-card">
                        <a href="<?php echo $data['link']; ?>" class="text-decoration-none">
                            <div class="plot-card-header"><h5 class="text-dark"><?php echo $data['title']; ?></h5></div>
                            <div class="plot-image-container"><img src="<?php echo $amr_plot_path . $amr_filename; ?>" class="plot-image img-fluid" alt="AMR Plot for <?php echo strip_tags($data['title']); ?>" loading="lazy"></div>
                        </a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>

        <!-- =================================================================== -->
        <!-- SECTION 3: COMBINED REFERENCE AMR PLOT                            -->
        <!-- =================================================================== -->
        <div class="row mt-5 justify-content-center">
            <div class="col-12 mb-4">
                <p class="h6 fw-bold">Total number of AMR genes involved in different resistance mechanisms for Reference genomes of BPPLs</p>
            </div>
            <div class="col-lg-8 col-md-10">
                 <div class="plot-card">
                     <a href="https://datascience.imtech.res.in/anshu/arkbase/home.php" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                         <div class="plot-card-header">
                             <h5 class="text-dark">Combined Reference Genome AMR</h5>
                         </div>
                         <div class="plot-image-container p-3">
                             <img src="/anshu/arkbase/pan_amr_figs/combined_amr_ref.jpg" class="plot-image img-fluid" alt="Combined AMR data for reference genomes" loading="lazy">
                         </div>
                     </a>
                 </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- SECTION 4: VIRULENCE FACTORS                                      -->
        <!-- =================================================================== -->
        <div class="row mt-5">
            <div class="col-12 mb-4">
                <p class="h6 fw-bold">Total number of virulence genes and functional categories</p>
            </div>
        </div>
        <div class="row">
            <?php
            $vf_plot_path = '/anshu/arkbase/images/vf_plots/';
            $vf_filenames = ['ab_vf.png', 'kp_vf.png', 'ec_vf.png'];
            foreach ($vf_filenames as $vf_filename) {
                $base_name = str_replace('_vf.png', '', $vf_filename);
                $cog_key   = 'COG_' . strtoupper($base_name) . '.png';
                $data = $pathogen_data[$cog_key] ?? ['title' => 'Unknown', 'link' => '#'];
                ?>
                <!-- **** CHANGE HERE **** -->
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="plot-card">
                        <a href="<?php echo $data['link']; ?>" class="text-decoration-none">
                            <div class="plot-card-header"><h5 class="text-dark"><?php echo $data['title']; ?></h5></div>
                            <div class="plot-image-container"><img src="<?php echo $vf_plot_path . $vf_filename; ?>" class="plot-image img-fluid" alt="Virulence Factor Plot for <?php echo strip_tags($data['title']); ?>" loading="lazy"></div>
                        </a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <!-- =================================================================== -->
        <!-- SECTION 5: COMBINED REFERENCE VF PLOT                             -->
        <!-- =================================================================== -->
        <div class="row mt-5 justify-content-center">
            <div class="col-12 mb-4">
                <p class="h6 fw-bold">Total number of virulence genes involved in different resistance mechanisms for Reference genomes of BPPLs</p>
            </div>
            <div class="col-lg-8 col-md-10">
                 <div class="plot-card">
                     <a href="https://datascience.imtech.res.in/anshu/arkbase/home.php" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                         <div class="plot-card-header">
                             <h5 class="text-dark">Combined Reference Genome Virulence</h5>
                         </div>
                         <div class="plot-image-container p-3">
                             <img src="/anshu/arkbase/images/vf_plots/vf_all_refs.png" class="plot-image img-fluid" alt="Combined Virulence Factor data for reference genomes" loading="lazy">
                         </div>
                     </a>
                 </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- SECTION 6: AMR HEATMAPS (NEW)                                     -->
        <!-- =================================================================== -->
        <div class="row mt-5">
            <div class="col-12 mb-4">
                <p class="h6 fw-bold">Heatmap showing presence and absence of AMR genes for critical pathogens at Pan-genome level</p>
            </div>
        </div>
        <div class="row">
            <?php
            $res_prof_path = '/anshu/arkbase/images/res_prof_plots/';
            $res_prof_filenames = ['ab_res.jpg', 'kp_res.jpg', 'ec_res.jpg'];

            foreach ($res_prof_filenames as $res_prof_filename) {
                $base_name = str_replace('_res.jpg', '', $res_prof_filename);
                $cog_key   = 'COG_' . strtoupper($base_name) . '.png';
                
                $data = $pathogen_data[$cog_key] ?? ['title' => 'Unknown', 'link' => '#'];
                ?>
                <!-- **** CHANGE HERE **** -->
                <div class="col-lg-6 col-md-6 mb-4">
                    <div class="plot-card">
                        <a href="<?php echo $data['link']; ?>" class="text-decoration-none">
                            <div class="plot-card-header"><h5 class="text-dark"><?php echo $data['title']; ?></h5></div>
                            <div class="plot-image-container"><img src="<?php echo $res_prof_path . $res_prof_filename; ?>" class="plot-image img-fluid" alt="AMR Heatmap for <?php echo strip_tags($data['title']); ?>" loading="lazy"></div>
                        </a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div> <!-- /row -->


    </div> <!-- /container -->

    
</main>

<script>
    const morePlots = document.getElementById('morePlots');
    const toggleBtn = document.getElementById('togglePlotsBtn');
    if (morePlots && toggleBtn) {
        morePlots.addEventListener('show.bs.collapse', event => { toggleBtn.textContent = 'Show Less'; });
        morePlots.addEventListener('hide.bs.collapse', event => { toggleBtn.textContent = 'Show More'; });
    }
</script>

<?php include 'footer.php'; ?>