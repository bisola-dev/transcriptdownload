
<?php
require_once('cann.php');
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user inputs
    $ustazname = $_POST["ustazname"];
    $ustaz = $_POST["ustaz"];

    // Perform basic validation (you can add more validation as needed)
  if (!empty($ustazname) && !empty($ustaz)){
        $user='anty';
        $token='antymi';
        $xml = '<?xml version="1.0" encoding="utf-8"?>
        <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
        <soap12:Body>
          <validate_erp_login xmlns="https://portal.yabatech.edu.ng/">
            <erpuser>'.$ustazname.'</erpuser>
            <erp_password>'.$ustaz.'</erp_password>
            <userid>'.$user.'</userid>
            <usertoken>'.$token.'</usertoken>
          </validate_erp_login>
        </soap12:Body>
      </soap12:Envelope>';
//echo $xml;


   // The URL for the SOAP service
   $url = 'https://portal.yabatech.edu.ng/paymentservice/yctoutservice.asmx?op=validate_erp_login';

   // Initialize cURL session
   $curl = curl_init($url);
   curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/soap+xml"));
   curl_setopt($curl, CURLOPT_POST, true);
   curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
   curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

   // Execute cURL session and get the response
   $result = curl_exec($curl);

   // To load get your response in JSON format the below code is required
   $cleanData = preg_replace("/(<\/?)(\w+):([^>]*>)/", '$1$2$3' , $result); 
   $convertToString=simplexml_load_string($cleanData);
   $encodingToJson=json_encode($convertToString); 
   //$responseArray=json_decode($json, true); 
   if (curl_errno($curl)) { 
   throw newException(curl_error($curl)); 
   } 
   curl_close($curl); //echo $json; 
   $decodeJson=json_decode($encodingToJson); 
   $resultValue =($decodeJson->soapBody->validate_erp_loginResponse->validate_erp_loginResult);
   //echo $resultValue;

   $rinu2 = "SELECT * FROM vw_Transcript_erpuser WHERE erpuserid=$resultValue";
   $params = array();
   $opts = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
   
   // Execute the SQL query
   $er = sqlsrv_query($conn, $rinu2, $params, $opts);
   $twin = sqlsrv_query($conn, $rinu2);
   $row_count = sqlsrv_num_rows($er);
   
   // Check if there are rows in the result
   if ($row_count > 0) {
       // Loop through the rows
       while ($rowz = sqlsrv_fetch_array($er, SQLSRV_FETCH_ASSOC)) {
           $erpuserid = $rowz['erpuserid'];
           $uname = $rowz['uname'];
       // echo $erpuserid;
       // Check the result
       if ($resultValue == $erpuserid) {
            // Set the session variable    
           $uname= $rowz['uname']; 
           $uname= $_SESSION['uname'] = $uname;   
          // Successful login 
           echo '<script type="text/javascript">
               alert("Login successful!");
               window.location.href="transcriptcheck.php";
           </script>';

       } 
    }
  }
  else{
    // Failed login
    echo '<script type="text/javascript">
        alert("Login failed. Please input your login details correctly.");
    </script>';
}

}
   // Close cURL session
   curl_close($curl);
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transcript Download Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f4f4f4; /* Set a light background color */
        }

        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Full height of the viewport */
        }

        .combined-container {
            background-color: #006400; /* Set bottle green background color for the combined container */
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 100%; /* Set the width to take up the available space */
            max-width: 800px; /* Set a maximum width to avoid excessive stretching on larger screens */
        }

        .login-form-container,
        .school-info-container {
            padding: 20px;
            background-color: #fff; /* Set a white background color for each container */
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Add margin to the bottom of each container */
            border: 2px solid #006400; /* Set the border color */
        }

        .btn-custom {
            background-color: #28a745; /* Set a green color for the button */
            color: #fff;
        }

        .school-info-container img {
            padding: 30px;
            max-width: 100%;
            border-radius: 8px;
            display: block;
            margin: auto; /* Center the image horizontally */
            margin-bottom: 20px; /* Add margin to the bottom of the image */
        }

        .school-name {
            font-size: 24px;
            margin-bottom: 20px; /* Add margin to the bottom of the school name */
        }
    </style>
</head>
<body class="login-page">
    <div class="container">
        <div class="row">
            <div class="col-md-12 login-container">
                <div class="combined-container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="school-info-container">
                                <h2 class="school-name">Yaba College of Technology</h2>
                                <img src="assets/img/yabalogo.jpg" alt="Yaba College of Technology Logo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="login-form-container">
                                <h2 class="text-center">Transcript Download Login</h2>
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="username">Username:</label>
                                        <input type="text" class="form-control" id="username" name="ustazname" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password:</label>
                                        <input type="password" class="form-control" id="password" name="ustaz" required>
                                    </div>
                                    <button type="submit" class="btn btn-custom btn-block">Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
