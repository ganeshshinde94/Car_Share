<?php
$link = mysqli_connect("localhost","root","","carshare");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
  else {
    //echo "connection successful";
    // $sql="SELECT * FROM users";
    // $result = mysqli_query($con,$sql);
    // $test= mysqli_fetch_array($result );
    //print_r($test);
  }
?>
