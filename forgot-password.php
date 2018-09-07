<?php
//Start session
session_start();
//Connect to the database
include('connection.php');
$errors='';

//Check user inputs
    //Define error messages
$missingEmail = '<p><strong>Please enter your email address!</strong></p>';
$invalidEmail = '<p><strong>Please enter a valid email address!</strong></p>';
    //Get email
    //Store errors in errors variable
if(empty($_POST["forgotemail"])){
    $errors .= $missingEmail;
}else{
    $email = filter_var($_POST["forgotemail"], FILTER_SANITIZE_EMAIL);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errors .= $invalidEmail;
    }
}

    //If there are any errors
        //print error message
if($errors){
    $resultMessage = '<div class="alert alert-danger">' . $errors .'</div>';
    echo $resultMessage;
    exit;
}
    //else: No errors
        //Prepare variables for the query
$email = mysqli_real_escape_string($link, $email);
        //Run query to check if the email exists in the users table
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($link, $sql);
if(!$result){
    echo '<div class="alert alert-danger">Error running the query!</div>'; exit;
}
$count = mysqli_num_rows($result);
//If the email does not exist
            //print error message
if($count != 1){
    echo '<div class="alert alert-danger">That email does not exist on our database!</div>';  exit;
}

        //else
            //get the user_id
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$user_id = $row['user_id'];
            //Create a unique  activation code
$key = bin2hex(openssl_random_pseudo_bytes(16));
            //Insert user details and activation code in the forgotpassword table
$time = time();
$status = 'pending';
$sql = "INSERT INTO forgotpassword (`user_id`, `rkey`, `time`, `status`) VALUES ('$user_id', '$key', '$time', '$status')";
$result = mysqli_query($link, $sql);
if(!$result){
    echo '<div class="alert alert-danger">There was an error inserting the users details in the database!</div>';
    exit;
}

            //Send email with link to resetpassword.php with user id and activation code

$message = "Please click on this link to reset your password:\n\n";
$message .= "http://localhost:8666/carshare/resetpassword.php?user_id=$user_id&key=$key";
//if(mail($email, 'Reset your password', $message, 'From:'.'developmentisland@gmail.com')){
        //If email sent successfully
                //print success message
       //echo "<div class='alert alert-success'>An email has been sent to $email. Please click on the link to reset your password.</div>";
//}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
  $to = $email;
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
       echo "<div class='alert alert-success'>An email has been sent to $email. Please click on the link to reset your password.</div>";
      // }

    ?>
