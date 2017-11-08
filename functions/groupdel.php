<?php
$id = $_GET['id'];
$sql = "DELETE FROM mailgroup WHERE id=" . $id;
$s = $db->prepare($sql);
$s->execute();

$res['msg'] = 1;
echo json_encode($res);



