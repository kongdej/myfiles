<?php

//require_once("../common/init.php");
$config = parse_ini_file("config.ini", true);
getSettings();

$id = $_GET['id'];
$file = getDocumentPath($id);

/*
//$db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $password . '');
$stmt = $db->prepare("SELECT name FROM contents WHERE id=:id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
$name = $rows['name'];

/*
$stmt = $db->prepare("SELECT filepath,mime_id FROM document_version WHERE document_id=:id ORDER BY modified DESC");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
$filepath = $rows['filepath'];


$stmt = $db->prepare("SELECT filetypes,mimetypes FROM mime_types WHERE id=:id");
$stmt->bindValue(':id', $rows['mime_id'], PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
$ext = $rows['filetypes'];
$ctype = $rows['mimetypes'];
$filepath = $config['documentpath'] .  $filepath;
*/
if (file_exists($file)) {
    $filetype = mime_content_type($file);
    $ext = explode('.',$file); 
    $name = getDocumentName($id);
    $filename = $name.'.'.$ext[count($ext)-1];
//    echo $filetype;
//    exit;
    header('Content-Description: File Transfer');
    header('Content-Type: '.$filetype);
    header('Content-Disposition: attachment; filename="'.basename($filename).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);

    
    
    
} else {
    echo "File $filepath not found.";
}