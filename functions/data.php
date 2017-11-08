<?php

$uid = $_SESSION['uid'];
$folder_id = $_GET['folder_id'];

$data = new JSONDataConnector($conn, $dbtype);
$data->set_encoding("utf8");

//select folder
if ($folder_id) {
    $data->filter("folder_id=" . $folder_id);
}

// search ===
// simple search
if (!empty($_GET['s'])) {
    $search_text = html_entity_decode($_GET['s'], null, 'UTF-8');
    $data->filter("name like \"%" . $search_text . "%\" OR refno like \"%" . $search_text . "%\" OR id like \"%" . $search_text . "%\"");
    logging('Search-'.$_GET['s']);
}
// advanced search
else if (!empty($_GET['advs'])) { // advanced search
    $advs = json_decode($_GET['advs'],"TRUE");
    if (!empty($advs['docno'])) {
        $data->filter("id like '%".$advs['docno']."%'");    
    }
    if (!empty($advs['title'])) {
        $advs['title']=html_entity_decode($advs['title'], null, 'UTF-8');
        $data->filter("name like '%".$advs['title']."%'");    
    }
    if (!empty($advs['refno'])) {
        $advs['refno']=html_entity_decode($advs['refno'], null, 'UTF-8');
        $data->filter("refno like '%".$advs['refno']."%'");    
    }
    if (!empty($advs['keyword'])) {
        $advs['keyword']=html_entity_decode($advs['keyword'], null, 'UTF-8');
        $data->filter("keyword like '%".$advs['keyword']."%'");    
    }
    if (!empty($advs['start'])) {
        $data->filter("revise_date >= '".$advs['start']."'");    
    }
    if (!empty($advs['end'])) {
        $data->filter("revise_date <= '".$advs['end']."'");    
    }
    
    // sort and direction
    $sort = $advs['sortby'].' '.$advs['dir'];
    $data->sort($sort);    
}

// order by
if (!empty($_GET['srt'])){
   list($field,$dir) = split('\.', $_GET['srt']);
   $data->sort($field.' '.$dir);   
}
else {
    $data->sort("id DESC");
}

// permission check 
$data->filter("folder_id not in (select folder_id from folder_user_perm p  group by folder_id) or folder_id in (select folder_id from folder_user_perm p where user_id = $uid group by folder_id)");

header('Content-Type: text/html; charset=utf-8');
$data->dynamic_loading(50);

// build folder format
function formatting($row){
    global $db;
    global $folderpaths;
    
    $folder_id =$row->get_value("folder_id"); 
    $folderpaths = array();
    getParentTree($folder_id, $db);
    $folderpaths = array_reverse($folderpaths);
    $folderpath_str = join(' > ', $folderpaths);

    $row->set_value("path",$folderpath_str);
}
 
$data->mix("path", "");
$data->event->attach("beforeRender","formatting");

//$data->render_table("documents", "id", "name,refno,revise_date");
$data->render_table("contents", "id", "name,refno,revise_date,folder_id");

