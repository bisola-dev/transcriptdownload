<?php
$serverName        = "213.171.204.36";
$connectionOptions = array(
    "Database" => "EBPORTAL",
    "Uid"      => "Bisola_new",
    "PWD"      => "eiu947qwbjgf@#455",
    "TrustServerCertificate"=> 'true',
    "Encrypt"=>'Yes',
);


//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die(FormatErrors(sqlsrv_errors()));
}


?>
