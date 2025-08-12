<?php include 'header.php'; ?>
<style>

  body {
            font-family: 'Arial';
          
        }
  /* Slideshow container */
		.slideshow-container {
			max-width: 960px;
			position: relative;
			margin: auto;
		}

		/* Hide the images by default */
		.mySlides {
			display: none;
		}

		/* Next & previous buttons */
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

		/* Position the "next button" to the right */
		.next {
			right: 0;
			border-radius: 3px 0 0 3px;
		}

		/* On hover, add a black background color with a little bit see-through */
		.prev:hover, .next:hover {
			background-color: rgba(0,0,0,0.8);
		}

		/* The dots/bullets/indicators */
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

		.active, .dot:hover {
			background-color: #10428d;
		}

		/* Fading animation */
		.fade {
			animation-name: fade;
			animation-duration: 11.5s;
		}

		@keyframes fade {
			from {opacity: .4}
			to {opacity: 1}
		}

		/* Reduce the max-width of the cards and ensure they are centered in their columns */
.feature-card {
    max-width: 420px; /* Adjust this value to make cards wider or narrower */
    margin-left: auto;
    margin-right: auto;
    text-align: left; /* Reset text alignment for flex layout */
    font-size: 15px;
}

/* Style the icon container */
.feature-icon {
    font-size: 3rem; /* Make the icon a bit larger */
    line-height: 1; /* Ensure it doesn't add extra vertical space */

}

/* Ensure headings and paragraphs inside the card have no top margin */
.feature-card-content h4,
.feature-card-content p {
    margin-top: 0;
     padding: 0; /* You can adjust the value as needed */
     margin-down: 0;
}

		
</style>
    <!-- Main Content -->
    <main class="main-content">
    <br>
        <div class="container">
            
	<!-- Replacing the section div with slideshow container -->
	<div class="slideshow-container">
		<!-- Full-width images with number and caption text -->
		
		<div class="mySlides fade1">
			<img src="images/ga/1-ARKbase.png" style="width:100%">
		</div>
		
		<div class="mySlides fade1">
				<img src="images/ga/2-ARKbase.png" style="width:100%">
		</div>

		<div class="mySlides fade1">
				<img src="images/ga/3-ARKbase.png" style="width:100%">
		</div>

		<div class="mySlides fade1">
				<img src="images/ga/4-ARKbase.png" style="width:100%">
		</div>
		
		<div class="mySlides fade1">
				<img src="images/ga/5-ARKbase.png" style="width:100%">
		</div>

		<div class="mySlides fade1">
			<img src="images/ga/6-ARKbase.jpeg" style="width:100%">
		</div>
		<div class="mySlides fade1">
			<img src="images/ga/7-ARKbase.jpg" style="width:100%">
		</div>
		<div class="mySlides fade1">
			<img src="images/ga/8-ARKBase.png" style="width:100%">
		</div>
		<div class="mySlides fade1">
			<img src="images/ga/9-ARKbase.png" style="width:100%">
		</div>
		<div class="mySlides fade1">
			<img src="images/ga/10-ARKbase.png" style="width:100%">
		</div>
		
		<!-- Next and previous buttons -->
		<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
		<a class="next" onclick="plusSlides(1)">&#10095;</a>
	</div>
	<br>

	<!-- The dots/circles -->
	<div style="text-align:center">
		<span class="dot" onclick="currentSlide(1)"></span>
		<span class="dot" onclick="currentSlide(2)"></span>
		<span class="dot" onclick="currentSlide(3)"></span>
		<span class="dot" onclick="currentSlide(4)"></span>
		<span class="dot" onclick="currentSlide(5)"></span>
		<span class="dot" onclick="currentSlide(6)"></span>
		<span class="dot" onclick="currentSlide(7)"></span>
		<span class="dot" onclick="currentSlide(8)"></span>
		<span class="dot" onclick="currentSlide(9)"></span>
		<span class="dot" onclick="currentSlide(10)"></span>
		
	</div>

         <!-- Feature Cards -->
