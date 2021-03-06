<?php
$id = $_GET['id'];
$to = $_POST['to'];
$cc = $_POST['cc'];
$subject = $_POST['subject'];
$message = $_POST['message'];

$uid = $_SESSION['uid'];
$from = getUserEmail($uid);
$username = getUsername($uid);

$attachfile = getDocumentPath($id);

$matches = array();
$pattern = '/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i';
preg_match_all($pattern,$to,$matches);
$to = $matches[0][0];
//print_r($matches);
//echo $to;

require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;

//PHPMailer for EGAT
/*
$mail->SMTPDebug = 3;               // Enable verbose debug output
$mail->isSMTP();                    // Set mailer to use SMTP
$mail->Host = 'mail.egat.co.th';    // Specify main and backup SMTP servers
$mail->Port = 25;                   // TCP port to connect to
$mail->SMTPAuth = true;             // Enable SMTP authentication
$mail->Username = '539953';         // EGAT username
$mail->Password = 'xxxxxxxxxxxxx';  // EGAT password
$mail->SMTPSecure = 'tls';          // Enable TLS encryption, `ssl` also accepted
$mail->SMTPDebug = 0;
*/

//PHPMailer for gmail
$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'tls';
$mail->Username = "xxxxx@gmail.com";   // gmail user
$mail->Password = "xxxxxxxxxxxxxxx";   // gmail password

$mail->SMTPOptions = array(
	'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => false
    )
);

$mail -> CharSet = "UTF-8";
$mail->setFrom('xxxx@mail.com', 'Mailer'); // <--Add a email sender
$mail->addAddress($to);                     // Add a recipient
$mail->addReplyTo($from);
$mail->addCC($cc);
$mail->addAttachment($attachfile);         // Add attachments

$mail->isHTML(true);                       // Set email format to HTML

$mail->Subject = $subject;
$mail->Body    = '<b>Document: '.getDocumentName($id)."<br>";
$mail->Body   .= "Message from: ".$username ."&lt;".$from."&gt;<br><br></b>";
$mail->Body   .= $message;
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    $msg =  'Message could not be sent.';
    $msg .= 'Mailer Error: ' . $mail->ErrorInfo;
    $res = array("status" => "err", "sname" => $msg);    
} else {
    $msg = 'Message has been sent';
    $res = array("status" => "ok", "sname" => $msg);    
    logging('Mail-'.$id.'- '.getDocumentName($id).'-to-'.$to);
}

echo json_encode($res);
