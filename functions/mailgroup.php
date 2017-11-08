<?php
$id = $_GET['id'];
$group_id = $_POST['listgroup'];
$uid = $_SESSION['uid'];
$from = getUserEmail($uid);
$username = getUsername($uid);
$attachfile = getDocumentPath($id);

$docname = getDocumentName($id);
$subject = 'เอกสารเวียน - '.$docname;
$message =  'เรียนทุกท่าน<br><br>';
$message .= '  เพื่อโปรดทราบเอกสารเวียนดังแนบ<br>';
$message .= '  - '.$docname.'<br><br>';
$message .= $username.'<br>ผู้ส่งเอกสาร<br>';

// get mailgroup data
$sql = "SELECT emails FROM mailgroup WHERE id=".$group_id;
$stmt = $db->prepare($sql);
$stmt->execute();
$emails = $stmt->fetchColumn();   
$email_list = explode(',', $emails);

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
$mail->setFrom('tpc.myfiles@egat.co.th', 'Mailer');
//$mail->setFrom($from, $username);
foreach($email_list as $to_add){
    if (!empty($to_add)) {
        $mail->AddAddress($to_add); 
    }
}
$mail->addReplyTo($from);
$mail->addCC($cc);
$mail->addAttachment($attachfile);         // Add attachments
$mail->isHTML(true);                       // Set email format to HTML
$mail->Subject = $subject;
$mail->Body   = $message;
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
