<?php
$conn = new mysqli("localhost", "root", "", "newcontact");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $conn->real_escape_string($_POST["name"]);
  $email = $conn->real_escape_string($_POST["email"]);
  $message = $conn->real_escape_string($_POST["message"]);

  $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$message')";
  if ($conn->query($sql)) {
    echo "Message sent successfully!";
  } else {
    echo "Error: " . $conn->error;
  }
}
?>
