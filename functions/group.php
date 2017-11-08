<?php
$uid = $_SESSION['uid'];
$data = new JSONDataConnector($conn, $dbtype);
$data->sort('name');  
$data->filter("uid", $uid);
$data->render_table("mailgroup","id","name,emails,uid");



