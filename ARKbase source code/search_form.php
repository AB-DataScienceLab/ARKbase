<!DOCTYPE html>
<?php
// Best practice for error reporting in development. For production, log errors instead of displaying them.
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'header.php';
include 'conn.php'; //--REVIEW: Perfect. Ensures the mysqli connection is ready.

// --- REVIEW: This function is perfectly converted to mysqli_* ---
function getFilterOptions($conn, $column) {
    // The backticks are a great practice, protecting against reserved SQL keywords.
    $sql = "SELECT DISTINCT `$column` FROM ProteinTable5 WHERE `$column` IS NOT NULL AND `$column` != '' ORDER BY `$column` ASC";
    
    $result = mysqli_query($conn, $sql);
    $options = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $options[] = $row[$column];
        }
        //--BEST PRACTICE: Excellent. Always free the result set memory when you're done.
        mysqli_free_result($result);
    } else {
        //--BEST PRACTICE: Excellent error logging.
        error_log("getFilterOptions failed for column `$column`: " . mysqli_error($conn));
    }
    
    return $options;
}

// Get the filter options - no changes needed
$organismOptions = getFilterOptions($conn, 'Organism');
$ptmOptions = getFilterOptions($conn, 'PTM');
$nrncOptions = getFilterOptions($conn, 'Member');
$modifierOptions = getFilterOptions($conn, 'Modifier_1');

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Search</title>
    <!-- Your CSS and JS includes are perfect, no changes needed -->
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    
    <style>
        /* Center the form */
        .search-container {
            margin: 20px auto;
            max-width: 900px;
            padding: 15px;
        }

        .search-box {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        /* Filter container */
        .filter-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-label {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Search input styling */
        input[type="text"] {
            width: 350px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Select styling */
        select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            width: 180px;
        }

        /* Button styling */
        .btn {
            padding: 10px 20px;
            background-color: #008000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .reset-btn {
            background-color: #FF0000;
        }

        .reset-btn:hover {
            background-color: #5a6268;
        }

        /* Table styling */
        .results {
            margin: 10px auto;
            width: 98%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #10428d;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            table-layout: auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
            word-wrap: break-word;
            white-space: normal;
            font-size: 14px;
            max-width: 300px;
            vertical-align: top; /* Add this to align content to top */
    line-height: 1.4; /* Add better line spacing */
        }

        table th {
            background-color: #10428d;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
            position: sticky;
            top: 0;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Checkbox container styling */
        .checkbox-container {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 8px;
            width: 180px;
            background-color: white;
            text-align: left;
        }

        .checkbox-option {
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .checkbox-option input {
            margin-right: 5px;
        }

        .checkbox-option label {
            font-size: 14px;
            cursor: pointer;
        }

        /* Responsive styling for smaller devices */
        @media (max-width: 768px) {
            .search-box, .filter-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            input[type="text"], select {
                width: 100%;
            }
            
            .filter-group {
                width: 100%;
            }

            .checkbox-container {
                width: 100%;
            }
        }

        /* Container styling */
        .container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Title styling */
        .title {
            font-size: 34px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #10428d;
        }
        
        /* Highlight search results */
        .highlight {
            background-color: #ffff99;
            font-weight: bold;
        }

        /* New styles for scroll container */
        .scroll-container {
            position: relative;
            width: 98%;
            margin: 10px auto;
            overflow: hidden;
        }

        .top-scroll {
            overflow-x: auto;
            overflow-y: hidden;
            width: 100%;
            height: 20px; /* Make scrollbar visible */
    border: 1px solid #ddd;
    border-bottom: none;
        }

        .top-scroll-content {
            width: 4500px; /* Adjust based on your table width */
            height: 1px;
         
        }

        .results {
            overflow-x: auto;
            width: 100%;
        }

        /* Synchronize scrolling */
        .results::-webkit-scrollbar,
        .top-scroll::-webkit-scrollbar {
            height: 12px;
    width: 12px;
        }

        .results::-webkit-scrollbar-track,
        .top-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }

        .results::-webkit-scrollbar-thumb,
        .top-scroll::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 6px;
        }

        .results::-webkit-scrollbar-thumb:hover,
        .top-scroll::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Enhanced autocomplete styling */
        .ui-autocomplete {
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0;
            border: 1px solid #ccc;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            z-index: 9999;
            font-size: 14px;
        }

        .ui-autocomplete .ui-menu-item {
            padding: 0;
            margin: 0;
        }

        .ui-autocomplete .ui-menu-item div {
            padding: 8px 10px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }

        .ui-autocomplete .ui-menu-item:last-child div {
            border-bottom: none;
        }

        .ui-autocomplete .ui-menu-item div:hover,
        .ui-autocomplete .ui-menu-item div.ui-state-active {
            background-color: #f0f0f0;
        }

        .ui-autocomplete .ui-menu-item div strong {
            font-weight: bold;
            color: #007bff;
        }

        /* Table Container Styling */
        .table-container {
            width: 100%;
            overflow-x: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #ffffff;
        }

        /* Table Styling */
        .results table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: Arial, sans-serif;
        }

        /* Table Header Styling */
        .results table thead {
            background-color: #2c3e50;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }

        .results table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #10428d;
        }

        /* Table Body Styling */
        .results table tbody tr {
            transition: background-color 0.2s ease;
        }

        .results table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .results table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Alternating Row Colors */
        .results table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        /* Highlight Styling */
        .results .highlight {
            background-color: #ffd700;
            font-weight: bold;
            padding: 2px 4px;
            border-radius: 3px;
        }

        /* Scrollbar Styling */
        .results {
            max-height: 500px;
            overflow-y: auto;
        }

        .results::-webkit-scrollbar {
            width: 10px;
        }

        .results::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .results::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 5px;
        }

        .results::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .results table {
                font-size: 12px;
            }
            
            .results table th,
            .results table td {
                padding: 4px 5px;
            }
        }

        /* Result Count Styling */
        .result-count {
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
        }
   .additional-columns-group {
    display: none;
}

