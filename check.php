<?php
$uname = $_SESSION['uname'];              
if ($uname == ""){header("Location:logout.php");}
?>