<?php
session_start();

//$config = parse_ini_file("config.ini", true);
if (file_exists("config.php")) {
    $config = parse_ini_file("config.php", true);
}
else {
    header('Location: install.php');
    
}
//print_r($_SESSION);
require_once("common/connector/data_connector.php"); //!connector

$dbtype = $config['database']['data_type'];
$host = $config['database']['host'];
$user = $config['database']['user'];
$password = $config['database']['password'];

if (isset($_SESSION['database'])) {
    $database=$_SESSION['database'];
}
else {
    $database = $config['database']['default_database'];
}

$conn = mysql_connect($host, $user, $password);
mysql_select_db($database);
mysql_query('SET NAMES utf8');
mysql_query('SET SET CHARACTER SET utf8');

//echo 'mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $password . '';
try {
    $db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $password . '');
} catch(PDOException $e) {
    echo 'Error!:'.$e->getMessage();
    die();    
}


function getSettings() {
    global $db, $config;

    $query = "SELECT * FROM system_settings";
    $sql = $db->prepare($query);
    $sql->execute();
    $data = $sql->fetchAll();
    foreach ($data as $rows) {
        $config[$rows['name']] = $rows['value'];
        if ($rows['name']=='cur_theme') {
            if ($rows['value']=='') {
                $config[$rows['name']] = "flat.css";
            }
            else 
                $config[$rows['name']] = $rows['value'].'.css';
        }
    }
}
function isUserlogin() {
    return (isset($_SESSION['uid'])) ? TRUE:FALSE;
}

function isAdmin() {
    return ($_SESSION['level'] === 'admin'  ) ? TRUE:FALSE;
}

function isDocadmin() {
    return ($_SESSION['level'] === 'docadmin'  ) ? TRUE:FALSE;
}

function isUser() {
    return ($_SESSION['level'] === 'user'  ) ? TRUE:FALSE;
}

function firstDB() {
    global $config;
    if (isset($_SESSION['database'])){
        return $_SESSION['database'];
    }
    else {
        foreach ($config['site'] as $dbname=>$name) {
            return $dbname;
        }
    }
}

function getDocumentName($id) {
    global $db;

    $stmt = $db->prepare("SELECT name FROM contents WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $name = $stmt->fetchColumn();    

    return $name;
}

function getLastId($table) {
    global $db;

//    $lid = $db->prepare("SELECT MAX(id) FROM " . $table);
    $lid = $db->prepare("SELECT id FROM " . $table." ORDER BY id DESC LIMIT 0,1");
    $lid->execute();
    $lastid = $lid->fetchColumn();
//    echo $lastid;

    return $lastid;
}

function getDocumentPath($id) {
    global $db;
    
    $stmt = $db->prepare("SELECT created FROM contents WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $created = $stmt->fetchColumn();    
    list($date,$_) = split(' ',$created);
    list($y,$m,$d) = split('-',$date);
    list($_,$site) = split('_',$_SESSION['database']);   // get path from name of database
    $filename = "./sites/".$site."/documents/".$y.$m."/".$id.".*";  // file all
//    echo $filename;
    foreach(glob($filename) as $file) {
        if (file_exists($file)) {
            return $file;
        }
    }           
}
function getPath($id) {
    global $db;
    
    $stmt = $db->prepare("SELECT created FROM contents WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $created = $stmt->fetchColumn();    
    list($date,$_) = split(' ',$created);
    list($y,$m,$d) = split('-',$date);
    list($_,$site) = split('_',$_SESSION['database']);   // get path from name of database
    
    return "./sites/".$site."/documents/".$y.$m;
}

function getUserId($username) {
    global $db;

    $stmt = $db->prepare("SELECT uid FROM users WHERE username=:username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $uid = $stmt->fetchColumn();

    return $uid;
}

function getUserEmail($id) {
    global $db;

    $stmt = $db->prepare("SELECT email FROM users WHERE uid=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $email = $stmt->fetchColumn();

    return $email;
}

function getUsername($id) {
    global $db;

    $stmt = $db->prepare("SELECT name FROM users WHERE uid=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $name = $stmt->fetchColumn();

    return $name;
}

function getFolderName($id) {
    global $db;

    $stmt = $db->prepare("SELECT text FROM folder WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $name = $stmt->fetchColumn();

    return $name;
}

function getFolderId($id) {
    global $db;

    $stmt = $db->prepare("SELECT folder_id FROM contents WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $folder_id = $stmt->fetchColumn();

    return $folder_id;
}

function getDocumentReviseDate($id) {
    global $db;

    $stmt = $db->prepare("SELECT revise_date FROM contents WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetchColumn();
}

function getDocument($id) {
    global $db;

    $stmt = $db->prepare("SELECT * FROM contents WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    return $stmt->fetchAll();
}

function getParentTree($folder_id, $db) {
    global $folderpaths;

    $stmt = $db->prepare("SELECT parent_id,text FROM folder WHERE id=:id");
    $stmt->bindValue(':id', $folder_id, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rows['parent_id']) {
        $folderpaths[] = $rows['text'];
        getParentTree($rows['parent_id'], $db);
    }
    else {
        $folderpaths[] = '>>'.$rows['text'];        
    }
}

function logging($event) {
    global $db;
    $stmt = $db->prepare("INSERT INTO logging (uid,event,modified) VALUES (:uid,:event,:modified)");
    $stmt->bindParam(':uid', $_SESSION['uid']);
    $stmt->bindParam(':event', $event);
    date_default_timezone_set('Asia/Bangkok');
    $now = date('Y-m-d H:i:s');
    $stmt->bindParam(':modified', $now);
    $stmt->execute();
}