<?php

$action = $_GET['action'];
//$uid = $_SESSION['uid'];  // for hacker
$uid = $_GET['uid'];
$folder_id = $_GET['folder_id'];
switch ($action) {
    case 'add':
        $sql = "INSERT INTO folder_user_perm (folder_id,user_id) VALUES (".$folder_id.",".$uid.")";
        $s = $db->prepare($sql);
        $s->execute();
        break;
    case 'del':
        $sql = "DELETE FROM folder_user_perm WHERE folder_id=".$folder_id." AND user_id=".$uid;
        $s = $db->prepare($sql);
        $s->execute();
        break;

    case 'listtarget':
        $sql = "SELECT p.folder_id as 'folder_id',u.uid as uid, u.name as 'name' FROM folder_user_perm p,users u WHERE  position <> 'Admin' AND u.uid=p.user_id AND folder_id=".$folder_id;
        $s = $db->prepare($sql);
        $s->execute();
        $data = $s->fetchAll();
        echo json_encode($data);        
        break;
    
    case 'listsource':
        $sql = "SELECT uid,name 'name' FROM users WHERE position <> 'Admin' AND uid not in (SELECT user_id from folder_user_perm WHERE folder_id=".$folder_id.")";
        $s = $db->prepare($sql);
        $s->execute();
        $data = $s->fetchAll();
        echo json_encode($data);        
        break;
        
}

