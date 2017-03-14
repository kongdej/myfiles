<?php
$lists = array();

listFolder(0,$tab);

function listFolder($parent,$tab) {
    global $db,$value,$tab,$lists;
//    $stmt = $db->prepare("SELECT * FROM folder where parent_id=".$parent." and id != 2 and status_id=1 order by orderfield");
    $stmt = $db->prepare("SELECT * FROM folder where parent_id=".$parent." order by orderfield");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tab++;
        for($i=0,$f='';$i<$tab-1;$i++) $f .= '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
        $text = '>&nbsp'.$f.$row['text'];
        $lists[] = "{\"id\":\"" . $row['id'] . "\",\"value\":\"" . $text . "\"}";
//        echo $value;
        listFolder($row['id'],$tab);
        $tab--;
    }
}
$folders = join(',',$lists);
echo '['.$folders .']';

//$r = join(',', $value);
//echo "[" . $r . "]";
