<!DOCTYPE HTML>
<!-- Website Template by freewebsitetemplates.com -->
<?php include 'header.php'?>
<html>
<head>
<!--	<meta charset="UTF-8">-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>ARKbase</title>

		<script src="script.js?v=<?php echo time(); ?>"></script>

    <style>
      
        body {
            font-family: 'Roboto';
         
            color: white;
           
        }
           
        }


        ------
      

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .dashboard-container {
            max-width: 2400px;
            margin: 0 auto 40px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            color: #456997;
        }

        .header-section {
            background: #ffffff;
            color: #456997;
            padding: 40px;
            text-align: center;
        }

        .header-section h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 600;
            letter-spacing: 1px;
            color: #456997;
        }

        .header-section p {
            margin: 20px 0 0 0;
            font-size: 1.1em;
            opacity: 0.8;
            line-height: 1.6;
            color: #456997;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Main cards container matching the reference image */
        .cards-flow-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 60px 0;
            position: relative;
        }

        .flow-card {
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 50%, #2E5B8A 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 25px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.3);
        }

        .flow-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(74, 144, 226, 0.4);
        }

        /* First card - Reference Genome Annotations */
        .card-1 {
            width: 280px;
            height: 180px;
            background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            position: relative;
            z-index: 3;
        }

        /* Second card - Deep Annotations (center, largest) */
        .card-2 {
            width: 400px;
            height: 200px;
            background: linear-gradient(135deg, #357ABD 0%, #2E5B8A 100%);
            border-radius: 25px;
            margin: 0 -20px;
            position: relative;
            z-index: 2;
        }

        /* Third card - Comparative Analysis */
        .card-3 {
            width: 280px;
            height: 180px;
            background: linear-gradient(135deg, #2E5B8A 0%, #1E3A5F 100%);
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
           
            
        }

        .card-title {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 15px;
            color: white;
        }

        .card-subtitle {
            font-size: 0.95rem;
            line-height: 1.4;
            opacity: 0.9;
            margin-bottom: 20px;
            color: white;
        }

        .card-stat {
            font-size: 0.9rem;
            font-weight: 600;
            opacity: 0.9;
            color: white;
        }

        /* Plus icon for first card */
        .plus-icon {
            position: absolute;
            top: 50%;
            right: -15px;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: #357ABD;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }


		 /* Plus icon for first card */
        .plus-icon1 {
            position: absolute;
            top: 50%;
            right: -15px;
            transform: translateY(-50%);
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            color: #357ABD;
            z-index: 50;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
	

        @media (max-width: 768px) {
            .cards-flow-container {
                flex-direction: column;
                gap: 20px;
            }
            
            .flow-card {
                width: 100% !important;
                height: auto !important;
                border-radius: 15px !important;
                margin: 0 !important;
            }

       
    </style>
</head>
<body>

 <div class="container">
        <div class="dashboard-container">
            <div class="header-section">
                <h1>ARKbase - Antimicrobial Resistance Knowledgebase</h1>
                <p>ARKbase is a comprehensive resource dedicated to the bacterial antimicrobial resistance (AMR) landscape, focusing on WHO priority pathogens. It integrates high-quality bacterial genomes, proteomes, biosynthetic gene clusters (BGCs), AMR genes, drug targets, protein-protein and host-pathogen interactions, drug-target interactions, antibiotics data, antimicrobial susceptibility profiles, and gene expression datasets under antibiotic stress. ARKbase serves as a centralized platform towards understanding resistance mechanisms and pathogen biology for effective drug discovery.</p>
            </div>
        </div>

        <div class="cards-flow-container">
            <!-- First Card -->
            <div class="flow-card card-1" onclick="window.open('https://datascience.imtech.res.in/anshu/arkbase/ab_home.php', '_blank')">
                <div class="card-title">Reference Genome Annotations</div>
                <div class="card-stat">14 Priority Pathogens</div>
                <div class="plus-icon">+</div>
            </div>

            <!-- Second Card (Center) -->
            <div class="flow-card card-2" onclick="window.open('https://datascience.imtech.res.in/anshu/arkbase/index.php', '_blank')">
                <div class="card-title">Deep Annotations on Curated Genomes</div>
                <div class="card-subtitle">Deep pan annotations for over 3000 isolates</div>
                <div class="plus-icon1">&#8594;</div>
                <div class="connection-symbols">
                 
                </div>
            </div>

            <!-- Third Card -->
            <div class="flow-card card-3" onclick="window.open('https://datascience.imtech.res.in/anshu/arkbase/index.php', '_blank')">
                <div class="card-title">Comparative Analysis</div>
                <div class="card-subtitle">Perform comparative analysis of AMR Genes, Virulence Factors, Drug Targets, etc.</div>
                
            </div>
        </div>

        <div class="bottom-indicator">
            <div class="indicator-dot"></div>
        </div>
    </div>
    <script>
	 // Add click functionality and hover effects
        document.querySelectorAll('.flow-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add loading animation
        window.addEventListener('load', function() {
            const cards = document.querySelectorAll('.flow-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }

        // Add keyboard support
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const openModal = document.querySelector('.modal[style*="block"]');
                if (openModal) {
                    openModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            }
        });

        // Add smooth scrolling and parallax effect
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.header');
            const speed = scrolled * 0.5;
            parallax.style.transform = `translateY(${speed}px)`;
        });

        // Add intersection observer for card animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.8s ease forwards';
                }
            });
        });

        document.querySelectorAll('.card').forEach(card => {
            observer.observe(card);
        });

        // Add CSS for fade in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);

		
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
			setTimeout(autoSlideshow, 10000); // Change image every 5 seconds
		}

		// Start auto slideshow
		setTimeout(autoSlideshow, 10000);
        
    </script>
</body>
<?php include 'footer.php'?>
</html>