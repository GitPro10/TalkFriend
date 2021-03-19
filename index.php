<?php
session_start();
include("conn.php");

$showAlert = false;
$showError = false;



if (isset($_POST['submit-btn'])) {
  $user_name = $_POST['user-name'];
  $login_pass = $_POST['user-pass'];


  $sql = "Select * from Users where name='$user_name'";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    $num = mysqli_num_rows($result);
    if ($num == 1) {
      while ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($login_pass, $row['pass'])) {
          $_SESSION["uid"] = $row["user_id"];
          $_SESSION["name"] = $row['name'];

          $_SESSION["logged"] = true;
          /*
        $message_text = $_SESSION["name"]." return to the chat!";
        $sql_message = "INSERT INTO `messageDB` (`message`, `date`) VALUES ('$message_text', current_timestamp());";
        $result_message = mysqli_query($conn, $sql_message);
        */
          $showAlert = "Logged In to your account";
          header("Refresh: 1");
        } else {

          $showError = "Invalid credentials";
        }
      }

    } else {
      $showError = "Account does not exists, Create new";

    }
  } else {
    $showError = "Account does not exists, Create new";
  }
}
if (isset($_POST['signup_submisson'])) {
  $signup_name = $_POST['name'];
  $signup_pass = $_POST['pass'];
  $signup_cpass = $_POST['cpass'];

  $existSql1 = "Select * from Users where name='$signup_name'";
  $result1 = mysqli_query($conn, $existSql1);
  if ($result1) {


    $numExistRows1 = mysqli_num_rows($result1);


    if ($numExistRows1 > 0) {
      $showError = "An account already exists with this name";
    } else {
      if ($signup_pass == $signup_cpass) {
        $hash = password_hash($signup_pass, PASSWORD_DEFAULT);
        $uid_raw = rand();
        $user_id = substr($uid_raw, 0, 5);
        $sql = "INSERT INTO `Users` (`name`, `pass`, `user_id`, `register_date`) VALUES ('$signup_name', '$hash', '$user_id', current_timestamp());";
        $result = mysqli_query($conn, $sql);
        if ($result) {
          $_SESSION["name"] = $signup_name;
          $_SESSION["logged"] = true;
          $_SESSION["uid"] = $user_id;
          $message_text = $_SESSION["name"]." joined the chat!";
          $sql = "INSERT INTO `messageDB` (`message`, `date`) VALUES ('$message_text', current_timestamp());";
          $result = mysqli_query($conn, $sql);

          $showAlert = "Your account have been created";
          header("Refresh: 1");

        } else {
          $showError = "Unable to create account";
        }
      } else {
        $showError = "Password does not matching";
      }
    }
  } else {
    $showError = "Unable to create account";
  }
}
if (isset($_POST['message-submit'])) {
  $message_name = $_SESSION["name"];
  $message_text = $_POST["message"];

  $simple_string = $message_text;
  $ciphering = "AES-128-CTR";
  $iv_length = openssl_cipher_iv_length($ciphering);
  $options = 0;
  $encryption_iv = '1234567891011121';
  $encryption_key = "GeeksforGeeks";
  $encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);


  $sql = "INSERT INTO `messageDB` (`name`, `message`, `date`) VALUES ('$message_name', '$encryption', current_timestamp());";

  $result = mysqli_query($conn, $sql);





}
if (isset($_POST["logout"])) {
  /*
  $message_text = $_SESSION["name"]." left the chat!";
  $sql = "INSERT INTO `messageDB` (`message`, `date`) VALUES ('$message_text', current_timestamp());";
  $result = mysqli_query($conn, $sql);
  */
  session_destroy();
  header("location: http://localhost:8000/");

  $showAlert = "Logged Out from your Account";

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
  <link rel="shortcut icon" href="white_logo.png" type="image/png" />
  <title>TalkFriend</title>
  <style type="text/css" media="all">

@import url('https://fonts.googleapis.com/css2?family=Oswald&family=Redressed&display=swap');
    .message-text p {
      font-weight: normal;
    }
    .chat-option-dropdown {
      color: black;
    }
    .chat-option-dropdown:hover {
      color: #5b5b5b;
    }
    .name-stranger {
      color: White;
      font-family: 'Oswald', sans-serif;

    }
    .name-myself {
      font-family: 'Oswald', sans-serif;
      color: black;
    }
    .name-stranger b, .name-myself b,.new-user {
      font-family: 'Redressed', cursive;
    }
    .wlc-msg,.form-control,.message-send-button,.strt-cht-btn,.account-heading,.alert,.manage-acc-btn,.logout-btn,.group-chat-btn,.form-text {
      font-family: 'Oswald', sans-serif;
    }

    #message-container {

      height: 80vh;
      overflow: scroll;

    }
    .message-send-button {
      color: black;
      width: 60px;
      background-color: #ebebeb;
    }
    .message-send-button:hover {
      opacity: 75%;
    }
  </style>
