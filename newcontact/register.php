<?php
$conn = new mysqli("localhost", "root", "", "newcontact");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = $_POST["password"];

  $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

   header("Location: ../indexprivit.php");
    exit(); 
}
?>
