<?php

    $servername = "localhost";
    $username = "root";
    $password = "";
    // Create connection
    $conn = mysqli_connect($servername, $username, $password);
    $conn_create = mysqli_connect($servername, $username, $password,'ML_Naives_Bayes');
    mysqli_set_charset($conn, 'UTF8');
    mysqli_set_charset($conn_create, 'UTF8');
    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


?>