</head>
<body>


  <?php

  if ($showAlert) {
    echo('
    <div class="alert my-0 alert-success alert-dismissible fade show" role="alert">
  <strong>Success: </strong>'.$showAlert.'.
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
    ');
  }
  if ($showError) {
    echo('
    <div class="alert my-0 alert-danger alert-dismissible fade show" role="alert">
  <strong>Error: </strong>'.$showError.'.
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
    ');
  }





  if (!isset($_SESSION['logged']) && $_SESSION['logged'] != true) {
    echo('
  <form method="post">
  <h5 class="text-center account-heading py-2">LogIn to your Account.</h5>
  <div class="container my-3 justify-content-center d-grid gap-2 text-center">
    <input class="form-control" type="text" name="user-name" required autocomplete="off" placeholder="Enter your name..."/>
    <input class="form-control" required autocomplete="off"  type="password" name="user-pass" placeholder="Enter password...">

    <input class="btn strt-cht-btn btn-success d-md-block" type="submit" name="submit-btn" value="Start Chat" />
   </div>
  </form>
  <hr style="margin: 2px 20px"/>
  <form method="post">
  <h5 class="text-center account-heading py-2">Create a new Account.</h5>
  <div class="container my-3 justify-content-center d-grid gap-2 text-center">
    <input class="form-control" type="text" name="name" required autocomplete="off"  placeholder="Enter your name..."/>
    <div class="form-text text-muted">*emojis and symbols are not allowed.</div>
    <input class="form-control" required autocomplete="off"  type="password" name="pass" placeholder="Create password...">
    <input class="form-control" required autocomplete="off"  type="password" name="cpass" placeholder="Confirm password...">

    <input class="btn strt-cht-btn btn-success d-md-block" type="submit" name="signup_submisson" value="Create Account">
   </div>
  </form>
      ');
  } else {
    echo('
    <div class="d-flex mt-3 justify-content-around">
    <form method="post"><input name="logout" class="btn btn-danger logout-btn" type="submit" value="Log Out"></form>
    <a href="/group" class="btn group-chat-btn btn-primary" role="button">Group Chat</a>
    <a href="/manage-account?user-id='.$_SESSION["uid"].'" class="btn manage-acc-btn btn-success" role="button">Manage your account</a>
    </div>
 <div id="message-container" class="cotainer shadow py-4 my-3 rounded-2 mx-3 px-2 border border-2">
    <h5 class="text-center wlc-msg py-2"> Welcome  '.$_SESSION['name'].'!</h5>');
    echo('<div id="messages"></div>');

    echo('
    </div>
  <form method="post">
    <div class="input-group d-flex justify-content-center mb-3  my-0">
      <textarea required  autocomplete="off" autofocus="on" name="message" style="max-width: 75vw" type="text" class="form-control" placeholder="Say something..." aria-describedby="basic-addon2"></textarea>
      <input value="Send" name="message-submit" type="submit" class="btn message-send-button input-group-text" id="basic-addon2">
    </div>
  </form>');
  }
  ?>
  <?php

  ?>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
    $(document).ready(function() {
      setInterval(function () {
        $('#messages').load('server.php')

      }, 1000);

    });
    $(document).ready(function() {
      setInterval(function () {

        $("#message-container").scrollTop(function() {
          return this.scrollHeight;
        });
      }, 0);
    });


  </script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>