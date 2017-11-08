<?php

try {
 
    $host = $config['database']['host'];
    $user = $config['database']['user'];
    $password = $config['database']['password'];
    $dbtype = $config['database']['data_type'];

    $loginuser = isset($_POST['username']) ? $_POST['username'] : '';
    $loginpass = isset($_POST['password']) ? $_POST['password'] : '';
//    $database = isset($_POST['database']) ? $_POST['database'] : '';
    $database = $config['database']['default_database'];
  
    if ($config['var']['loginlocal'] == 'true') {
        $db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $password . '');
        $stmt = $db->prepare("SELECT * FROM users WHERE username=:id AND password=:pass");
        $stmt->bindValue(':id', $loginuser);
        $stmt->bindValue(':pass', $loginpass);
        $stmt->execute();
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rows['uid']) {
            saveSession($rows['uid'], $loginuser, $rows['name'],$database);
            echo '[{"uid":"' . $rows["uid"] . '"}]';
            return;
        }
    }

    // login with EGAT account
    if ($config['var']['loginegat'] == 'true') {
        // check user existing users table
        $db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $password . '');

        $stmt = $db->prepare("SELECT * FROM users WHERE username=:id");
        $stmt->bindValue(':id', $loginuser);
        $stmt->execute();
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rows['uid']) {
            // Authenication with EGAT email 
            $wsdl = "http://webservices.egat.co.th/authentication/au_provi.php?wsdl";
            $client = new SoapClient($wsdl);
            $result = $client->validate_user($loginuser, $loginpass);
            if ($result) { //
                saveSession($rows['uid'], $loginuser, $rows['name'], $database);
                echo '[{"uid":"' . $rows["uid"] . '"}]';
                return;
            }             
        }
        else{
            echo '[{"uid":"-2"}]'; // not found user in system
            return;
        }
    }
    echo '[{"uid":"-2"}]';
    
} catch (Exception $e) {
//    echo $e->getMessage();
    // login with Local account
    if ($config['var']['loginlocal'] == 'true') {
        $db = new PDO('mysql:host=' . $host . ';dbname=' . $database . ';charset=utf8', '' . $user . '', '' . $password . '');
        $stmt = $db->prepare("SELECT * FROM users WHERE username=:id AND password=:pass");
        $stmt->bindValue(':id', $loginuser);
        $stmt->bindValue(':pass', $loginpass);
        $stmt->execute();
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rows['uid']) {
            saveSession($rows['uid'], $loginuser, $rows['name'], $database);
            echo '[{"uid":"' . $rows["uid"] . '"}]';
        } else {
            echo '[{"uid":"0"}]';
        }
    } else {
        echo '[{"uid":"0"}]';
    }
}

function saveSession($uid, $username, $name, $database) {
    $_SESSION['uid'] = $uid;
    $_SESSION['username'] = $username;
    $_SESSION['name'] = $name;
    $_SESSION['database'] = $database;
    $_SESSION['level'] = userLevel($uid);
    logging('Login');
}

function userLevel($uid) {
    global $db;
    
    // user level using positio field in users table
    $stmt = $db->prepare("SELECT position FROM users WHERE uid=:id");
    $stmt->bindValue(':id', $uid);
    $stmt->execute();
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($rows['position'] == 'Admin') {
        return 'admin';
    } else if ($rows['position'] == 'DocAdmin') {
        return 'docadmin';
    } else {
        return 'user';
    }
    
   /*
    * user level using user_group_member table 
//  
    $stmt = $db->prepare("SELECT gid FROM user_group_member WHERE uid=:id ORDER BY gid LIMIT 0,1");
    $stmt->bindValue(':id', $uid);
    $stmt->execute();
//    $stmt->debugDumpParams();
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stmt->rowCount()) {
        if ($rows['gid'] == 0) {
            return 'admin';
        } else if ($rows['gid'] == 1) {
            return 'docadmin';
        } else {
            return 'user';
        }
    }
    else {
        return 'user';
    }
    * 
    */
}