<div class="row feature-cards justify-content-center"> <!-- Added justify-content-center to center the columns -->
    <!-- Card 1 -->
    <div class="col-lg-4 col-md-6 mb-4"> <!-- The column class provides spacing -->
        <a href="home.php" class="feature-card-link">
            <!-- Added d-flex and align-items-start to arrange content side-by-side -->
            <div class="feature-card fade-in-up d-flex align-items-start">
                <!-- Added margin-end (me-3) for spacing -->
                <div class="feature-icon me-3">
                    <i class="bi bi-database"></i>
                </div>
                <!-- Added a wrapper for all text content -->
                <div class="feature-card-content">
                    <h4>Reference Genome Annotations</h4>
                    <p>14 Priority Pathogens</p>
                    <h3>Database Module</h3>
<!--                    <p class="small">Comprehensive genomic annotations for WHO priority bacterial pathogens with detailed functional characterization.</p>-->
                </div>
            </div>
        </a>
    </div>
    <!-- Card 2 -->
    <div class="col-lg-4 col-md-6 mb-4">
        <a href="genome_Acinetobacter_baumannii.php" class="feature-card-link">
            <div class="feature-card fade-in-up d-flex align-items-start" style="animation-delay: 0.2s;">
                <div class="feature-icon me-3">
                    <i class="bi bi-search"></i>
                </div>
                <div class="feature-card-content">
                    <h4>Deep Annotations on Curated Genomes</h4>
                    <p>Deep pan annotations for over 3000 isolates</p>
                    <h3>Database Module</h3>
<!--                    <p class="small">Extensive functional annotations across thousands of bacterial isolates for comprehensive analysis.</p>-->
                </div>
            </div>
        </a>
    </div>
    <!-- Card 3 -->
    <div class="col-lg-4 col-md-6 mb-4">
        <a href="comparative_analysis.php" class="feature-card-link">
            <div class="feature-card fade-in-up d-flex align-items-start" style="animation-delay: 0.4s;">
                <div class="feature-icon me-3">
                     <!-- Corrected typo from bdi to bi -->
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="feature-card-content">
                    <h4>Comparative Analysis</h4>
                    <p>Perform comparative analysis of AMR Genes, Virulence Factors, Drug Targets, etc.</p>
                    <h3>Comparative Genomics Module</h3>
                    
<!--                    <p class="small">Advanced tools for comparing resistance mechanisms across different bacterial species.</p>-->
                </div>
            </div>
        </a>
    </div>
</div>

<!--
==================================================
END OF CORRECTED SECTION
==================================================
-->

            <div class="row">
                <div class="col-12">
                    <div class="arkbase-description fade-in-up">
                     <p class="lead" style="text-align: justify;">
                           ARKbase is an integrated, curated, value-added knowledge-base for AMR, with focus on WHO Bacterial Priority Pathogens. ARKbase is a dedicated AMR resource with a potential to provide novel insights towards expanding the drug-target space.
                        </p>
                        <p class="lead" style="text-align: justify;">
                           ARKbase is the largest dedicated resource for WHO bacterial priority pathogens, integrating high-quality genomes, proteomes, AMR genes, and comprehensive datasets including biosynthetic gene clusters, drug targets, antibiotic profiles, gene expression data and machine learning models. This centralized platform assists in understanding resistance mechanisms and supports targeted drug discovery for AMR.
                        </p>
                    </div>
                </div>
            </div>
        </div>
				
    </main>
    <script>
    
    let slideIndex = 1;
		showSlides(slideIndex);

		// Next/previous controls
		function plusSlides(n) {
			showSlides(slideIndex += n);
		}

		// Thumbnail image controls
		function currentSlide(n) {
			showSlides(slideIndex = n);
		}

		function showSlides(n) {
			let i;
			let slides = document.getElementsByClassName("mySlides");
			let dots = document.getElementsByClassName("dot");
			if (n > slides.length) {slideIndex = 1}
			if (n < 1) {slideIndex = slides.length}
			for (i = 0; i < slides.length; i++) {
				slides[i].style.display = "none";
			}
			for (i = 0; i < dots.length; i++) {
				dots[i].className = dots[i].className.replace(" active", "");
			}
			slides[slideIndex-1].style.display = "block";
			dots[slideIndex-1].className += " active";
		}

		// Auto slideshow
		function autoSlideshow() {
			plusSlides(1);
			setTimeout(autoSlideshow, 10000); // Change image every 10 seconds
		}

		// Start auto slideshow
		setTimeout(autoSlideshow, 10000);
    </script>

<?php include 'footer.php'; ?>