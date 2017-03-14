<?php

$action = $_GET['action'];
$uid = $_SESSION['uid'];
$folder_id = $_GET['folder_id'];
switch ($action) {
    case 'update':
//        $folder_id = $_GET['folder_id'];
        $text = html_entity_decode($_POST['text']);
        $parent_id = $_POST['parent_id'];
        $orderfield = $_POST['orderfield'];
        $permission = $_POST['permission'];
        $stmt = $db->prepare("UPDATE folder SET text=:text,parent_id=:parent_id,orderfield=:orderfield WHERE id=:id");
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->bindParam(':orderfield', $orderfield);
        $stmt->bindParam(':id', $folder_id);
        $stmt->execute();
        if (!empty($permission)) {
            updateFolderPermission($folder_id, $permission);
        }
        echo '{"ret":true}';
        logging('Updatefolder-'.$text);
        break;
    case 'data':
        $sql = "SELECT id,text,parent_id,orderfield FROM folder WHERE id=" . $folder_id;
        $s = $db->prepare($sql);
        $s->execute();
        $data = $s->fetchAll();

        $data[0]['permission'] = getFolderPermission($folder_id);
        echo json_encode($data[0]);
        //echo '{ "data":'.$json.'}';
        break;
    case 'edit':
//        $folder_id = $_GET['folder_id'];
        $text = html_entity_decode($_GET['data']);
        $stmt = $db->prepare("UPDATE folder SET text=:text WHERE id=:id");
        $stmt->bindParam(':text', $text);
        $stmt->bindParam(':id', $folder_id);
        $stmt->execute();
        logging('Editfolder-'.$text);
        break;

    case 'move':
//     $folder_id see... above
        $parent_id = $_GET['parent_id'];
        $index = $_GET['index'];
        // update new index;
        $stmt = $db->prepare("UPDATE folder SET parent_id=:parent_id, orderfield=:index WHERE id=:id");
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->bindParam(':id', $folder_id);               
        $stmt->bindParam(':index', $index);
        $stmt->execute();
        logging('Movefolder-'.getFolderName($folder_id));

        // afer update new item then ordering items at parent_id
        $sql = "SELECT id,text,parent_id,orderfield FROM folder WHERE parent_id=" . $parent_id ."  ORDER BY orderfield,text";
        $s = $db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->bindParam(':id', $folder_id);               
        $s->execute();
        $data = $s->fetchAll();
        $i=0;
        $orderfield=0;
        foreach ($data as $row) {
            // if same orderfield, swap moved item and old item  
            if ($row['id'] != $folder_id && $i == $index) {
                $orderfield = $i++;
            }
            // skip for the moved item 
            if ($row['id'] == $folder_id) {
                continue;
            }
            $orderfield = $i;
            $stmt = $db->prepare("UPDATE folder SET orderfield=:index WHERE id=:id");
            $stmt->bindParam(':id', $row['id']);
            $stmt->bindParam(':index', $orderfield);
            $stmt->execute();
            $i++;
        }
        
//         $stmt->debugDumpParams();
        break;

    case 'add':
        $parent_id = $_GET['parent_id'];
        $last_folder_id = getLastId('folder') + 1;
        $text = "New folder";
        // echo 'add'.$parent_id.'-'.$last_folder_id;
        $stmt = $db->prepare("INSERT INTO folder (id,parent_id,text,orderfield) VALUES (:id, :parent_id,:text,:order)");
        $stmt->bindParam(':id', $last_folder_id);
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->bindParam(':text', $text);
        //$order = getFolderOrder($parent_id);
        $order = '';
        $stmt->bindParam(':order', $order);
        $stmt->execute();
        echo '{ "folder_id":"' . $last_folder_id . '"}';
        logging('Addfolder');

//        $stmt->debugDumpParams();
        break;

    case 'del':
//        $stmt = $db->prepare("UPDATE folder SET status_id=0 WHERE id=:id");
        $sql = "SELECT id FROM folder WHERE parent_id=" . $folder_id;
        $s = $db->prepare($sql);
        $s->execute();
        $data = $s->fetchAll();
        if (count($data)) {
            echo '{"msg":"Folder is not empty!"}';
        }
        else {
            logging('Deletefolder-'.getFolderName($folder_id));
            $stmt = $db->prepare("DELETE FROM folder WHERE id=:id");
            $stmt->bindParam(':id', $folder_id);
            $stmt->execute();
        }
//         $stmt->debugDumpParams();
        break;

    default:
        $data = new JSONTreeDataConnector($conn, "MySQL"); //initializes the connector object === modify data_connector.php to rootId = 3
        //$data->dynamic_loading(30);//enables dynamic loading 
//        $data->filter("status_id=1");
//        $data->filter("text <> 'New Document'");
        // folder permission
        if (isUser()) {
            $data->filter("id not in (select folder_id from folder_user_perm p  group by folder_id) or id in (select folder_id from folder_user_perm p where user_id = $uid group by folder_id)");
        }
        $data->sort("orderfield,text");
        $data->render_table("folder", "id", "text", "", "parent_id");
}

function getFolderOrder($parent_id) {
    global $db;
    $stmt = $db->prepare("select * from folder where parent_id =" . $parent_id . " order by orderfield");
    $stmt->execute();
    $data = $stmt->fetchAll();
    $count = 0;
    foreach ($data as $row) {
        $stmt2 = $db->prepare("update folder SET orderfield=" . $count . " WHERE id=" . $row['id']);
        $stmt2->execute();
        $count++;
    }
}

function getFolderPermission($folder_id) {
    global $db;
    $stmt = $db->prepare("select username from folder_user_perm p, users u where p.user_id=u.uid and p.folder_id=" . $folder_id);
    $stmt->execute();
    $data = $stmt->fetchAll();
    $lists = array();
    foreach ($data as $row) {
        $lists[] = $row['username'];
    }

    return join(',', $lists);
}

function updateFolderPermission($folder_id, $permission) {
    //update folder user permession   
    // delete all folder_id
    global $db;
    //echo '555';
    $stmt = $db->prepare("DELETE FROM folder_user_perm WHERE folder_id=:id");
    $stmt->bindParam(':id', $folder_id);
    $stmt->execute();
    //echo '666';
    // add user for permission list
    $lists = split(',', $permission);
    for ($i=0; $i<count($lists); $i++) {
        $stmt = $db->prepare("INSERT INTO folder_user_perm (folder_id,user_id) VALUES (:folder_id,:user_id)");
        $stmt->bindParam(':folder_id', $folder_id);
        $stmt->bindParam(':user_id', getUserId($lists[$i]));
        $stmt->execute();
    }
    //echo '777';
    
    return true;
}
