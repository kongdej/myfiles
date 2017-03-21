<?php

//require_once("../common/init.php");
$config = parse_ini_file("config.ini", true);
getSettings();

$id = $_GET['id'];
$file = getDocumentPath($id);

if (file_exists($file)) {
    $filetype = mime_content_type($file);
    $ext = explode('.',$file); 
    $name = getDocumentName($id);
    $filename = $name.'.'.$ext[count($ext)-1];
    
    if ($filetype == 'application/pdf') {
        header("Content-Disposition: attachment; filename=" . basename($filename));    
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: ".$filetype );
        header("Content-Description: File Transfer");             
        header("Content-Length: " . filesize($file));
        flush(); // this doesn't really matter.

        $fp = fopen($file, "r"); 
        while (!feof($fp))
        {
            echo fread($fp, 65536); 
            flush(); // this is essential for large downloads
        }  
        fclose($fp); 
    }
    else {
        header('Location: '.$file);
    }
    
    logging('Download- '.$id.'- '.getDocumentName($id));
} else {
    echo "File $filepath not found.";
}