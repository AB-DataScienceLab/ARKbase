<!DOCTYPE HTML>
<?php include'header.php';?>

<html>

<head>
	<meta charset="UTF-8">
	<title>Team - NRatlas</title>
<!--	<link rel="stylesheet" href="css/style.css" type="text/css">-->
	<!-- Using Bootstrap for a responsive grid layout -->
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    /* Custom styles to enhance the page */
    body {
          background: linear-gradient(to top right, #e9ecef, #ffffff);
    }

/*    .team-section {*/
/*        padding: 60px 0;*/
/*    }*/

    .section-title {
        text-align: center;
        margin-bottom: 50px;
        color: #10428d;
        font-weight: bold;
    }

    .contact-card {
        text-align: center;
        padding: 30px 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%; /* Ensures all cards in a row have the same height */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .contact-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .contact-card img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%; /* Makes images circular */
        border: 4px solid #e9ecef;
        margin-bottom: 20px;
    }

    .contact-card h3 {
        font-size: 1.25rem;
        color: #343a40;
        margin-bottom: 5px;
    }

    .contact-card h3 a {
        color: inherit;
        text-decoration: none;
    }
     .contact-card h3 a:hover {
        color: #10428d;
    }

    .contact-card .title {
        font-size: 1rem;
        color: #10428d;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .contact-card .affiliation {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0;
    }

    .map-container {
        padding: 60px 0;
        text-align: center;
    }

    .map-container iframe {
        max-width: 800px;
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

</style>
<body>

<div class="container team-section">

    <h1 class="section-title">Our Team</h1>

    <!-- A single row to hold all team members. It will wrap automatically. -->
    <div class="row justify-content-center">

        <!-- Member 1 -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/ankita.jpg" alt="Ankita">
                <h3><a href="mailto:pawan@imtech.res.in">Ms. Ankita Das</a></h3>
                <p class="title">Project Associate I</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 2 -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/dr_bhupender.png" alt="Dr. Bhupender Singh">
                <h3><a href="#">Dr. Bhupender Singh</a></h3>
                <p class="title">Project Scientist I</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>
        
        <!-- Member 3 -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/harsh.jpeg" alt="Mr. Harsh Bajetha">
                <h3><a href="mailto:#">Mr. Harsh Bajetha</a></h3>
                <p class="title">Project Associate I | Developer</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 4 -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/jasleen.jpg" alt="Ms. Jasleen Kaur">
                <h3><a href="mailto:#">Ms. Jasleen Kaur</a></h3>
                <p class="title">Project Candidate</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

         <!-- Member 5 -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/mayur.jpg" alt="Mr. Mayur Zarkar">
                <h3><a href="mailto:#">Mr. Mayur Zarkar</a></h3>
                <p class="title">Project Associate I | Developer</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 5 -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/pranav.jpg" alt="Dr. Pranavathiyani Gnanasekar">
                <h3><a href="mailto:#">Dr. Pranavathiyani Gnanasekar</a></h3>
                <p class="title">Project Scientist I</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 6 -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/raghav.jpg" alt="Mr. Raghav Sankhdher">
                <h3><a href="mailto:#">Mr. Raghav Sankhdher</a></h3>
                <p class="title">Senior Project Associate | Developer</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 7 (Placeholder) -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/rupali.jpg" alt="Ms. Rupali Aggrawal">
                <h3><a href="#">Ms. Rupali Aggrawal</a></h3>
                <p class="title">Dissertation Student</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 8 (Placeholder) -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/shivani.jpg" alt="Ms. Shivani Seth">
                <h3><a href="#">Ms. Shivani Seth</a></h3>
                <p class="title">Junior Research Fellow</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 9 (Placeholder) -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/spd.jpg" alt="Ms. Shweta Pandey">
                <h3><a href="#">Ms. Shweta Pandey</a></h3>
                <p class="title">Senior Research Fellow</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 10 (Placeholder) -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/simran.jpg" alt="Ms. Simran Gambhir">
                <h3><a href="#">Ms. Simran Gambhir</a></h3>
                <p class="title">Project Associate I</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 11 (Placeholder) -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/suhani .jpg" alt="Ms. Suhani Dange">
                <h3><a href="#">Ms. Suhani Dange</a></h3>
                <p class="title">Project Associate I</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 12 (Placeholder) -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/upasana.jpg" alt="Ms. Upasana Maity">
                <h3><a href="#">Ms. Upasana Maity</a></h3>
                <p class="title">Project Associate II</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

        <!-- Member 13 (Placeholder) -->
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="contact-card">
                <img src="photos/dr_anshu.png" alt="Dr. Anshu Bhardwaj">
                <h3><a href="#">Dr. Anshu Bhardwaj</a></h3>
                <p class="title">Principal Scientist</p>
                <p class="affiliation">CSIR-IMTech, Chandigarh</p>
            </div>
        </div>

    </div> <!-- End of the single row -->
</div>

<!-- Google Maps Section -->
<div class="container-fluid map-container">
    <h2 class="section-title">Find Us At CSIR-IMTech, Chandigarh</h2>
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3429.0075085464823!2d76.73144557527894!3d30.746290584977775!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390fede2a3e1498f%3A0xe9e249a6b5b8e967!2sCSIR%20%E2%80%93%20Institute%20Of%20Microbial%20Technology%20(IMTECH)!5e0!3m2!1sen!2sin!4v1719998734071!5m2!1sen!2sin" 
        height="450" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
</div>

</body>

<?php include'footer.php';?>
</html>