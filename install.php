<?php
error_reporting( error_reporting() & ~E_NOTICE );
$action = $_GET['action'];
if ($action == "dbinit"){
    error_reporting(0);

    // create config.php file
    $config = "[database]\n";
    $config .= "data_type=\"MySQL\"\n";
    $config .= "host=\"".$_POST['dbhost']."\"\n";
    $config .= "user=\"".$_POST['dbusername']."\"\n";
    $config .= "password=\"".$_POST['dbpassword']."\"\n";
    $config .= "default_database=\"".$_POST['dbname']."\"\n\n";
    $config .= "[var]\n";
    $config .= "loginegat=\"true\"\n";
    $config .= "loginlocal=\"true\"\n";

    if (file_exists("config.php")) {
            rename("config.php","config-old.php");
    }

    $fileconfig = fopen("config.php", "w") or die("Unable to open file!");
    fwrite($fileconfig, $config);
    fclose($fileconfig);

    // database
    $link = mysql_connect($_POST['dbhost'], $_POST['dbusername'], $_POST['dbpassword']);
    if (!$link) {
        $err .= "Could not connect: " . mysql_error()."<br>\n";
    }
    else {
            $sql = 'CREATE DATABASE '.$_POST['dbname'].' CHARACTER SET utf8 COLLATE utf8_general_ci';
            if (mysql_query($sql, $link)) {
                $ok .= "Database my_db created successfully<br>\n";

                    mysql_select_db($_POST['dbname'], $link);

                    $sqlSource = file_get_contents('sql/myfiles.sql');
                    $query_array = explode(';', $sqlSource);
                    foreach ($query_array as $k=>$v) {
                            $v = str_replace(array("\r\n","\r"),"",$v);
                            if (!empty($v)) {
                                    if(!mysql_query($v, $link)) {
                                            $err .=  'Error creating tables: '.mysql_error();
                                    }
                            }
                    }

                    // insert setting
                    $sql  = "INSERT INTO system_settings (id, name, value) VALUES ";
                    $sql .= "(1, 'site_name', '".$_POST['sitecode']."'),";
                    $sql .= "(2, 'cur_theme', '".$_POST['theme']."'),";
                    $sql .= "(3, 'title', '".$_POST['title']."'),";
                    $sql .= "(4, 'copyright', '".$_POST['copyright']."')";
                    if(!empty($sql)) {
                            if(!mysql_query($sql, $link)) {
                                    $err .=  'Error insert tables: '.mysql_error();
                            }
                    }
            } 
            else {
                $err .= 'Error creating database: ' . mysql_error() . "\n";
            }
    }

    if ($err) {
            $res = array("status" => "err", "sname" => $err);    
    }
    else {
            $res = array("status" => "ok", "sname" => $ok);    	
    }
    //echo $err;
    echo json_encode($res);
    exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="codebase/webix.css" type="text/css" charset="utf-8">
        <script src="codebase/webix.js" type="text/javascript" charset="utf-8"></script>
    </head>
    <body bgcolor="#ccc">
		<div id="viewA" style="width:700px; height:300px; margin:100px auto;"></div>
		<div id="msg" style="width:700px; height:20px; margin:10px auto;"></div>
		
		<script type="text/javascript" charset="utf-8">
			function submitform(){
			    var values = $$("configform").getValues();
			    console.log(values);    
			    webix.ajax().post(
			        "install.php?action=dbinit",
			        values,
			        function (text) {  //responce
			            var d = JSON.parse(text);
			            console.log(d);
			            if (d.status == 'ok') {
			               // webix.alert({type: "success", text: d.sname});
                                        webix.confirm("Installation completed. Login:admin/admin", function (result) {
                                            if (result) {
                                                window.location = "./";
                                            }
                                        });
                                    }
			            if (d.status == 'err') { 
			                webix.alert({type: "error", text: d.sname});                
			            }
			        }
			    );

			}

	        webix.ui({
				type: "line",
				id:"configform",
				view: "form",
				container:"viewA",
	    		rows: [
			        {view:"toolbar", 
                                    elements:[
					{ view:"label", height:40, label:" : : INSTALLATION - MyFiles LEO (Light Easy OK)"}
                                    ]
                                },
			        {height:20},
				{view: "text", label: "Database Host", name: "dbhost", labelWidth:180, value:"localhost"},
			        {view: "text", label: "Database Username", name: "dbusername", labelWidth:180, value:"root"},
			        {view: "text", label: "Database Password", name: "dbpassword", labelWidth:180, value:""},
			        {view: "text", label: "Database Name", name: "dbname", labelWidth:180, value:"myfiles_cid"},
			        {height:20},
			        {view: "text", label: "Site code", name: "sitecode", labelWidth:180,value:"CID"},
			        {view: "text", label: "Tittle", name: "title", labelWidth:180, value:"Control and Instrument Department"},
			        {view: "text", label: "Copyright", name: "copyright", labelWidth:180,value:"PUDZA Maker Club"},
                                {view: "select", label:"Theme", labelWidth:180, name:"theme", options:"functions/themelist.php"},
			        {height:30},
			        {view:"toolbar", 
                                    elements:[
					{},
                                        { view:"button", value:"SUBMIT", with:100, click:"submitform()"},{}
                                    ]
                                },
	    		]
			});
        </script>

    </body> 
</html>