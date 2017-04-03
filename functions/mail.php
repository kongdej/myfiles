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


require 'PHPMailer/PHPMailerAutoload.php';
$mail = new PHPMailer;
$mail->SMTPDebug = 3;               // Enable verbose debug output
$mail->isSMTP();                    // Set mailer to use SMTP
$mail->Host = 'mail.egat.co.th';    // Specify main and backup SMTP servers
$mail->SMTPAuth = true;             // Enable SMTP authentication
$mail->Username = '950601';         // SMTP username
$mail->Password = 'tpc.myfiles1!';     // SMTP password
$mail->SMTPSecure = 'tls';          // Enable TLS encryption, `ssl` also accepted
$mail->Port = 25;                   // TCP port to connect to
$mail->SMTPDebug = 0;


$mail->SMTPOptions = array(
	'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => false
    )
);

$mail -> CharSet = "UTF-8";
$mail->setFrom('tpc.myfiles@egat.co.th', 'Mailer');
$mail->addAddress($to);     // Add a recipient
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
