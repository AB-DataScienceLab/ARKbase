<?php

// The absolute path on the server to the directory you want to zip.
// IMPORTANT: Make sure this path is correct for your server environment.
$rootPath = '/var/www/html/anshu/arkbase/BIG_SCAPE_result';

// The name for the final downloaded zip file.
$zipFileName = 'BiG_SCAPE_Results.zip';

// Check if the directory exists and is readable.
if (!is_dir($rootPath) || !is_readable($rootPath)) {
    // If not, stop the script and show an error.
    die("Error: The directory could not be found or is not readable.");
}

// Use a temporary file for the zip archive to avoid conflicts.
$zipTempFile = tempnam(sys_get_temp_dir(), 'zip');

// Initialize the ZipArchive class.
$zip = new ZipArchive();
if ($zip->open($zipTempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
    die("Error: Cannot create zip archive.");
}

// Create a recursive iterator to find all files and directories.
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    // Skip directories (they will be added automatically).
    if (!$file->isDir()) {
        // Get the real and relative paths for the file.
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add the file to the zip archive.
        $zip->addFile($filePath, $relativePath);
    }
}

// Finalize the zip file.
$zip->close();

// If the zip was created successfully, send it to the browser.
if (file_exists($zipTempFile)) {
    // Set HTTP headers to force a download prompt.
    header('Content-Description: File Transfer');
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($zipTempFile));
    
    // Read the file and send its contents to the output buffer.
    readfile($zipTempFile);
    
    // Delete the temporary zip file from the server.
    unlink($zipTempFile);
    
    exit;
} else {
    die("Error: The zip file could not be created.");
}

?>