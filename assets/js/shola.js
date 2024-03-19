$(document).ready(function() {
    $('#downloadResultsBtn').click(function() {    
        downloadFilesBatch();
    });
});

function downloadFilesBatch() {
    alert("Download in progress. Please wait patiently as files are being downloaded in batches.");
    $.ajax({
        url: 'download.php',
        type: 'POST',
        success: function(response) {
            // Handle success response if needed
            console.log(response);
        },
        error: function(xhr, status, error) {
            // This function will be called if there's an error during the AJAX request.
            console.error(xhr.responseText);
        }
    });
}
