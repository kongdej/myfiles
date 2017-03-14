<?php
$directory = '../codebase/skins/';
$scanned_directory = array_diff(scandir($directory), array('..', '.','fonts','debug'));

$json = Array();
foreach ($scanned_directory as $id => $value) {
        $theme = str_replace('.css', '', $value);
	$json[] = Array("id"=> $theme,"value"=>$value);	
}
echo json_encode($json);
