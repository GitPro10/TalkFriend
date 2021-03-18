<?php

$host = "fdb28.awardspace.net";
$username = "3653255_chatwebapp";
$password = "Kayes100";
$database = "3653255_chatwebapp";


$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
  echo("Connection Fail --> ". mysqli_connect_error());
}

?>