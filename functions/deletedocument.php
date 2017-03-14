<?php

$document_id = $_GET['id'];
$config = parse_ini_file("config.ini", true);
getSettings();

$query = $db->prepare("SELECT * FROM contents WHERE id='" . $document_id . "'");
$query->execute();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $created = $row['created'];   ///<-- check file directory 
    list($date,$_) = split(' ',$created);
    list($y,$m,$d) = split('-',$date);
    list($_,$dbname) = split('_',firstDB());
    $filename = "./sites/".$dbname."/documents/".$y.$m."/".$document_id.".*";  //<-- delete all 
    foreach(glob($filename) as $file) {
        if (file_exists($file)) {
            echo $file." file has deleted.";
            unlink($file);       
        }
    }
}       
$docname = getDocumentName($document_id);
$cnt_d=$db->exec("DELETE FROM contents WHERE id = '".$document_id."'");
logging('Delete-'.$document_id.'-'.$docname);

