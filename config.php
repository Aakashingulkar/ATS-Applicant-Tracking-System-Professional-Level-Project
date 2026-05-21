<?php
$conn = mysqli_connect("localhost","root","","ats_system");

if(!$conn){
    die("Connection failed: " . mysqli_connect_error());
}
session_start();
?>