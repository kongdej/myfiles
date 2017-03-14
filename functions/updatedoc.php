<?php
// ==== delete ====
$config = parse_ini_file("config.ini", true);
getSettings();

$document_id = $_POST['id'];

print_r($_GET);
print_r($_POST);
print_r($_FILES);

exit;

$name = html_entity_decode($_POST['name']);
$refno = html_entity_decode($_POST['refno']);
$keyword = html_entity_decode($_POST['keyword']);
$revise_date = $_POST['revise_date'];
list($revise_date,$_)=  explode(' ', $revise_date);

date_default_timezone_set('Asia/Bangkok');
$now = date('Y-m-d H:i:s');
$datenow = date('Y-m-d');

$stmt = $db->prepare("UPDATE documents SET name=:name,refno=:refno,revise_date=:revise_date,keyword=:keyword WHERE id=:id");
$stmt->bindParam(':name', $name);
$stmt->bindParam(':refno', $refno);
$stmt->bindParam(':revise_date', $revise_date);
$stmt->bindParam(':keyword', $keyword);
$stmt->bindParam(':id', $document_id);
$stmt->execute();


// update documents_version
$stmt = $db->prepare("UPDATE document_version SET modified=:now WHERE document_id=:id");
$stmt->bindParam(':now', $now);
$stmt->bindParam(':id', $document_id);
$stmt->execute();

