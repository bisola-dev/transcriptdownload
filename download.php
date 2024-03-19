
<?php
require_once('connection.php');
require_once('cann.php');
require_once('check.php');

$maxFilesPerBatch = 50; // Maximum number of files to download per batch

// Check if the session variable for file count exists and initialize if necessary
if (!isset($_SESSION['fileCount'])) {
    $_SESSION['fileCount'] = 0; // Initialize the file count if it doesn't exist
}

date_default_timezone_set('Africa/Lagos');

// Ensure database connection is established
if (!$conn) {
    die("Database connection failed: " . print_r(sqlsrv_errors(), true));
}

$sql_count = "SELECT COUNT(finalresult) as count FROM [Transcript].[dbo].[Transcript_order] WHERE status = 1 AND transdown IS NULL AND download IS NULL";
$getFilesCount = sqlsrv_query($conn, $sql_count);
if ($getFilesCount === false) {
    die("Error retrieving file count: " . print_r(sqlsrv_errors(), true));
}
$row = sqlsrv_fetch_array($getFilesCount, SQLSRV_FETCH_ASSOC);
$count = $row['count'];

$downloadLoop = ceil($count / $maxFilesPerBatch);

if ($downloadLoop == 0) {
    die("No files to download.");
}

for ($i = 0; $i < $downloadLoop; $i++) {
    // Create a new zip file for each batch
    $zip = new ZipArchive();
    $zipPath = 'Results_' . date('Ymd_H-i') . '_Batch_' . ($i + 1) . '.zip'; // Include date and batch number in zip filename

    // Open the zip file
    if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
        die("Failed to create the zip file for batch " . ($i + 1) . ".");
    }

    // Fetch files from the database for the current batch
    $sql = "SELECT TOP $maxFilesPerBatch finalresult FROM [Transcript].[dbo].[Transcript_order] WHERE status = 1 AND transdown IS NULL AND download IS NULL";
    $query = sqlsrv_query($conn, $sql);

    if ($query === false) {
        die("Error executing SQL query: " . print_r(sqlsrv_errors(), true));
    }

    // Initialize file count
    $fileCount = 0;

    while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        $filePath = 'https://portal.yabatech.edu.ng/transcript/lastresult/' . $row['finalresult'];
        $sqlUpdate = "UPDATE [Transcript].[dbo].[Transcript_order] SET transdown = 1, downloaded_date = GETDATE() WHERE finalresult = ?";
        $params = array($row['finalresult']);
        $queryUpdate = sqlsrv_query($conn, $sqlUpdate, $params);

        if ($queryUpdate === false) {
            die("Error updating database: " . print_r(sqlsrv_errors(), true));
        }

        // Use cURL to fetch file content
        $ch = curl_init($filePath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set a timeout for cURL request (in seconds)
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Adjust as needed

        $fileContent = curl_exec($ch);

        if ($fileContent === false) {
            die('Failed to download file: ' . curl_error($ch));
        }

        // Add file to the zip
        $zip->addFromString($row['finalresult'], $fileContent);
        $fileCount++; // Increment file count

        curl_close($ch);
    }

    // Close the zip file for the current batch
    $zip->close();

    // Generate a download link for the zip file and output it as HTML
    $downloadLink = '<a href="' . $zipPath . '"> Download Batch ' . ($i + 1) . '</a>';
    echo $downloadLink . "<br>";

    // Update session variable with new file count
    $_SESSION['fileCount'] += $fileCount;

    
}
echo '<br><br><a href="javascript:history.back()">Back</a>'; // Add back button
// Close database connection
sqlsrv_close($conn);

// Function to delete zip files older than 24 hours
function deleteOldZipFiles()
{
    $directory = '.'; // Directory where the files are stored
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