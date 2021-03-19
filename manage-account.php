<?php
session_start();
include("conn.php");
$showAlert = false;
$showError = false;

$user_id = $_GET["user-id"];
if (!isset($_SESSION["logged"]) && $_SESSION["logged"] != true && $_SESSION["uid"] != $user_id) {
  header("location: http://localhost:8000/");
}


if (isset($_POST["delete-acc-confirm"])) {
  $sql = "DELETE FROM `Users` WHERE `Users`.`user_id` = $user_id;";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    $message_text = $_SESSION["name"]." is now gone forever :(";
        $sql_message = "INSERT INTO `messageDB` (`message`, `date`) VALUES ('$message_text', current_timestamp());";
        $result_message = mysqli_query($conn, $sql_message);
    session_destroy();
    header("location: http://localhost:8000/");
  }
}



if (isset($_POST["update-name-submit"])) {

  $update_name = $_POST['update-name'];

  $sql = "UPDATE `Users` SET `name` = '$update_name' WHERE `Users`.`user_id` = $user_id;";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    $_SESSION["name"] = $update_name;

    $showAlert = "Your name is changed";
  } else {
    $showError = "Unable to change name";
  }

}



if (isset($_POST["update-pass-submit"])) {
  $current_pass = $_POST["current-pass"];
  $update_pass = $_POST['update-pass'];
  $confirm_update_pass = $_POST['update-confirm-pass'];
  $user_id = $_GET["user-id"];
  $sql = "Select * from Users where user_id='$user_id'";
  $result = mysqli_query($conn, $sql);
  $num = mysqli_num_rows($result);
  if ($num == 1) {
    while ($row = mysqli_fetch_assoc($result)) {
      if ($update_pass == $confirm_update_pass) {


        if (password_verify($current_pass, $row['pass'])) {

          $pass_hash = password_hash($changed_pass, PASSWORD_DEFAULT);
          $sql1 = "UPDATE `Users` SET `pass` = '$pass_hash' WHERE `Users`.`user_id` = $user_id;";
          $result1 = mysqli_query($conn, $sql1);
          if ($result1) {
            $showAlert = "Your password is changed";
          } else {
            $showError = "Unable to change password";
          }

        } else {
          $showError = "Current password does not matching";
        }
      } else {
        $showError = "Updated confirm password does not matcing";
      }
    }
  }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">

  <title>TalkFriend – Manage Account!</title>
   <link rel="shortcut icon" href="white_logo.png" type="image/png" />
  <style>
@import url('https://fonts.googleapis.com/css2?family=Oswald&family=Redressed&display=swap');
  </style>
</head>
<body style="font-family: 'Oswald', sans-serif;">
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
  ?>
  <form method="post">
    <div class="container my-3 justify-content-center d-grid gap-2 text-center">
      <h5 class="text-center account-heading py-2">Update Your Name.</h5>
      <input required autocomplete="off" class="form-control" type="text" name="update-name" placeholder="Update your name..." />
              <div class="form-text text-muted">*emojis and symbols are not allowed.</div>
      <input type="submit" class="btn update-name-submit btn-success d-md-block" type="submit" value="Update Name" name="update-name-submit">
    </div>
  </form>
  <form method="post">
    <div class="container my-3 justify-content-center d-grid gap-2 text-center">
      <h5 class="text-center account-heading py-2">Update Your Password.</h5>
      <input required type="password" name="current-pass" class="form-control" placeholder="Current password..." />
      <input required autocomplete="off" type="text" name="update-pass" class="form-control" placeholder="Update password..." />
      <input required autocomplete="off" type="text" name="update-confirm-pass" class="form-control" placeholder="Re-type password..." />
      <input type="submit" class="btn update-pass-submit btn-success d-md-block" type="submit" value="Update Password" name="update-pass-submit">
    </div>
  </form>
  <hr style="margin: 2px 23px" />
  <div class="d-flex mt-3 justify-content-around">
    <button type="button" class="my-1 btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
      Delete Account
    </button>
    <a class="btn btn-outline-primary my-1" href="/">Back to Chat</a>
  </div>




  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal_title pt-3" id="exampleModalLabel">Are you sure?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal_body pt-3 text-center">
          <p>
            Do you really want to delete your account? <br>(This action can't be undone!)
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">No!</button>
          <form method="post">
            <input name="delete-acc-confirm" value="Yes!" class="btn btn-outline-danger" type="submit">
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

</body>
</html>