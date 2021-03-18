<?php
session_start();
include("conn.php");

$group_name = $_SESSION["group-name"];
$sql = "SELECT `name`,`message`,`group-pass`, EXTRACT( DAY FROM `date`) as 'date', EXTRACT( MONTH FROM `date`) as 'month', EXTRACT( YEAR FROM `date`) as 'year', EXTRACT( HOUR FROM `date`) as 'hour', EXTRACT( MINUTE FROM `date`) as 'minute' FROM `groupChat` WHERE `group-name`='$group_name';";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
  $name = $row['name'];
  $message = $row['message'];
  $group_pass = $row['group-pass'];

  $decryption_iv = '1234567891011121';
  $decryption_key = "GeeksforGeeks";
  $ciphering = "AES-128-CTR";
  

$options = 0;
  $decryption = openssl_decrypt ($message, $ciphering,
    $decryption_key, $options, $decryption_iv);

  $date_from_db = $row["date"];
  $month_from_db = $row["month"];
  $year_from_db = $row["year"];
  $hour_from_db = $row["hour"];
  $minute_from_db = $row["minute"];

  $date = "$hour_from_db:$minute_from_db $month_from_db/$date_from_db/$year_from_db";

  $actualyear = date('Y', strtotime($date));
  $actualmonth = date('m', strtotime($date));
  $actualday = date('d', strtotime($date));
  $actualhour = date('h', strtotime($date));
  $actualminute = date('i', strtotime($date));
  $amorpm = date('a', strtotime($date));

    if ($name==$_SESSION["created-by"] && $_SESSION["name"] != $name && $name != NULL) {
      echo('

    <div class="message-text name-stranger rounded-3 text-wrap px-2 py-1 bg-danger">
      <span class="d-flex justify-content-between"><b >'.$name.'</b><span class="badge bg-light fw-light text-dark fst-italic bg-secondary">'.$actualday."/".$actualmonth."/".$actualyear." • (Creator) • ".$actualhour.":".$actualminute." ".$amorpm.'</span></span>
      <p>
        '.$decryption.'
      </p>
    </div>
    <br/>
    ');
    }
    if ($name!=$_SESSION["created-by"] && $_SESSION["name"] != $name && $name != NULL) {
    echo('

    <div class="message-text name-stranger rounded-3 text-wrap px-2 py-1 bg-secondary">
      <span class="d-flex justify-content-between"><b >'.$name.'</b><span class="badge bg-light fw-light text-dark fst-italic bg-secondary">'.$actualday."/".$actualmonth."/".$actualyear." • ".$actualhour.":".$actualminute." ".$amorpm.'</span></span>
      <p>
        '.$decryption.'
      </p>
    </div>
    <br/>
    ');
  }

  if ($_SESSION["name"] == $name) {
    echo('

    <div class="message-text px-2 py-1 name-myself rounded-3 text-wrap bg-warning">
      <span class="d-flex justify-content-between"><b >'.$name.'</b><span class="badge bg-light fw-light text-dark fst-italic bg-secondary">'.$actualday."/".$actualmonth."/".$actualyear." • (You) • ".$actualhour.":".$actualminute." ".$amorpm.'</span></span>

      <p>
        '.$decryption.'
      </p>
    </div>
    <br/>
    ');
  }

  if ($name == NULL && $group_pass==NULL ) {
    echo('
    <div class="message-text new-user text-center text-white rounded-3 mb-4 text-wrap px-3 py-2 bg-primary">

      <span><strong>
        '.$message.'
      </strong></span>
    </div>
    ');
  }
  





}
?>