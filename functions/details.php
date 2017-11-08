<?php
// maybe delete
$id = $_GET['id'];
$folder_id = getFolderId($id);

$folderpaths = array();
getParentTree($folder_id, $db);
$folderpaths = array_reverse($folderpaths);
$folderpath_str = join(' > ', $folderpaths);

$title=  getDocumentName($id);
//$revise_date = getDocumentReviseDate($id);
$documents = getDocument($id);
$documents =$documents[0];

$msg = "<b>".$documents['name']."</b><hr>";
$msg .="<table>";
$msg .="<tr valign=top><td align=left><b>Folder:</b></td><td align=left>".$folderpath_str."</td></tr>";
$msg .="<tr valign=top><td align=left><b>Date:</b></td><td align=left>".$documents['revise_date']."</td></tr>";
$msg .="<tr valign=top><td align=left><b>Ref No.</b></td><td align=left>".$documents['refno']."</td></tr>";
$msg .="<tr valign=top><td align=left><b>Keyword:</b></td><td align=left>".$documents['keyword']."</td></tr>";
$msg .="</table>";


$res = array("status" => "ok",title=>$title, "res" => $msg);    
echo json_encode($res);
