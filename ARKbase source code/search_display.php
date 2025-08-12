<!DOCTYPE html>
<?php include 'header.php'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Based Search</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .title {
            background-color: #fff2cc;
            padding: 10px;
            font-size: 1.5em;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        .search-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .organism-container {
            display: flex;
            gap: 20px;
        }
        .organism {
            text-align: center;
            cursor: pointer;
        }
        .organism img {
            width: 80px;
            height: auto;
            transition: transform 0.2s; /* Add a transition effect */
        }
        .organism img:hover {
            transform: scale(1.1); /* Scale up on hover */
        }
        .organism label {
            display: block;
            margin-top: 5px;
            font-size: 14px;
        }
        .search-bar {
            margin-top: 20px;
        }
        .search-bar input[type="text"] {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }
        .search-bar input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Browse Based Search</div>
        <div class="search-section">
            <div class="organism-container">
                <a href="search_form.php?organism=human" class="organism">
                    <img src="images/human.png" alt="Human">
                    <label>Human</label>
                </a>
                <a href="search_form.php?organism=mouse" class="organism">
                    <img src="images/mouse.png" alt="Mouse">
                    <label>Mouse</label>
                </a>
                <a href="search_form.php?organism=rat" class="organism">
                    <img src="images/rat.png" alt="Rat">
                    <label>Rat</label>
                </a>
            </div>
        </div>
        <div class="search-bar">
            <form action="search_form.php" method="GET">
                <input type="text" name="query" placeholder="Enter your search term" required>
                <input type="submit" value="Search">
            </form>
        </div>
    </div>
</body>
</html>