<?php

require_once('connection.php');
require_once('cann.php');
require_once('check.php');

$sql = "SELECT finalresult FROM [Transcript].[dbo].[Transcript_order] where status=1 AND download IS NULL";
$query = sqlsrv_query($conn, $sql);

// Check if there are rows in the result set
if (sqlsrv_has_rows($query)) {
    // Create a zip file
    $zip = new ZipArchive();
    $zipPath = 'Results.zip';

    if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
        while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
            $filePath = 'https://portal.yabatech.edu.ng/transcript/lastresult/' . $row['finalresult'];
            
            // Use cURL to fetch file content
            $ch = curl_init($filePath);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $fileContent = curl_exec($ch);

            if ($fileContent === false) {
                echo 'cURL Error: ' . curl_error($ch);
            } else {
                // Add file to the zip
                $zip->addFromString($row['finalresult'], $fileContent);
            }

            curl_close($ch);
        }

        $zip->close();

        // Set response headers
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="Results.zip"');
        header('Content-Length: ' . filesize($zipPath));

        // Read and output the zip file
        readfile($zipPath);

        // Delete the temporary zip file
        unlink($zipPath);
    } else {
        echo 'Failed to create the zip file.';
    }
} else {
    // Display an alert message using JavaScript
    echo '<script>alert("No data available for download."); window.history.back();</script>';
}
?>
