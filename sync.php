<?php
define('DB_LOCAL_SERVER', 'localhost');
define('DB_LOCAL_USERNAME', 'root');
define('DB_LOCAL_PASSWORD', '');
define('DB_LOCAL_DATABASE', "myfiles_sbrp1");
define('LOCAL_DOCPATH', "sites/sbrp1/documents/");

define('DB_REMOTE_SERVER', '10.20.18.162');
define('DB_REMOTE_USERNAME', 'root');
define('DB_REMOTE_PASSWORD', 'rooter162');
define('DB_REMOTE_DATABASE', "myfiles_sbrp1");
define('REMOTE_DOCPATH', "http://myfiles.egat.co.th/sbrp1/sites/sbrp1/documents/");
define('REMOTE_SERVER', "http://myfiles.egat.co.th/sbrp1/functions/sync_server.php");

$db_local = mysqli_connect(DB_LOCAL_SERVER,DB_LOCAL_USERNAME,DB_LOCAL_PASSWORD,DB_LOCAL_DATABASE);
if (!$db_local) {
    die('Could not connect: ' . mysql_error());
}
$db_remote = mysqli_connect(DB_REMOTE_SERVER,DB_REMOTE_USERNAME,DB_REMOTE_PASSWORD,DB_REMOTE_DATABASE);
if (!$db_remote) {
    die('Could not connect: ' . mysql_error());
}

mysqli_set_charset($db_local,"utf8");
mysqli_set_charset($db_remote,"utf8");

//== Table Contents ========================
$sql = "SELECT max(modified) 'max' FROM contents";
$result = mysqli_query($db_local,$sql);
$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
$lastmodified =  $row['max'];
echo 'LOCAL: LastModified = '.$lastmodified."\n";
$sql = "SELECT * FROM contents WHERE modified > '".$lastmodified."'";
$result = mysqli_query($db_remote,$sql);
while($row = mysqli_fetch_array($result,MYSQLI_ASSOC)){
	$sql = "INSERT INTO contents (id,name,revise_date,refno,keyword,description,folder_id,creator_id,created,modified)";
	$sql.= " VALUES (";
	$sql.= "'".$row['id']."',";
	$sql.= "'".$row['name']."',";
	$sql.= "'".$row['revise_date']."',";
	$sql.= "'".$row['refno']."',";
	$sql.= "'".$row['keyword']."',";
	$sql.= "'".$row['description']."',";
	$sql.= "'".$row['folder_id']."',";
	$sql.= "'".$row['creator_id']."',";
	$sql.= "'".$row['created']."',";
	$sql.= "'".$row['modified']."'";
	$sql.= ")";
	//echo $sql."\n";	
        //echo REMOTE_SERVER."?id=".$row['id'];
        $dir = getDocumentPath($row['created']);
        
        // create local directory monyear
        $path = LOCAL_DOCPATH.$dir;
        if (!file_exists($path)) {
            mkdir($path, 0777,true);
        }
        $request = file_get_contents(REMOTE_SERVER."?id=".$row['id']);
        $params = json_decode($request,true); // true for return as array        
        $remotepath = REMOTE_DOCPATH.$dir.'/'.$params['file'];
        $localpath = LOCAL_DOCPATH.$dir.'/'.$params['file'];
        echo $remotepath."=>".$localpath."\n";
            if(!@copy($remotepath,$localpath)){
                $errors= error_get_last();
                echo "Err:".$errors['message'];
        }
	mysqli_query($db_local,$sql);
}      

function getDocumentPath($created) {
    list($date,$_) = split(' ',$created);
    list($y,$m,$d) = split('-',$date);
    
    return $y.$m;
}