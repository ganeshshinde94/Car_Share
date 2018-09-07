<?php
//start session and connect
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
session_start();
include ('connection.php');

//get user_id and new email sent through Ajax
$user_id = $_SESSION['user_id'];
$newemail = $_POST['email'];

//check if new email exists
$sql = "SELECT * FROM users WHERE email='$newemail'";
$result = mysqli_query($link, $sql);
$count = $count = mysqli_num_rows($result);
if($count>0){
    echo "<div class='alert alert-danger'>There is already as user registered with that email! Please choose another one!</div>"; exit;
}


//get the current email
$sql = "SELECT * FROM users WHERE user_id='$user_id'";
$result = mysqli_query($link, $sql);

$count = mysqli_num_rows($result);

if($count == 1){
    $row = mysqli_fetch_array($result);
    $email = $row['email'];
}else{
    echo "<div class='alert alert-danger'>There was an error retrieving the email from the database</div>";exit;
}

//create a unique activation code
$activationKey = bin2hex(openssl_random_pseudo_bytes(16));

//insert new activation code in the users table
$sql = "UPDATE users SET activation2='$activationKey' WHERE user_id = '$user_id'";
$result = mysqli_query($link, $sql);
if(!$result){
    echo "<div class='alert alert-danger'>There was an error inserting the user details in the database.</div>";exit;
}else{
    //send email with link to activatenewemail.php with current email, new email and activation code
    $message = "Please click on this link prove that you own this email:\n\n";
    $message.="http://localhost:8666/carshare/activatenewemail.php?email=".urlencode($email)."&newemail=".urlencode($newemail)."&key=$activationKey";
//Load Composer's autoloader
require 'email/vendor/autoload.php';
//require 'index.php';
//print_r($_POST);;
// $email_body=$_POST['message']."<br>"."<br>"."with Regards"."<br>".$_POST['name']." ".$_POST['surname']."<br>".$_POST['phone'];
// $to="ganeshshinde1494@gmail.com";
// $email=$_POST['email'];
// $subject="Message from portfolio";
//echo "email:".$email;

//function send_$messageemail($to, $from, $subject, $email_body){
  $email_body=$message;
  $sender="ganeshshinde721@gmail.com";
  $to = $newemail;
  $subject="Confirm your Registration";
  // require_once '../../../gmailpassword.php';
  $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
  //try {
      //Server settings
      //$mail->SMTPDebug = 2;                               // Enable verbose debug output
      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'ganeshshinde721@gmail.com';   // SMTP username
      $mail->Password = 'ganeshshinde745';                   // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;                                    // TCP port to connect to

      //Recipients
      $mail->setFrom($sender,$sender);
      $mail->addAddress($to,$to);                      // Add a recipient
      //$mail->addAddress('ellen@example.com');             // Name is optional
      $mail->addAddress($to,$to);                      // Add a recipient

      $mail->addReplyTo($email,$email);
      //$mail->addCC('cc@example.com');
      //$mail->addBCC('bcc@example.com');

      //Attachments
      //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
      //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

      //Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $mail->Subject    =$subject;
      $mail->Body    = $email_body;
      $mail->AltBody = $email_body;

      $mail->send();
      echo "<div class='alert alert-success'>An email has been sent to $newemail. Please click on the link to prove you own that email address.</div>";
}


?>
