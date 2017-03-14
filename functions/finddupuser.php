<?php
    $username = $_GET['username'];
    $sql = "SELECT username FROM users WHERE username=" . $username;
    $s = $db->prepare($sql);
    $s->execute();
    $data = $s->fetchAll();
    if (count($data)) {
        echo '1';
    }
    else {
        echo '';        
    }
    

