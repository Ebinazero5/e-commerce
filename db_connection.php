<?php
$connection = mysqli_connect("localhost:4306", "root", "", "e_com");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
