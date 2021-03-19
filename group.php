<?php
session_start();
include("/server/conn.php");
$showAlert = false;
$showError = false;
$showAlertJS = false;
$group_id = rand();
if (!isset($_SESSION["logged"]) && $_SESSION["logged"] != true) {
  header("location: http://llocalhost:8000/");
}


if (isset($_POST['exit-group'])) {
  /*
  $group_name_session = $_SESSION["group-name"];
  $message_text = $_SESSION["name"]." left the group!";
  $sql = "INSERT INTO `groupChat` (`group-name`, `message`, `date`) VALUES ('$group_name_session', '$message_text', current_timestamp());";
  $result = mysqli_query($conn, $sql);
  */
  $_SESSION["in-a-group"] = false;
  $_SESSION["group-name"] = NULL;
  $_SESSION["group-owner"] = false;
}



if (isset($_POST['group-create-submit'])) {
  $group_name = $_POST['group-name'];
  $group_pass = $_POST['group-pass'];



  $sql2 = "Select * from groupChat where `group-name`='$group_name'";
  $result2 = mysqli_query($conn, $sql2);
  if ($result2) {
  $num2 = mysqli_num_rows($result2);
  if ($num2 > 0) {
    while ($row2 = mysqli_fetch_assoc($result2)) {

      if ($row2["group-name"] == $group_name) {
        $showError = "A group already exists with this name";
      }
    }
  } else {

    $_SESSION["group-owner"] = $_SESSION["name"];
    $created_by = $_SESSION["group-owner"];
    $hash = password_hash($group_pass, PASSWORD_DEFAULT);
    $sql = "INSERT INTO `groupChat` (`group-name`, `group-pass`, `group-id`, `group-owner`, `date`) VALUES ('$group_name', '$hash','$group_id', '$created_by', current_timestamp());";
    $result = mysqli_query($conn, $sql);
    if ($result) {
      $showAlert = "Your group have been created";
      $_SESSION["group-name"] = $group_name;
      $_SESSION["gid"] = $group_id;
      $showAlertJS = 'Your secret key for ('.$_SESSION["group-name"].') this group is '.$_SESSION["gid"].'. Take a screenshot of this and save this informations. It\'s very important!';
      $_SESSION["in-a-group"] = true;
      $group_name_session = $_SESSION["group-name"];
      $message_text = $_SESSION["group-owner"]." created the group!";
      $sql1 = "INSERT INTO `groupChat` (`group-name`, `message`, `date`) VALUES ('$group_name_session', '$message_text', current_timestamp());";
      $result1 = mysqli_query($conn, $sql1);
      header("Refresh: 1");
    } else {
      $showError = "Unable to create group";

    }
  }
  } else {
    $showError = "Unable to create group";
  }

}

if (isset($_POST['group-join-submit'])) {
  $group_name = $_POST['group-name'];
  $group_pass = $_POST['group-pass'];
  $group_id_given = $_POST['group-id'];
  if ($_SESSION["in-a-group"] == true) {
    $showError = "Already in a group";

  }



  $sql = "Select * from groupChat where `group-id`='$group_id_given'";
  $result = mysqli_query($conn, $sql);
  $num = mysqli_num_rows($result);
  if ($num == 1) {
    while ($row = mysqli_fetch_assoc($result)) {
      if ($row["group-name"] == $group_name) {
        if (password_verify($group_pass, $row['group-pass'])) {
          $showAlert = "Joined the group";
          $_SESSION["group-name"] = $group_name;
          $_SESSION["in-a-group"] = true;
          $_SESSION["created-by"] = $row["group-owner"];

          $group_name_session = $_SESSION["group-name"];
          $message_text = $_SESSION["name"]." entered to the group!";
          $sql1 = "INSERT INTO `groupChat` (`group-name`, `message`, `date`) VALUES ('$group_name_session', '$message_text', current_timestamp());";
          $result1 = mysqli_query($conn, $sql1);

          header("Refresh: 1");
        } else {
          $showError = "Password does not matching";
        }
      } else {
        $showError = "Name does not matching";
      }

    }

  } else {
    $showError = "No group found";

  }
}

