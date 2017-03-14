<?php

if(count($argv) != 3) {
	echo "Usage:\nupgrade.php <source database name> <new database name>";
	exit;
}


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE_SRC', $argv[1]);
define('DB_DATABASE_DST', $argv[2]);

$db_src = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE_SRC);
if (!$db_src) {
    die('Could not connect: ' . mysql_error());
}
$db_dst = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE_DST);
if (!$db_dst) {
    die('Could not connect: ' . mysql_error());
}

mysqli_set_charset($db_src,"utf8");
mysqli_set_charset($db_dst,"utf8");

//== Table Users ========================
$sql_src = "SELECT username,name,password,email FROM users WHERE username NOT IN ('admin','docadmin','system')";
$result_src = mysqli_query($db_src,$sql_src);
while($row_src = mysqli_fetch_array($result_src,MYSQLI_ASSOC)){
	$sql_dst = "INSERT INTO users (username,name,password,email) VALUES ('".$row_src['username']."','".$row_src['name']."','".$row_src['password']."','".$row_src['email']."')";
	mysqli_query($db_dst,$sql_dst);
	echo $sql_dst."\n";	
}      

//== Table Contents ========================
$sql_src = "SELECT d.name,d.revise_date,d.refno,d.keyword,d.description,d.folder_id,";
$sql_src.= " v.creator_id,v.created,v.modified";
$sql_src.= " FROM documents d, document_version v";
$sql_src.= " WHERE d.id = v.document_id";
echo $sql_src;

$result_src = mysqli_query($db_src,$sql_src);
while($row_src = mysqli_fetch_array($result_src,MYSQLI_ASSOC)){
	$sql_dst = "INSERT INTO contents (name,revise_date,refno,keyword,description,folder_id,creator_id,created,modified)";
	$sql_dst.= " VALUES (";
	$sql_dst.= "'".$row_src['name']."',";
	$sql_dst.= "'".$row_src['revise_date']."',";
	$sql_dst.= "'".$row_src['refno']."',";
	$sql_dst.= "'".$row_src['keyword']."',";
	$sql_dst.= "'".$row_src['description']."',";
	$sql_dst.= "'".$row_src['folder_id']."',";
	$sql_dst.= "'".$row_src['creator_id']."',";
	$sql_dst.= "'".$row_src['created']."',";
	$sql_dst.= "'".$row_src['modified']."'";
	$sql_dst.= ")";
	echo $sql_dst."\n";	
	mysqli_query($db_dst,$sql_dst);
}      

//== Table Folders ========================
$sql_src = "SELECT * FROM folder WHERE status_id=1";
echo $sql_src;

$result_src = mysqli_query($db_src,$sql_src);
while($row_src = mysqli_fetch_array($result_src,MYSQLI_ASSOC)){
	$sql_dst = "INSERT INTO folder (id,parent_id,text,orderfield,expanded)";
	$sql_dst.= " VALUES (";
	$sql_dst.= "'".$row_src['id']."',";
	$sql_dst.= "'".$row_src['parent_id']."',";
	$sql_dst.= "'".$row_src['text']."',";
	$sql_dst.= "'".$row_src['orderfield']."',";
	$sql_dst.= "'".$row_src['expanded']."'";
	$sql_dst.= ")";
	echo $sql_dst."\n";	
	mysqli_query($db_dst,$sql_dst);
}      

$sql = "UPDATE folder SET parent_id='0' WHERE parent_id='1'";
mysqli_query($db_dst,$sql);
