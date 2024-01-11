

function showalertpop() {
  alert("Please note that once the download is complete, the Transcript pool will be empty until new data is available.");
}

// Function to update information
function updateInformation() {
      // Your Ajax script to update information
      $.ajax({
          url: 'update_download_count.php',
          method: 'POST',
          data: {
              // Your data to send for updating
          },
          success: function (response) {
              console.log('Information updated successfully:', response);
              // Reload the page or perform other actions as needed
              location.reload()
             
          },
          error: function (error) {
              console.error('Error updating information:', error);

              // Display an alert for the error
              alert('Error updating information. Please try again later.');
          }
      });
  } 


// Function to initiate the download process
function downloadZip() {
  // Redirect to the PHP script that handles the download
  window.location.href = 'download.php';

}
