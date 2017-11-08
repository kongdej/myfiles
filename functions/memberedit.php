<?php
$id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM mailgroup WHERE id=".$id);
$stmt->execute();
$row = $stmt->fetch();
$email = $row['emails'];
$name = $row['name'];

$emails =  explode(',',$email);
for($i=0;$i<count($emails);$i++){
    $email_list[]="'".$emails[$i]."'";
}
$emailq = implode(',', $email_list);

$sql = "SELECT * FROM users where email in (".$emailq.") order by username";
$stmt = $db->prepare($sql);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lists[] = "{\"name\":\"" . $row['name'] . "\",\"email\":\"" . $row['email'] . "\"}";
}
$res['data']=$lists;
$res['name'] = $name;
echo json_encode($res);

