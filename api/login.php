<?php session_start(); // Gunakan DIR agar pasti menemukan file di folder yang sama (folder api) require_once(DIR . '/koneksi.php');

if (isset($_POST['login'])) { $username = mysqli_real_escape_string($conn, $_POST['username']); $password = $_POST['password'];

} ?>

<!DOCTYPE html>

<html> <head> <title>Login Sistem</title> </head> <body> <div style="width: 300px; margin: 100px auto; padding: 20px; border: 1px solid #ccc; text-align: center;"> <h2>Login Smart Arca</h2> <form method="POST" action=""> <input type="text" name="username" placeholder="Username" required style="display:block; width:90%; margin:10px auto;">

<input type="password" name="password" placeholder="Password" required style="display:block; width:90%; margin:10px auto;">


<button type="submit" name="login">MASUK</button> </form> </div> </body> </html>
