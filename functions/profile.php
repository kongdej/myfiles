<?php
$data = new JSONDataConnector($conn, $dbtype);
$username = $_GET['username'];
$data->filter("username", $username);

$data->render_table("users", "uid,username,name,password,email,position");

