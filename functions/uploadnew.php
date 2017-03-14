<?php
/// <== delete
ini_set('max_execution_time', 120);
//$size = 256*256;
print_r($_GET);
print_r($_POST);
print_r($_FILES);

exit;

$destination = realpath('./files');
if (isset($_FILES['upload'])) {
    $file = $_FILES['upload'];

    $filename = $destination . "/" . preg_replace("|[\\\/]|", "", $file["name"]);
    $sname = $folder_id = $_POST["key"];
    
    //
    //check that file name is valid
    if ($filename != "" && !file_exists($filename)) {
        // insert documents table
        $config = parse_ini_file("config.ini", true);
        getSettings();
        date_default_timezone_set('Asia/Bangkok');
        $now = date('Y-m-d H:i:s');
        $datenow = date('Y-m-d');
        $stmt = $db->prepare("INSERT INTO documents (id,name,folder_id,revise_date) VALUES (:id, :name, :folder_id, :revise_date)");
        $last_document_id = getLastId('documents')+1;
        $stmt->bindParam(':id', $last_document_id);
        $stmt->bindParam(':name', $file["name"]);
        $stmt->bindParam(':folder_id', $folder_id);
        $stmt->bindParam(':revise_date', $datenow);

        $stmt->execute();
        //$stmt->debugDumpParams();

        // insert document_version table
        $stmt = $db->prepare("INSERT INTO document_version (id,document_id,creator_id,filename,filepath,mime_id,created,modified) VALUES (:id, :document_id, :creator_id, :filename, :filepath, :mime_id,:now,:now)");
        $id = getLastId('document_version')+1;
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':document_id', $last_document_id);
        $stmt->bindParam(':creator_id', $_SESSION['uid']);
        $stmt->bindParam(':filename', $file['name']);
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

        // move file to destination folder
        move_uploaded_file($file["tmp_name"], $config['documentpath'].$filename);


        echo "{ status: 'server', sname:'folder id : $sname'}";
    } else {
        echo "{ status:'error' ,sname:'found already that file.' }";
    }
}

function getFilePath($id, $filename, $mime) {
    global $db,$config;
    
    $uploadDir = getUploadDir();
    $filepath = $uploadDir.'/'.$config['org'].'_'.$id.'.';
    if (getFileExt($mime)) {
        $filepath .= getFileExt($mime);
    }
    else {
        $tokens = split('\.',$filename);
        $ext = $tokens[count($tokens)-1];
        $filepath .= $ext;
    }
    
    return $filepath;
}

function getUploadDir() {
	global $config;

	$mon_now=date('Ym');
	$dir = $config['documentpath'].'documents/'.$mon_now;
	$reldir = 'documents/'.$mon_now;
	if (file_exists($dir) && is_dir($dir)) {
		return $reldir;
	}
	else {
		mkdir($dir);
		return $reldir;
	}
}

function getLastId($table) {
    global $db;

    $lid = $db->prepare("SELECT MAX(id) FROM " . $table);
    $lid->execute();
    $lastid = $lid->fetchColumn();

    return $lastid;
}

function getMime($type) {
    global $db;

    $s = $db->prepare("SELECT id FROM mime_types WHERE mimetypes='" . $type."' ORDER BY id desc LIMIT 0,1");
    $s->execute();
    return $s->fetchColumn();
}

function getFileExt($id) {
    global $db;

    $s = $db->prepare("SELECT filetypes FROM mime_types WHERE id='" . $id."' ORDER BY id desc LIMIT 0,1");
    $s->execute();
    return $s->fetchColumn();
}