<?php

$uid = $_SESSION['uid'];
$action = $_GET['action'];

//if ($action == 'list') {
//     echo '{ "data":[{"id":"54","text":"test upload","refno":"","revise_date":"2015-04-17"},{"id":"51","name":"Visualizer ","refno":"1F.2","revise_date":"2015-04-01"},{"id":"49","name":"02_Latitude_7000_Series_spec_sheet.pdf","refno":"","revise_date":"2015-04-08"},{"id":"48","name":"Dell Latiude 700011111","refno":"111111","revise_date":"2015-04-08"},{"id":"47","name":"EOS 700D","refno":"","revise_date":"2015-04-08"},{"id":"46","name":"3G Router","refno":"","revise_date":"2015-04-08"},{"id":"45","name":"04_Dell-OptiPlex-3020-Spec-Sheet.pdf","refno":"","revise_date":"2015-04-08"},{"id":"44","name":"03_Latitude_3000_Series_Spec_Sheet.pdf","refno":"","revise_date":"2015-04-08"},{"id":"43","name":"02_Latitude_7000_Series_spec_sheet.pdf","refno":"","revise_date":"2015-04-08"},{"id":"42","name":"13_Camera Canon_IXUS160.pdf","refno":"","revise_date":"2015-04-08"},{"id":"41","name":"10_Epson EB-X03-Datasheet.pdf","refno":"","revise_date":"2015-04-08"},{"id":"40","name":"02_Latitude_7000_Series_spec_sheet.pdf","refno":"","revise_date":"2015-04-08"},{"id":"39","name":"09_Brother Scanner ADS-1600W.pdf","refno":"","revise_date":"2015-04-08"},{"id":"38","name":"04_Dell-OptiPlex-3020-Spec-Sheet.pdf","refno":"","revise_date":"2015-04-08"},{"id":"37","name":"11_PanasonicTV TH-42A410.pdf","refno":"","revise_date":"2015-04-08"},{"id":"36","name":"this is a book","refno":"","revise_date":"2015-04-08"},{"id":"34","name":"08_Cannon PIXMA PRO-100.pdf","refno":"","revise_date":"2015-04-08"},{"id":"33","name":"tttttt","refno":"","revise_date":"2015-04-08"},{"id":"32","name":"cccc","refno":"","revise_date":"2015-04-08"},{"id":"31","name":"qqqqq","refno":"","revise_date":"2015-04-08"},{"id":"30","name":"xxxx","refno":"","revise_date":"2015-04-08"},{"id":"29","name":"sssss","refno":"","revise_date":"2015-04-08"},{"id":"28","name":"sdfsdf","refno":"","revise_date":"2015-04-08"},{"id":"27","name":"bbbb","refno":"","revise_date":"2015-04-08"},{"id":"26","name":"zzzzz","refno":"","revise_date":"2015-04-08"},{"id":"25","name":"xcxc","refno":"","revise_date":"2015-04-08"},{"id":"24","name":"tehsi","refno":"fsd","revise_date":"2015-04-08"},{"id":"23","name":"love you","refno":"EGAT 1\/2015","revise_date":"2015-04-07"},{"id":"22","name":"999","refno":"fsadf","revise_date":"2015-04-07"},{"id":"21","name":"yyy","refno":"fff","revise_date":"2015-04-01"},{"id":"20","name":"xxxx","refno":"xxxx","revise_date":"2015-04-07"},{"id":"19","name":"test add again","refno":"ref","revise_date":"2015-04-07"},{"id":"18","name":"\u0e07\u0e32\u0e19\u0e1a\u0e31\u0e19\u0e17\u0e36\u0e01","refno":"\u0e01\u0e1f\u0e1c. 1\/2558","revise_date":"2015-04-01"},{"id":"17","name":"this is a book","refno":"no ref","revise_date":"2015-04-07"},{"id":"16","name":"\u0e2a\u0e27\u0e31\u0e2a\u0e14\u0e35... \u0e19\u0e30 \"555\"","refno":"\u0e01\u0e1f\u0e1c. 88","revise_date":"2015-04-04"},{"id":"15","name":"TPLINKTL-SG105E V1 Datasheet","refno":"\u0e2d\u0e04-\u0e17\u0e21 2\/2558","revise_date":"2015-04-02"},{"id":"13","name":"19_UBiQUiTi_NanoBeam_DS.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"12","name":"18_UBiQUiTi_Nanostation_loco_m5.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"11","name":"17_UBiQUiTi_AMO-5G13_ds_web.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"10","name":"15_UBiQUiTi_UniFI_Video_DS.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"9","name":"16_UBiQUiTi_RocketM_DS.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"8","name":"12_Visuailzer Vertex D-1320.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"7","name":"03_Latitude_3000_Series_Spec_Sheet.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"6","name":"08_Cannon PIXMA PRO-100.pdf","refno":null,"revise_date":"2015-04-02"},{"id":"5","name":"02_Latitude_7000_Series_spec_sheet.pdf","refno":null,"revise_date":"2015-04-02"}], "pos":0, "total_count":45}';
//} else {
//list all permission folder only status_id is 1
    $s = $db->prepare("SELECT folder_id,group_id FROM folder_group_perm p, folder f WHERE p.folder_id=f.id AND f.status_id=1");
    $s->execute();
    $data = $s->fetchAll();
    $folders = array();
    foreach ($data as $row) {
        $folder_id = $row['folder_id'];
        $group_id = $row['group_id'];
        // check user login id same as group member
        $s2 = $db->prepare("SELECT * FROM user_group_member WHERE uid=:uid and gid=:gid");
        $s2->bindParam(':uid', $uid);
        $s2->bindParam(':gid', $group_id);
        $s2->execute();
        if (!$s2->rowCount()) { // if has show folder
            $folders[] = array("folder_id" => $folder_id);  // send folder_id to hide (logic.js)
        }
    }
//print_r($folders);
    $json = json_encode($folders);
    echo '{ "list":' . $json . '}';
//}
