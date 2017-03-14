<?php

$data = new JSONDataConnector($conn, $dbtype);
$data->sort('username');  
$data->render_table("users","uid","username,name,password,email,position");



