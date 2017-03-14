<?php
// May be delete ** use upload.php instead.
$config = parse_ini_file("config.ini", true);
getSettings();

$document_id = $_POST['id'];
/*
print_r($_GET);
print_r($_POST);
print_r($_FILES);

exit;
*/
// insert documents table
$config = parse_ini_file("config.ini", true);
getSettings();
date_default_timezone_set('Asia/Bangkok');
$now = date('Y-m-d H:i:s');
$datenow = date('Y-m-d');
$stmt = $db->prepare("INSERT INTO documents (id,name,folder_id,revise_date) VALUES (:id, :name, :folder_id, :revise_date)");
$last_document_id = getLastId('documents') + 1;
$stmt->bindParam(':id', $last_document_id);
$stmt->bindParam(':name', $_POST["name"]);
$stmt->bindParam(':folder_id', $_POST["folder_id"]);
$stmt->bindParam(':revise_date', $_POST["revise_date"]);

$stmt->execute();
//$stmt->debugDumpParams();
// insert document_version table
$stmt = $db->prepare("INSERT INTO document_version (id,document_id,creator_id,filename,filepath,mime_id,created,modified) VALUES (:id, :document_id, :creator_id, :filename, :filepath, :mime_id,:now,:now)");
$id = getLastId('document_version') + 1;
$stmt->bindParam(':id', $id);
$stmt->bindParam(':document_id', $last_document_id);
$stmt->bindParam(':creator_id', $_SESSION['uid']);
$stmt->bindParam(':filename', $_GET['filename']);
$file_info = new finfo(FILEINFO_MIME);  // object oriented approach!
$mime_type = $file_info->buffer(file_get_contents($file["tmp_name"]));  // e.g. gives "image/jpeg"
$mime = explode(';', $mime_type);
$mime_id = getMime($mime[0]);
$filename = getFilePath($id, $file['name'], $mime_id);
$stmt->bindParam(':filepath', $filename);
$stmt->bindParam(':mime_id', $mime_id);

$stmt->bindParam(':now', $now);

$stmt->execute();
//$stmt->debugDumpParams();
