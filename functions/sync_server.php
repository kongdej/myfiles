<?php
$id = $_GET['id'];
define('DB_REMOTE_SERVER', '10.20.18.162');
define('DB_REMOTE_USERNAME', 'root');
define('DB_REMOTE_PASSWORD', 'rooter162');
define('DB_REMOTE_DATABASE', "myfiles_sbrp1");
define('REMOTE_DOCPATH', "../sites/sbrp1/documents/");

$db_remote = mysqli_connect(DB_REMOTE_SERVER,DB_REMOTE_USERNAME,DB_REMOTE_PASSWORD,DB_REMOTE_DATABASE);
if (!$db_remote) {
    die('Could not connect: ' . mysql_error());
}

mysqli_set_charset($db_remote,"utf8");

//== Table Contents ========================
$sql = "SELECT * FROM contents WHERE id=".$id;
$result = mysqli_query($db_remote,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
$created = $row['created'];

list($date,$_) = split(' ',$created);
list($y,$m,$d) = split('-',$date);
$filename = REMOTE_DOCPATH.$y.$m."/".$id.".*";  // file all
//echo $filename;
foreach(glob($filename) as $file) {
//    echo $file;
    if (file_exists($file)) {
        $tok = explode('/', $file);
        $data['file']=$tok[count($tok)-1];
        echo json_encode($data);
    }
}  