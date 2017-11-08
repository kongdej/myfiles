<?php
session_start();

error_reporting(0);
$config = parse_ini_file("../config.php", true);
$view = parse_ini_file("../view.ini", true);
$dbtype = $config['database']['data_type'];
$host = $config['database']['host'];
$user = $config['database']['user'];
$password = $config['database']['password'];
$database = $config['database']['default_database'];

$db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $passwod . '');
$query = "SELECT * FROM system_settings";
$sql = $db->prepare($query);
$sql->execute();
$data = $sql->fetchAll();
foreach ($data as $rows) {
    $config[$rows['name']] = $rows['value'];
}

$res['config']= $config;
$res['view'] = $view;
$res['session'] =$_SESSION;
echo json_encode($res);