.show-additional-columns {
    display: flex !important;
}



    </style>
</head>
<body>

<div class="container">
    <div class="title">Simple Search</div>

    <?php 
    function escapeLike($keyword) {
        return str_replace(['%', '_'], ['\\%', '\\_'], $keyword);
    }

    // --- REVIEW: This is a textbook-perfect implementation of prepared statements! ---
    // This function is secure, robust, and well-written.
    function searchDatabase($conn, $keyword = '', $organisms = ['all'], $ptm = 'all', $nrnc = 'all', $modifier = 'all') {
        $sql = "SELECT * FROM ProteinTable5 WHERE 1=1"; // Starting with 1=1 makes appending AND clauses easy.
        
        $params = []; // Will hold the values to bind
        $types = "";  // Will hold the type string for bind_param (e.g., "sss")

        // Keyword search across multiple columns
        if (!empty($keyword)) {
            //--NOTE: We do not use escapeLike here. The wildcard is added, but the core keyword is sent separately.
            $wildcardKeyword = '%' . $keyword . '%';
            $searchColumns = [ /* ... your list of columns ... */ 'Sub_family', 'Group_name', 'NRNC_symbol', 'Gene', 'Member', 'Uniprot_ID', 'TrEMBL_ID','Organism', 'Isoforms', 'Uniprot_ID_Isoforms', 'PTM', 'SITE_OF_PTM', 'SITE_OF_PTM_Uniprot','Domain', 'Cell_line', 'Sequence', 'Modifier_1', 'Modifier_1_Uniprot', 'Modifier_2','Modifier_2_Uniprot', 'Effect', 'Reference', 'Pathway_name', 'Molecular', 'Biological','Cellular', 'LBD', 'MOTIF', 'ZINC', 'Source'];
            $keywordConditions = [];
            foreach ($searchColumns as $col) {
                $keywordConditions[] = "`$col` LIKE ?"; // Use placeholder
                $types .= 's';
                $params[] = $wildcardKeyword; // Add value to be bound
            }
            $sql .= " AND (" . implode(" OR ", $keywordConditions) . ")";
        }
        
        // Organism filter (handles multiple selections)
        if (!empty($organisms) && !in_array('all', $organisms)) {
            //--BEST PRACTICE: This is the correct way to handle a dynamic IN clause with prepared statements.
            $placeholders = implode(',', array_fill(0, count($organisms), '?'));
            $sql .= " AND `Organism` IN ($placeholders)";
            foreach ($organisms as $org) {
                $types .= 's';
                $params[] = $org;
            }
        }
        
        // Dropdown filters
        if (!empty($ptm) && $ptm != 'all') {
            $sql .= " AND `PTM` = ?";
            $types .= 's';
            $params[] = $ptm;
        }
        if (!empty($nrnc) && $nrnc != 'all') {
            $sql .= " AND `Member` = ?";
            $types .= 's';
            $params[] = $nrnc;
        }
        if (!empty($modifier) && $modifier != 'all') {
            $sql .= " AND `Modifier_1` = ?";
            $types .= 's';
            $params[] = $modifier;
        }
        
        $sql .= " ORDER BY `PTM` ASC, `Uniprot_ID` ASC"; // Added a secondary sort for consistency

        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
             error_log("Prepare failed: " . mysqli_error($conn));
             die("Error: Could not prepare the search query.");
        }

        //--BEST PRACTICE: The splat operator (...) cleanly passes the array of params to bind_param. Excellent!
        if (!empty($params)) {
             mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            error_log("Query execution failed: " . mysqli_error($conn));
            return false;
        }
        
        // We don't close the statement here because we need the $result object.
        // It will be freed later after we're done looping through it.
        return $result;
    }

    // Your HTML search form is well-structured. No changes needed.
    // ... [your form HTML] ...

    // --- REVIEW: Form handling logic is also perfectly converted ---
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        //--NOTE: No need for any manual escaping (like mysql_real_escape_string).
        // The prepared statement handles all security aspects.
        $keyword = trim($_POST['keyword'] ?? '');
        $organisms = $_POST['organism'] ?? ['all'];
        $ptm = $_POST['ptm'] ?? 'all';
        $nrnc = $_POST['nrnc'] ?? 'all';
        $modifier = $_POST['modifier'] ?? 'all';

        // Call the new, secure searchDatabase function
        $result = searchDatabase($conn, $keyword, $organisms, $ptm, $nrnc, $modifier);

        if ($result) {
            $num_rows = mysqli_num_rows($result); // Correct mysqli_* function
            echo "<div class='result-count'>{$num_rows} Results Found</div>";

            if ($num_rows > 0) {
                echo "<div class='table-container'><div class='results'><table border='1' cellpadding='10'>";
                // ... [your table header] ...

                function highlightKeyword($text, $keyword) {
                    //--BEST PRACTICE: Using htmlspecialchars first before highlighting is safer.
                    $safeText = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
                    if (empty($keyword) || empty($safeText)) {
                        return $safeText;
                    }
                    // Use preg_quote to escape special regex characters in the keyword
                    return preg_replace('/' . preg_quote($keyword, '/') . '/i', '<span class="highlight">$0</span>', $safeText);
                }

                $serialNo = 1;
                // Correct mysqli_* function
                while ($row = mysqli_fetch_assoc($result)) {
                    //--REVIEW: Your output structure is fine. The key is that you are escaping output.
                    // Using a helper function like highlightKeyword which incorporates htmlspecialchars is great.
                    echo "<tr><td>" . $serialNo++ . "</td>";
                    // Loop through all columns to simplify the output and avoid repetition
                    foreach ($row as $key => $value) {
                         // Skip the first column if you're handling Serial No. separately
                        if ($key == array_key_first($row) && $serialNo == 2) continue;

                        if ($key != 'ID') { // Assuming 'ID' is your primary key and you don't want to show it.
                            echo "<td>" . (!empty($value) ? highlightKeyword($value, $keyword) : "NA") . "</td>";
                        }
                    }
                    echo "</tr>";
                }
                echo "</table></div></div>";
                
                //--BEST PRACTICE: Free the result memory. Perfect.
                mysqli_free_result($result);

            } else {
                echo "<p><b><h2>No results found for your search criteria.</h2></b></p>";
            }
        }
    }
    ?>
