<?php
// create config.php file
$config = "[database]\n";
$config .= "data_type=\"MySQL\"\n";
$config .= "host=\"".$_POST['dbhost']."\"\n";
$config .= "user=\"".$_POST['dbusername']."\"\n";
$config .= "host=\"".$_POST['dbpassword']."\"\n";
$config .= "default_database=\"".$_POST['dbname']."\"\n\n";
$config .= "[var]\n";
$config .= "loginegat=\"true\"\n";
$config .= "loginlocal=\"true\"\n";

if (file_exists("config.php")) {
	rename("config.php","config-old.php");
}

$fileconfig = fopen("config.php", "w") or die("Unable to open file!");
fwrite($fileconfig, $config);
fclose($fileconfig);

// create directory
$dirname = $_POST['docpath'];
if (!file_exists($dirname)) {
    mkdir($dirname, 0777, true);
    $ok .=  "The directory $dirname was successfully created.<br>\n";
} else {
    $err .= "The directory $dirname exists.<br>\n";
}


// database
$link = mysql_connect($_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword']);
if (!$link) {
    $err .= "Could not connect: " . mysql_error()."<br>\n";
}
else {
	$sql = 'CREATE DATABASE '.$_POST['dbname'].' CHARACTER SET utf8 COLLATE utf8_general_ci';
	if (mysql_query($sql, $link)) {
	    $ok .= "Database my_db created successfully<br>\n";
		
		mysql_select_db($_POST['dbname'], $link) or die( $err .= mysql_error());
		
		$sqlSource = file_get_contents('sql/myfiles.sql');
		$query_array = explode(';', $sqlSource);
		foreach ($query_array as $k=>$v) {
			$v = str_replace(array("\r\n","\r"),"",$v);
			if(!mysql_query($v, $link)) {
				$err .=  'Error creating tables: '.mysql_error();
			}
		}
		
		// insert setting
		$sql  = "INSERT INTO system_settings (id, name, value) VALUES ";
		$sql .= "(1, 'site_name', '".$_POST['sitecode']."'),";
		$sql .= "(2, 'cur_theme', '".$_POST['theme']."'),";
		$sql .= "(3, 'title', '".$_POST['title']."'),";
		$sql .= "(4, 'copyright', '".$_POST['copyright']."'),";
		$sql .= "(5, 'documentpath', '".$_POST['docpath']."')";
		echo $sql;
		if(!mysql_query($sql, $link)) {
			$err .=  'Error creating tables: '.mysql_error();
		}

	} 
	else {
	    $err .= 'Error creating database: ' . mysql_error() . "\n";
	}
}

if ($err) {
	$res = array("status" => "err", "sname" => $err);    
}
else {
	$res = array("status" => "ok", "sname" => $ok);    	
}
//echo $err;
echo json_encode($res);


