<?php
$stmt = $db->prepare("SELECT uid,email,name,username FROM users ORDER BY email");
$stmt->execute();
$emails = array();
while($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $emails[]=array('id' => $rows['uid'], 'value' => $rows['email'], 'name' => $rows['name'], 'username' => $rows['username']);
}
        
/*
while ($rows = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    $data = $row[0] . "\t" . $row[1] . "\t" . $row[2] . "\n";
    print $data;
}
*/

$filter = isset($_GET["filter"]) ? $_GET["filter"]["value"] : false;
$json = Array();
$max = 0;
for ($i = 0; $i < count($emails); $i++) {
    if ($filter===false || $filter === "" 
            || strpos(strtolower($emails[$i]["value"]), strtolower($filter)) === 0 
            || strpos(strtolower($emails[$i]["name"]), strtolower($filter)) === 0
            || strpos(strtolower($emails[$i]["username"]), strtolower($filter)) === 0
        ) 
    {
        $json[] = Array("id"=> $emails[$i]["id"],"value"=>$emails[$i]["name"].' <'.$emails[$i]["value"].'>' );
        if (++$max >= 10) break;
    }
}


echo json_encode($json);
