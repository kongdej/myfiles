<?php
/*
$db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $passwod . '');

$query = "SHOW DATABASES";
$sql = $db->prepare($query);
$sql->execute();
$data = $sql->fetchAll();
//print_r($data);

foreach ($data as $rows) {
    if (ereg('myfiles',$rows['Database']))
        $value[]="{id:\"".$rows['Database']."\",value:\"".$rows['Database']."\"}";
}
$r = join(',',$value);
echo "[".$r."]";
*/
$config = parse_ini_file("config.ini", true);
foreach ($config['site'] as $db=>$name) {
        $value[]="{\"id\":\"".$db."\",\"value\":\"".$name."\"}";
}
$r = join(',',$value);
echo "[".$r."]";