</div>
<!-- JavaScript for autocomplete and checkbox handling -->
<script>
 $(document).ready(function() {
    // Reset Button functionality - Fixed version
    $('#resetForm').click(function() {
        // Clear text input
        $('#searchInput').val('');

        // Reset all select dropdowns to 'all' option
        $('select').prop('selectedIndex', 0);

        // Reset organism checkboxes - check only "All Organisms"
        $('.checkbox-container input[type="checkbox"]').prop('checked', false);
        $('#organism-all').prop('checked', true);

        // Reset additional columns
        $('input[name="additional_columns[]"]').prop('checked', false);

        // **ADD THIS LINE TO HIDE THE ADDITIONAL COLUMNS GROUP ON RESET**
        $('.additional-columns-group').removeClass('show-additional-columns');

        // Submit the form with a reset parameter
        $('<input>').attr({
            type: 'hidden',
            name: 'reset',
            value: 'true'
        }).appendTo('#searchForm');

        // Submit the form to reload the page with default values
        $('#searchForm').submit();

        return false;
    });


    // Add autocomplete to search input
    $("#searchInput").autocomplete({
        source: "get_suggestions.php",
        minLength: 5,
        select: function(event, ui) {
            $("#searchInput").val(ui.item.value);
            // **ADD THIS LINE TO SHOW THE ADDITIONAL COLUMNS GROUP WHEN A SEARCH IS PERFORMED**
            $('.additional-columns-group').addClass('show-additional-columns');
            $("#searchForm").submit();
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        // Highlight the matching text in suggestions
        var term = this.term.trim();
        var regex = new RegExp("(" + $.ui.autocomplete.escapeRegex(term) + ")", "gi");
        var highlightedText = item.label.replace(regex, "<strong>$1</strong>");
        return $("<li></li>")
            .data("item.autocomplete", item)
            .append("<div>" + highlightedText + "</div>")
            .appendTo(ul);
    };
});

  // Add scrollbar synchronization
    function syncScrollbars() {
        const topScroll = $('.top-scroll');
        const results = $('.results');
        
        if (topScroll.length && results.length) {
            // Set the width of top scroll content to match table width
            const tableWidth = results.find('table').outerWidth();
            $('.top-scroll-content').width(tableWidth);
            
            // Sync scrolling from top scrollbar to results
            topScroll.on('scroll', function() {
                if (!$(this).data('scrolling')) {
                    results.data('scrolling', true);
                    results.scrollLeft($(this).scrollLeft());
                    results.data('scrolling', false);
                }
            });
            
            // Sync scrolling from results to top scrollbar
            results.on('scroll', function() {
                if (!$(this).data('scrolling')) {
                    topScroll.data('scrolling', true);
                    topScroll.scrollLeft($(this).scrollLeft());
                    topScroll.data('scrolling', false);
                }
            });
        }
    }
    
    // Initialize scrollbar sync after form submission
    if ($('.results table').length > 0) {
        syncScrollbars();
    }
    
    // Re-sync scrollbars on window resize
    $(window).resize(function() {
        syncScrollbars();
    });

    // Organism checkbox logic
function handleOrganismCheckboxes() {
    const allOrganismCheckbox = $('#organism-all');
    const otherOrganismCheckboxes = $('input[name="organism[]"]:not(#organism-all)');
    
    // When "All Organisms" is clicked
    allOrganismCheckbox.change(function() {
        if ($(this).is(':checked')) {
            // Uncheck all other organism checkboxes
            otherOrganismCheckboxes.prop('checked', false);
        }
    });
    
    // When any specific organism is clicked
    otherOrganismCheckboxes.change(function() {
        if ($(this).is(':checked')) {
            // Uncheck "All Organisms" when any specific organism is selected
            allOrganismCheckbox.prop('checked', false);
        } else {
            // If no specific organisms are selected, check "All Organisms"
            if (otherOrganismCheckboxes.filter(':checked').length === 0) {
                allOrganismCheckbox.prop('checked', true);
            }
        }
    });
    
    // Initialize: If no specific organisms are selected, ensure "All Organisms" is checked
    if (otherOrganismCheckboxes.filter(':checked').length === 0 && !allOrganismCheckbox.is(':checked')) {
        allOrganismCheckbox.prop('checked', true);
    }
}

// Call the function to initialize checkbox behavior
handleOrganismCheckboxes();

// Update the reset button functionality to properly handle organism checkboxes
$('#resetForm').click(function() {
    // Clear text input
    $('#searchInput').val('');

    // Reset all select dropdowns to 'all' option
    $('select').prop('selectedIndex', 0);

    // Reset organism checkboxes - check only "All Organisms"
    $('input[name="organism[]"]').prop('checked', false);
    $('#organism-all').prop('checked', true);

    // Reset additional columns
    $('input[name="additional_columns[]"]').prop('checked', false);

    // Hide the additional columns group on reset
    $('.additional-columns-group').removeClass('show-additional-columns');

    // Submit the form with a reset parameter
    $('<input>').attr({
        type: 'hidden',
        name: 'reset',
        value: 'true'
    }).appendTo('#searchForm');

    // Submit the form to reload the page with default values
    $('#searchForm').submit();

    return false;
});

</script>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
 <?php include 'footer.php'; ?>

</body>
</html>