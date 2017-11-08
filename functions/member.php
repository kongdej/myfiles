<?php
$name = $_GET['name'];
$email = $_GET['email'];
$id = $_GET['id'];

if (empty($id)) {
    $stmt = $db->prepare("INSERT INTO mailgroup (name,emails,uid) VALUES (:name,:email,:uid)");
    $stmt->bindParam(':uid', $_SESSION['uid']); 
}
else {
    $stmt = $db->prepare("UPDATE mailgroup SET name=:name,emails=:email WHERE id=:id");
    $stmt->bindParam(':id', $id);
}
$stmt->bindParam(':name', $name);
$stmt->bindParam(':email', $email);
$stmt->execute();  

