<?php
$folder_id = $_GET['id'];

$folderpaths = array();
getParentTree($folder_id, $db);
$folderpaths = array_reverse($folderpaths);
$folderpath_str = join(' > ', $folderpaths);

$res = array("status" => "ok", "res" => $folderpath_str);    
echo json_encode($res);
