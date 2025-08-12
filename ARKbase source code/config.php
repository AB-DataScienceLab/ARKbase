<?php
// Database configuration
$host = 'localhost';
$username = 'arkbase'; // Change this to your MySQL username
$password = 'data@2025'; // Change this to your MySQL password
$database = 'arkbase';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Function to safely escape data
function escape_data($data) {
    global $conn;
    return mysqli_real_escape_string($conn, $data);
}

// Function to get pathogen data by ID or get first record
function getPathogenData($strain_id = null) {
    global $conn;
    
    if ($strain_id) {
        $sql = "SELECT * FROM ab_data WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $strain_id);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // Get first record if no ID provided
        $sql = "SELECT * FROM ab_data ORDER BY id LIMIT 1";
        $result = $conn->query($sql);
    }
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Function to get all strains for dropdown
function getAllStrains() {
    global $conn;
    
    $sql = "SELECT id, strain_name FROM ab_data ORDER BY strain_name";
    $result = $conn->query($sql);
    
    $strains = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $strains[] = $row;
        }
    }
    return $strains;
}

// Function to get genome statistics
function getGenomeStats() {
    global $conn;
    
    $sql = "SELECT 
                COUNT(*) as total_genomes,
                AVG(genome_size_mb) as avg_genome_size,
                AVG(gc_content_percent) as avg_gc_content,
                AVG(total_genes) as avg_total_genes,
                MIN(genome_size_mb) as min_genome_size,
                MAX(genome_size_mb) as max_genome_size
            FROM ab_data";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// Function to get country distribution
function getCountryDistribution() {
    global $conn;
    
    $sql = "SELECT country_of_isolation, COUNT(*) as count 
            FROM ab_data 
            GROUP BY country_of_isolation 
            ORDER BY count DESC";
    
    $result = $conn->query($sql);
    
    $countries = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $countries[] = $row;
        }
    }
    return $countries;
}

// Function to get isolation source distribution
function getIsolationSourceDistribution() {
    global $conn;
    
    $sql = "SELECT isolation_source, COUNT(*) as count 
            FROM ab_data 
            GROUP BY isolation_source 
            ORDER BY count DESC";
    
    $result = $conn->query($sql);
    
    $sources = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $sources[] = $row;
        }
    }
    return $sources;
}
?>