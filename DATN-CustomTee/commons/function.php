<?php
function connDBAss() {
    $host="mysql:host=localhost;dbname=;charset=utf8";
    $user="root";
    $pass="";
    try {
        $conn = new PDO($host, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $th) {
        echo $th->getMessage();
    }
}