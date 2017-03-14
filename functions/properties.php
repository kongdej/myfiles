<?php
$data = new JSONDataConnector($conn, $dbtype);
$data->dynamic_loading(25);
$id = $_GET['id'];
$data->filter("id", $id);


//$db = new PDO('mysql:host=localhost;dbname='.$database.';charset=utf8', ''.$user.'', ''.$passwod.'');
$stmt = $db->prepare("SELECT creator_id FROM contents WHERE id=:id ORDER BY modified DESC");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
//$data->mix("created", $rows['created']);
//$data->mix("modified", $rows['modified']);


$stmt = $db->prepare("SELECT name FROM users WHERE uid=:cid");
$stmt->bindValue(':cid', $rows['creator_id'], PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
$data->mix("creator", $rows['name']);

$stmt = $db->prepare("SELECT folder_id FROM contents WHERE id=:id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);

global $folderpaths;
$folderpaths = array();
//$folderpaths[] = ">>".  getFolderName($rows['folder_id']);
getParentTree($rows['folder_id'], $db);
$folderpaths = array_reverse($folderpaths);
$folderpath_str = join(' > ', $folderpaths);
//print_r($folderpath_str);
$data->mix("folder", $folderpath_str);
$data->mix("folder_id", $rows['folder_id']);
$filename = getDocumentPath($id);
$data->mix("filename", $filename);

/*
$stmt = $db->prepare("SELECT filepath,filename FROM document_version WHERE document_id=:id order by revision DESC");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
$data->mix("filepath", $rows['filepath']);
$data->mix("filename", $rows['filename']);
*/

//$data->render_table("documents", "id,name,revise_date,refno,keyword,description");
$data->render_table("contents", "id,name,revise_date,refno,keyword,description,created,modified");

/*
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
}
*/