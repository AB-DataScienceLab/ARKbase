<!DOCTYPE html>
<?php include 'header.php'; ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Statistics</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">

    <style>
     
<style>
    .container {
        text-align: center;
        padding: 20px;
    }

    .container h3 {
        margin-bottom: 20px;
        font-size: 24px;
        color: #333;
    }

    .image-wrapper {
        display: flex; /* Enables flexbox */
        justify-content: center; /* Centers images horizontally */
        align-items: center; /* Aligns images vertically */
        gap: 20px; /* Space between images */
        flex-wrap: wrap; /* Allows images to wrap to the next line if needed */
    }

    .image-wrapper img {
        height: 400px; /* Standardized height */
        width: auto; /* Maintain aspect ratio */
        max-width: 90%; /* Prevent images from exceeding container width */
        border: 2px solid #ccc;
        border-radius: 8px;
    }

    .image-wrapper1 img {
        height: 700px; /* Standardized height */
        width: auto; /* Maintain aspect ratio */
        max-width: 90%; /* Prevent images from exceeding container width */
        border: 2px solid #ccc;
        border-radius: 8px;
        margin-left: 100px;
        padding: 20px;
    }

    .image-wrapper img:hover {
        border-color: #0073e6;
        transition: border-color 0.3s ease;
    }

    .image-wrapper1 img:hover {
        border-color: #0073e6;
        transition: border-color 0.3s ease;
    }
</style>
    </style>
</head>
<body>
<br><br>
    <div class="container">
<!--        <h3>Human PTM Distribution</h3>-->
        <div class="image-wrapper1">
            <img src="Statistics/chr_op.600dpi2.jpg" alt="Chromosome Distribution">
            </div>
        <div class="image-wrapper">    
            <img src="Statistics/Human_PTMdistribution3.png" alt="PTM Distribution">
            <img src="Statistics/Organism3.png" alt="Organism Statistics">
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
