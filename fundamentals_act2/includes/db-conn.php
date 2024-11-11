<?php
$sname = 'localhost';
$uname= 'root';
$password = "";
$db_name = "university_db3";

$conn = mysqli_connect($sname, $uname, $password, $db_name);

    if(!$conn){
        echo "Connection Failed";
    }else{
        "Connection Success!";
    }
?>