if (isset($_POST['message-submit'])) {
  $message_name = $_SESSION["name"];
  $message_text = $_POST["message"];
  $group_name_session = $_SESSION["group-name"];

  $simple_string = $message_text;
  $ciphering = "AES-128-CTR";
  $iv_length = openssl_cipher_iv_length($ciphering);
  $options = 0;
  $encryption_iv = '1234567891011121';
  $encryption_key = "GeeksforGeeks";
  $encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);




  $sql = "INSERT INTO `groupChat` (`group-name`, `name`, `message`, `date`) VALUES ('$group_name_session', '$message_name', '$encryption', current_timestamp());";

  $result = mysqli_query($conn, $sql);


}
if (isset($_POST["delete-group-confirm"])) {
  $group_name_owner_regeistered = $_SESSION['group-name'];
  $sql = "DELETE FROM `groupChat` WHERE `groupChat`.`group-name` = '$group_name_owner_regeistered';";
  $result = mysqli_query($conn, $sql);
  if ($result) {


    $_SESSION["in-a-group"] = false;
    $_SESSION["group-name"] = NULL;
    $_SESSION["group-owner"] = false;
    header("location: http://localhost:8000/group");
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

  <title>
    <?php
    if (isset($_SESSION["group-name"]) && $_SESSION["group-name"] != NULL) {
      echo("TalkFriend – ".$_SESSION["group-name"]);
    } else {
      echo("TalkFriend – Create Group");
    }

    ?>
  </title>
  <link rel="shortcut icon" href="white_logo.png" type="image/png" />
  <style type="text/css" media="all">
@import url('https://fonts.googleapis.com/css2?family=Oswald&family=Redressed&display=swap');
    .create-group-div {
      font-family: 'Oswald', sans-serif;
    }
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
    .wlc-msg,.form-control,.message-send-button,.alert,.exit-group-btn,.manage-group-btn,.back-to-chat-btn,.form-text,.modal {
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
  if ($showAlertJS) {
    echo('
    <script>
    alert("'.$showAlertJS.'");
    </script>
    ');
  }
  if ($_SESSION['in-a-group'] == false) {
    echo('
   <form method="post">
    <div class="my-5 create-group-div justify-content-center d-grid gap-2">
      <input required autofocus="on" autocomplete="off" class="form-control" type="text" placeholder="Enter Group Name..." name="group-name" id="group-name"  ?>
        <div class="form-text text-muted">*emojis and symbols are not allowed.</div>
      <input required autofocus="on" autocomplete="off" class="form-control" type="password" name="group-pass" id="group-pass" placeholder="Enter Password..." />
      <input type="submit" class="btn btn-success d-md-block" name="group-create-submit" value="Create Group" />
    </div>
  </form>
  <hr style="margin: 2px 23px;"/>
  <form method="post">
    <div class="my-5 create-group-div justify-content-center d-grid gap-2">
      <input required autofocus="on" autocomplete="off" class="form-control" type="text" placeholder="Enter Group Name..." name="group-name" id="group-name" />
      <input required autofocus="on" autocomplete="off" class="form-control" type="password" name="group-pass" id="group-pass" placeholder="Enter Password..." />
      <input required autofocus="on" autocomplete="off" class="form-control" type="number" name="group-id" placeholder="Enter group secret key..." />
      <input type="submit" class="btn btn-success d-md-block" name="group-join-submit" value="Join Group" />
    </div>
  </form>
  ');
  } else {
    echo('
    <div class="d-flex mt-3 justify-content-around">
    <form method="post"><input name="exit-group" class="btn btn-danger exit-group-btn" type="submit" value="Exit Group"></form>');
    if ($_SESSION["created-by"] == $_SESSION['name'] || $_SESSION["group-owner"] == true) {
      echo('<a role="button" class="btn back-to-chat-btn btn-success" href="/">Back to Main Chat</a> <button type="button" class="manage-group-btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
      Delete Group
    </button>
    ');
    } else {
      echo('<a role="button" class="btn back-to-chat-btn btn-success" href="/">Back to Main Chat</a>');
    }

    echo('</div>
 <div id="message-container" class="cotainer shadow py-4 my-3 rounded-2 mx-3 px-2 border border-2">
    <h5 class="text-center wlc-msg py-2"> Welcome  '.$_SESSION['name'].'!</h5>
    <div id="messages"></div>
  </div>');

    $group_name_other_member = $_SESSION['group-name'];
    $check_group_exist_sql = "SELECT *  FROM `groupChat` WHERE `group-name` LIKE '$group_name_other_member'";
    $sql_result = mysqli_query($conn, $check_group_exist_sql);
    $numrows = mysqli_num_rows($sql_result);
    if ($numrows > 0) {
      echo('
  <form method="post">
    <div class="input-group d-flex justify-content-center mb-3  my-0">
      <textarea required  autocomplete="off" autofocus="on" name="message" style="max-width: 75vw" type="text" class="form-control" placeholder="Say something..." aria-describedby="basic-addon2"></textarea>
      <input value="Send" name="message-submit" type="submit" class="btn message-send-button input-group-text" id="basic-addon2">
    </div>
  </form>');


    } else {
      echo('
  <form method="post">
    <div class="input-group d-flex justify-content-center mb-3  my-0">
      <textarea disabled required  autocomplete="off" autofocus="on"  style="max-width: 75vw" type="text" class="form-control" placeholder="Group got deleted!" aria-describedby="basic-addon2"></textarea>
      <input disabled value="Send" type="submit" class="btn message-send-button input-group-text" id="basic-addon2">
    </div>
  </form>');


    }



  }

  ?>

  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal_title pt-3" id="exampleModalLabel">Are you sure?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal_body pt-3 text-center">
          <p>
            Do you really want to delete this group? <br>(This action can't be undone!)
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">No!</button>
          <form method="post">
            <input name="delete-group-confirm" value="Yes!" class="btn btn-outline-danger" type="submit">
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
    $(document).ready(function() {
      setInterval(function () {
        $('#messages').load('/server/server2.php')

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
