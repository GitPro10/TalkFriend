<?php


$host = "localhost";
$username = "root";
$password = "";
$database = "ChatWebApp";


$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
  echo("Connection Fail --> ". mysqli_connect_error());
}

?>