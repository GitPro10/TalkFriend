<?php
session_start();
include("conn.php");

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
      <textarea disabled required  autocomplete="off" autofocus="on" name="message" style="max-width: 75vw" type="text" class="form-control" placeholder="Group got deleted!" aria-describedby="basic-addon2"></textarea>
      <input disabled value="Send" name="message-submit" type="submit" class="btn message-send-button input-group-text" id="basic-addon2">
    </div>
  </form>');


}

?>