<?php

require_once('connection.php');
require_once('cann.php');
require_once('check.php');

// Ensure database connection is established
if (!$conn) {
    die("Database connection failed: " . print_r(sqlsrv_errors(), true));
}

// SQL query to revert changes made during the download process
$sqlRevert = "UPDATE [Transcript].[dbo].[Transcript_order] SET transdown = NULL WHERE transdown = 1 AND CONVERT(DATE, downloaded_date) = CONVERT(DATE, GETDATE())";

// Execute the SQL query
$queryRevert = sqlsrv_query($conn, $sqlRevert);

if ($queryRevert === false) {
    // If the query fails, return an error response
    die("Error reverting changes: " . print_r(sqlsrv_errors(), true));
} else {
    // If the query succeeds, return a success response
    echo "Changes reverted successfully, you can now attempt to generate result download link again.";
}

// Close database connection
sqlsrv_close($conn);
?>
