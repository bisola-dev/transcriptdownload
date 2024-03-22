<?php
require_once('connection.php');
require_once('cann.php');
require_once('check.php');

// Ensure database connection is established
if (!$conn) {
    die("Database connection failed: " . print_r(sqlsrv_errors(), true));
}

// Check if the reset date is provided
if (isset($_POST['pickDate'])) {
    // Get the selected date for reset
    $pickDate = $_POST['pickDate'];

    // SQL query to check if any downloads were made on the selected date
    $sqlCheckDownloads = "SELECT COUNT(*) AS downloadCount FROM [Transcript].[dbo].[Transcript_order] WHERE CONVERT(DATE, downloaded_date) = ?";
    $params = array($pickDate);

    // Prepare and execute the SQL query to check downloads
    $stmtCheckDownloads = sqlsrv_query($conn, $sqlCheckDownloads, $params);

    if ($stmtCheckDownloads === false) {
        // If query execution fails, return an error response
        die("Error checking downloads: " . print_r(sqlsrv_errors(), true));
    }

    // Fetch the result to check if any downloads exist on the selected date
    $rowCheckDownloads = sqlsrv_fetch_array($stmtCheckDownloads, SQLSRV_FETCH_ASSOC);
    $downloadCount = $rowCheckDownloads['downloadCount'];

    // If no downloads were made on the selected date, return an error response
    if ($downloadCount == 0) {
        die("No downloads made on the selected date.");
    }

    // SQL query to fetch data for the selected date
    $sqlFetchData = "SELECT finalresult, ROW_NUMBER() OVER (ORDER BY finalresult) AS RowNum FROM [Transcript].[dbo].[Transcript_order] WHERE status = 1 AND transdown = 1 AND CONVERT(DATE, downloaded_date) = ?";

    // Variable to store batch size
    $batchSize = 50;

    // Calculate total number of batches
    $totalBatches = ceil($downloadCount / $batchSize);

    // Loop through batches
    for ($i = 1; $i <= $totalBatches; $i++) {
        // Calculate row numbers for the current batch
        $startRow = ($i - 1) * $batchSize + 1;
        $endRow = $i * $batchSize;

        // Prepare and execute the SQL query to fetch data for current batch
        $sqlFetchDataBatch = "SELECT finalresult FROM ($sqlFetchData) AS Data WHERE RowNum BETWEEN ? AND ?";
        $paramsFetchDataBatch = array($pickDate, $startRow, $endRow);
        $stmtFetchDataBatch = sqlsrv_query($conn, $sqlFetchDataBatch, $paramsFetchDataBatch);

        if ($stmtFetchDataBatch === false) {
            // If query execution fails, return an error response
            die("Error fetching data for batch $i: " . print_r(sqlsrv_errors(), true));
        }

        // Initialize an array to store file paths for current batch
        $filePaths = array();

        // Process fetched data and collect file paths
        while ($rowFetchData = sqlsrv_fetch_array($stmtFetchDataBatch, SQLSRV_FETCH_ASSOC)) {
            // Assuming 'finalresult' is the column containing the file names
            $filePath = 'https://portal.yabatech.edu.ng/transcript/lastresult/' . $rowFetchData['finalresult'];
            $filePaths[] = $filePath;
        }

        // Close the statement for fetching data
        sqlsrv_free_stmt($stmtFetchDataBatch);

        // Create a zip file for the current batch
        $zip = new ZipArchive();
        $zipPath = 'Results_' . $pickDate . '_Batch_' . $i . '.zip';

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            die("Failed to create the zip file for batch $i.");
        }

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Adjust timeout as needed

        // Alert the user before download begins
        if ($i === 1) {
            echo "<script>alert('Downloading files. Please wait...');</script>"; // Alert to notify user of ongoing download
        }

        foreach ($filePaths as $filePath) {
            $fileName = basename($filePath);

            // Set URL to fetch
            curl_setopt($ch, CURLOPT_URL, $filePath);

            // Execute cURL request
            $fileContent = curl_exec($ch);

            if ($fileContent !== false) {
                $zip->addFromString($fileName, $fileContent);
            } else {
                // Log or display error message for failed file
                echo "Failed to add $fileName to the zip file.<br>";
            }
        }

        // Close cURL session
        curl_close($ch);

        // Close the zip file
        $zip->close();

        // Generate download link for the zip file
        $downloadLink = "<a href='$zipPath'>Download Batch $i</a>";

        // Output the download link
        echo $downloadLink . "<br>";
    }
    echo '<br><br><a href="javascript:history.back()">Back</a>'; // Add back button
    // Free statement resources
    sqlsrv_free_stmt($stmtCheckDownloads);
} else {
    // If the reset date is not provided, return an error response
    die("Error: No selected date provided.");
}

// Close database connection
sqlsrv_close($conn);


// Function to delete old zip files
function deleteOldZipFiles()
{
    $directory = '.'; // Directory where the zip files are stored
    $files = scandir($directory);

    foreach ($files as $file) {
        $filePath = $directory . '/' . $file;
        if (is_file($filePath) && pathinfo($file, PATHINFO_EXTENSION) === 'zip' && time() - filemtime($filePath) >= 24 * 3600) {
            unlink($filePath); // Delete the file if it's older than 24 hours and is a zip file
        }
    }
}

// Call the function to delete old zip files
deleteOldZipFiles();

?>
