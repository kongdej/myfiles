<?php
$loginuser = $_GET['id'];
$wsdl = "http://webservices.egat.co.th/authentication/au_provi.php?wsdl";
$client = new SoapClient($wsdl);
$result = $client->search_info($loginuser);
if ($result != 'null') {
    //echo $result;
    $result = utf8_decode($result);
    list($name,$email) = split(':',$result);
    echo '[{"name":"'.$name.'","email":"'.$email.'"}]';
    logging('Adduser-'.$name);
}