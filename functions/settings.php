<?php
$action = $_GET['action'];

if ($action == "update") {
    $stmt = $db->prepare("UPDATE system_settings SET value=:cur_theme WHERE name='cur_theme'");
    $stmt->bindParam(':cur_theme', $_POST['cur_theme']);
    $stmt->execute();
    $stmt = $db->prepare("UPDATE system_settings SET value=:title WHERE name='title'");
    $stmt->bindParam(':title', $_POST['title']);
    $stmt->execute();
    $stmt = $db->prepare("UPDATE system_settings SET value=:copyright WHERE name='copyright'");
    $stmt->bindParam(':copyright', $_POST['copyright']);
    $stmt->execute();
   
    logging('Updatesettings');        
    $res = array(
       'status' => 'ok',
       'sname' => 'Save settings done.'
    );
}
else {
    $query = $db->prepare("SELECT * FROM system_settings");
    $query->execute();
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
        $res[$row['name']]=$row['value'];
    }
}
echo json_encode($res);
