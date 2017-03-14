<?php

ini_set('max_execution_time', 120);
//$size = 256*256;
//$destination = realpath('./files');
if (isset($_FILES['upload'])) {
   $file = $_FILES['upload'];
   if (!empty($file['name'])) {
     list($_,$site) = split('_',$_SESSION['database']);   // get path from name of database
     $mon_now = date('Ym');
     $path = 'sites/'.$site.'/documents/'.$mon_now;       // check and create directory
     if (!file_exists($path)) {
         mkdir($path, 0777,true);
     }
     $ext = explode('.',$file['name']);                   // get extension
     if ($_GET['action'] == "changefile") {
         $id = $_GET['id'];
         list($_,$document_id) = split('-',$id);
         $oldfile = getDocumentPath($document_id);
         if (file_exists($oldfile)) {
             unlink($oldfile);
         }
         $path = getPath($document_id);
     }
     else {
         $document_id = getLastId('contents');
    }
    $filename = $document_id.'.'.$ext[count($ext)-1]; // rename to id of document with ole extension  
    $uploadfiles = $path.'/'.$filename;
    move_uploaded_file($file["tmp_name"], $uploadfiles);
    $res = array("status" => "server", "sname" => $file['name']);    
    echo json_encode($res);
    logging('Upload-'.$file['name']);
   }
 
}

if ($_POST['action'] == 'add' || $_GET['action'] == 'add') {
    $folder_id = $_POST["folder_id"];
    $name = $_POST["name"];
    $refno = $_POST["refno"];
    $revise_date = $_POST["revise_date"];
    list($revise_date, $_) = explode(' ', $revise_date);
    $keyword = $_POST["keyword"];
    $description = $_POST["description"];
    $sname = '';
    //
    // insert documents table
    $config = parse_ini_file("config.ini", true);
    getSettings();
    date_default_timezone_set('Asia/Bangkok');
    $now = date('Y-m-d H:i:s');
    if (empty($revise_date)) {
        $revise_date = date('Y-m-d');
    }
    
    if (empty($name)) {
        if (!empty($file['name'])) {
            $name = $file['name'];
        }
        else {
            $name = 'no title';
        }
    }
    
    $stmt = $db->prepare("INSERT INTO contents (id,name,refno,keyword,description,folder_id,revise_date,creator_id,created,modified) VALUES ( :id, :name,:refno,:keyword,:description, :folder_id, :revise_date,:creator_id,:created,:modified)");
    $last_document_id = getLastId('contents') + 1;
    $stmt->bindParam(':id', $last_document_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':refno', $refno);
    $stmt->bindParam(':keyword', $keyword);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':folder_id', $folder_id);
    $stmt->bindParam(':revise_date', $revise_date);
    $stmt->bindParam(':creator_id', $_SESSION['uid']); 
    $stmt->bindParam(':created', $now); 
    $stmt->bindParam(':modified', $now); 
    $stmt->execute();  
    logging('Add-'.$name);
} else if ($_GET['action'] == 'edit' || $_POST['action'] == 'edit') {
    list($_,$document_id) = explode('-',$_POST['id']);
    $name = html_entity_decode($_POST['name']);
    $refno = html_entity_decode($_POST['refno']);
    $keyword = html_entity_decode($_POST['keyword']);
    $description = html_entity_decode($_POST['description']);
    $folder_id = $_POST['folder_id'];
    $revise_date = $_POST['revise_date'];
    list($revise_date, $_) = explode(' ', $revise_date);

    date_default_timezone_set('Asia/Bangkok');
    $now = date('Y-m-d H:i:s');
    $datenow = date('Y-m-d');

    $stmt = $db->prepare("UPDATE contents SET name=:name,refno=:refno,revise_date=:revise_date,keyword=:keyword,description=:description,folder_id=:folder_id, modified=:modified, creator_id=:creator_id WHERE id=:id");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':refno', $refno);
    $stmt->bindParam(':revise_date', $revise_date);
    $stmt->bindParam(':keyword', $keyword);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':folder_id', $folder_id);
    $stmt->bindParam(':modified', $now);
    $stmt->bindParam(':creator_id', $_SESSION['uid']); 
    $stmt->bindParam(':id', $document_id);
    $stmt->execute();
    logging('Edit-'.$name);
    //   $stmt->debugDumpParams();    
} 

function getFilePath($id, $filename, $mime) {
    global $db, $config;

    $uploadDir = getUploadDir();
    $filepath = $uploadDir . '/' . $config['org'] . '_' . $id . '.';
    if (getFileExt($mime)) {
        $filepath .= getFileExt($mime);
    } else {
        $tokens = split('\.', $filename);
        $ext = $tokens[count($tokens) - 1];
        $filepath .= $ext;
    }

    return $filepath;
}

function getUploadDir() {
    global $config;

    $mon_now = date('Ym');
    $dir = $config['documentpath'] . 'documents/' . $mon_now;
    $reldir = 'documents/' . $mon_now;
    if (file_exists($dir) && is_dir($dir)) {
        return $reldir;
    } else {
        mkdir($dir);
        return $reldir;
    }
}

function getMime($type) {
    global $db;

    $s = $db->prepare("SELECT id FROM mime_types WHERE mimetypes='" . $type . "' ORDER BY id desc LIMIT 0,1");
    $s->execute();
    return $s->fetchColumn();
}

function getFileExt($id) {
    global $db;

    $s = $db->prepare("SELECT filetypes FROM mime_types WHERE id='" . $id . "' ORDER BY id desc LIMIT 0,1");
    $s->execute();
    return $s->fetchColumn();
}
