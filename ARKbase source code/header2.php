<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
         /* Header styles */
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      padding: 10px 20px;
      background-color: #f9f9f9;
      border-bottom: 1px solid #ccc;
    }

	nav {
    padding: 5px;
    background-color: #e6f0ff; /* Light blue background */
    text-align: center;
    font-size: 20px;
    position: relative;
}
	
    .logo {
      flex: 1 1 100px;
      display: flex;
      justify-content: flex-start;
      align-items: center;
    }

    .logo img {
      height: 80px;
      max-width: 100%;
    }

    .header-text {
      flex: 2 1 300px;
      text-align: center;
    }

    .header-text h1 {
      margin: 2px;
      font-size: 3.5vw;
      color: #456997;
      font-weight: bold;
    }

    .header-text h2 {
      margin: 2px;
      font-size: 1.8vw;
      color: #456997;
      font-weight: bold;
    }

    .header-text p {
      margin: 0;
      font-size: 14px;
      color: #10428d;
    }

    .search-bar {
      flex: 1 1 200px;
      display: flex;
      justify-content: flex-end;
    }

    .search-bar input {
      padding: 8px;
      font-size: 16px;
      width: 100%;
      max-width: 250px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: center;
      }

      .logo, .header-text, .search-bar {
        flex: 1 1 100%;
        text-align: center;
        justify-content: center;
        margin-bottom: 10px;
      }

      .header-text h1 {
        font-size: 32px;
      }

      .header-text h2 {
        font-size: 18px;
      }

      .search-bar {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
    <header>
    <div class="logo">
      <!-- <img src="logo_images/logo_final.png" alt="Left Logo"> -->
    </div>

    <div class="header-text">
      <h1>ARKbase</h1>
      <h2>Antimicrobial Resistance Knowledgebase</h2>
    </div>

    <div class="search-bar">
      <input type="text" placeholder="Search...">
    </div>
  </header>
    <nav>

    
       <div class="dropdown">
    <a href="index.php">Home</a>
</div>
<div class="dropdown">
            <a href="index.php">Pan AMR</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">Pan Virulence</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">Host-Pathogen Interactions</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">Drug Targets</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">Drug Target Interactions</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">BGCs</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">ML Models</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">Search by Annotations</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">Search by Sequences</a>
            <div class="dropdown-content">
                <a href="index.php">BLASTN</a>
                <a href="index.php">BLASTP</a>
                <a href="index.php">BLASTNX</a>
            </div>
        </div>

        <div class="dropdown">
            <a href="index.php">Search by Category</a>
            <div class="dropdown-content">
                <a href="index.php">PP1</a>
                <a href="index.php">PP2</a>
                <a href="index.php">PP3</a>
                <a href="index.php">Expands</a>
            </div>
        </div>
    </nav>
</body>
</html>
