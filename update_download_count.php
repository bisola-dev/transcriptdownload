<?php
require_once('connection.php');
require_once('cann.php');
require_once('check.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the current maximum value of download_count
    $maxQuery = "SELECT MAX(download) AS max_download_count FROM [Transcript].[dbo].[Transcript_order]";
    $maxResult = sqlsrv_query($conn, $maxQuery);
    $maxRow = sqlsrv_fetch_array($maxResult);
    $currentMax = $maxRow['max_download_count'];

    
    $newDownloadCount = $currentMax + 1;

    // Update the download count
    $updateQuery = "UPDATE [Transcript].[dbo].[Transcript_order] SET download = $newDownloadCount WHERE status=1 AND download IS NULL";
    $updateResult = sqlsrv_query($conn, $updateQuery);
    if ($updateResult === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Return a success message (optional)
    echo 'Download count updated successfully';
} else {
    // Handle the case when action is not set or not recognized
    echo 'Invalid action';
}
?>
