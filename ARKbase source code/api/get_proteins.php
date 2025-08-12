<?php
// File: api/get_proteins.php

// --- DATABASE CONNECTION DETAILS ---
$servername = "localhost";
$username = "arkbase";
$password = "data@2025";
$dbname = "arkbase";
$port = "3307";

// --- SET HEADERS ---
header('Content-Type: application/json');

// --- FUNCTION TO SEND A JSON ERROR AND EXIT ---
function json_error($message) {
    error_log($message);
    echo json_encode(['error' => 'An internal server error occurred.']);
    exit();
}

// --- CREATE DATABASE CONNECTION ---
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    json_error('Database connection failed: ' . $conn->connect_error);
}

// --- GET AND VALIDATE INPUT PARAMETERS ---
$category = isset($_GET['category']) ? $_GET['category'] : null;
$pathogen = isset($_GET['pathogen']) ? $_GET['pathogen'] : null;

if (!$category || !$pathogen) {
    echo json_encode([]);
    exit();
}

// --- PREPARE AND EXECUTE SQL QUERY ---
// **CHANGE**: Added `prot_id` to the SELECT statement
$sql = "SELECT unique_id, prot_id, protein_name FROM protein_search 
        WHERE pathogen_name = ? AND category = ? 
        ORDER BY protein_name ASC";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    json_error('Failed to prepare SQL statement: ' . $conn->error);
}

$db_category = strtoupper($category);
$stmt->bind_param("ss", $pathogen, $db_category);

if (!$stmt->execute()) {
    json_error('Failed to execute statement: ' . $stmt->error);
}
$result = $stmt->get_result();

// --- FETCH AND FORMAT RESULTS ---
$proteins = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proteins[] = [
            'id'      => $row['unique_id'],    // The internal ID for the form value
            'prot_id' => $row['prot_id'],      // **CHANGE**: The ID to display to the user
            'name'    => $row['protein_name']  // The protein's name/description
        ];
    }
}

// --- CLOSE RESOURCES AND OUTPUT JSON ---
$stmt->close();
$conn->close();

echo json_encode($proteins);

?>