<?php
$uid = $_SESSION['uid'];
$stmt = $db->prepare("SELECT * FROM mailgroup where uid=".$uid." order by name");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lists[] = "{\"id\":\"" . $row['id'] . "\",\"value\":\"" . $row['name'] . "\"}";
}
$res = join(',',$lists);
echo '['.$res .']';
