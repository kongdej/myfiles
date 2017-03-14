<?php
$action = $_GET['action'];

if ($action == 'del') {
    $stmt = $db->prepare("DELETE FROM logging");
    $stmt->execute();
    echo '{"msg":"Clear logging done."}';  
}
else {
    $data = new JSONDataConnector($conn, $dbtype);
    $data->set_encoding("utf8");
    $data->sort("modified DESC");
    header('Content-Type: text/html; charset=utf-8');
    $data->dynamic_loading(50);
    //$data->render_table("logging", "id", "event,modified,uid");
    $data->render_sql("SELECT * FROM logging l ,users u WHERE u.uid=l.uid", "id", "modified,event,name");
}