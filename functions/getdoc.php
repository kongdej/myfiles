<?php
$id = $_GET['id'];

$query = $db->prepare("SELECT * FROM contents WHERE id='" . $id . "'");
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);
list($date,$time) = explode(' ',$row['created']);  // find directory from created
list($y,$m,$d) = explode('-',$date);
$datedir = $y.$m;

list($_,$site) = split('_',$_SESSION['database']);
$path =  'sites/'.$site.'/documents/'.$datedir;

$listfiles = $path.'/'.$id.'*';
//echo $listfiles;
$files = glob($listfiles);

if (!count($files)) {
    $filepath = $config['var']['missing_file'];
    $filetype='html';
}
else {
    usort($files, create_function('$a,$b', 'return $a<$b;'));
    $filepath = $files[0];
    $filetype = mime_content_type($filepath);
//    $finfo = new finfo(FILEINFO_MIME);
//    $type = $finfo->file($filepath);
//    $filetype = finfo_file($finfo, $filename);
//    print_r($type);
}

echo '{"filepath":"'.$filepath.'","filetype":"'.$filetype.'"}';
logging('Read-'.$id.'- '.getDocumentName($id));


