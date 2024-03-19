$(document).ready(function() {
    $('#resetAndRerunBtn').click(function() {
        resetDatabaseAndRerunDownload();
    });
});

function resetDatabaseAndRerunDownload() {
    $.ajax({
        url: 'revert.php', // Adjust the URL to your PHP file handling the revert process
        type: 'POST',
        success: function(response) {
            // Alert the response from the revert.php script
            alert(response);
            },
        error: function(xhr, status, error) {
            alert("Error reverting changes: " + xhr.responseText);
        }
    });
}

