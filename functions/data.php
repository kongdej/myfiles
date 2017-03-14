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

//$data->render_table("documents", "id", "name,refno,revise_date");
$data->render_table("contents", "id", "name,folder_id");
//$data->render_table("contents","id");

//$data->debugDumpParams();

//$data->render_sql("select * from contents c,folder f","c.id","c.id,c.name,c.folder_id","");

//$data->debugDumpParams();

//{ "data":[{"id":"51","name":"Visualizer ","refno":"1F.2","revise_date":"2015-04-01"}, "pos":0, "total_count":44}
/*
$uid = $_SESSION['uid'];
$folder_id = $_GET['folder_id'];

//list all permission folder only status_id is 1
$sql = "SELECT id,name,refno,revise_date FROM documents ";
$s=$db->prepare($sql);
$s->execute();
$count = $s->rowCount();
$data = $s->fetchAll();
$lists=array();
foreach($data as $row) {
        $row['name'] .= '-';
        //echo '>'.$row['refno'];
        //if ($row['refno'] != '') $row['refno']= '?';
        $lists[]=$row;  // send folder_id to hide (logic.js)
}
//print_r($folders);
$json=json_encode($lists);
echo '{ "data":'.$json.',"pos":0, "total_count":'.$count.'}';
*/
