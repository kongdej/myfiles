<?php
///<===DELETE
$config = parse_ini_file("config.ini", true);
$dbtype = $config['database']['data_type'];
$host = $config['database']['host'];
$user = $config['database']['user'];
$password = $config['database']['password'];
$database = $config['database']['default_database'];

$db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $passwod . '');

function getSettings() {
    global $db, $config;

    $query = "SELECT * FROM system_settings";
    $sql = $db->prepare($query);
    $sql->execute();
    $data = $sql->fetchAll();
    foreach ($data as $rows) {
        $config[$rows['name']] = $rows['value'];
    }
}

function firstDB() {
    global $config;
    foreach ($config['site'] as $dbname=>$name) {
        return $dbname;
    }
}