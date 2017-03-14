<?php
$file = $_GET['file'];
$filetype = $_GET['type'];
if (file_exists($file)) {
/*
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
 * 
 */
 $ids = explode('/',$file);
 list($id,$_) = explode('.',$ids[count($ids)-1]);
 
 ?>
    <div style="margin:auto;text-align:center;">
        <div style="margin-top: 80px">
            Warning! document format cannot view,<br><br>
            CLICK TO DOWNLOAD.
        </div>        
        <a href="index.php?m=download&id=<?php echo $id;?>">
            <img src="images/download.png" style="width:150px;margin-top: 10px">
        </a>
    </div>
<?php
    //   echo "Warning! Cannot view this document format. Please click download button bellow to download.";
}
else {
    echo "File is not found.";
}
