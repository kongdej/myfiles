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
			        "install_server.php",
			        values,
			        function (text) {  //responce
			            var d = JSON.parse(text);
			            console.log(d);
			            if (d.status == 'ok') {
			                webix.alert(d.sname);
							window.location = "./";
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
			        { view:"toolbar", elements:[
						{ view:"label", height:40, label:" : : INSTALLATION - MyFiles LEO v1.0 "}]
					},
			        {height:20},
					{view: "text", label: "Database Host", name: "dbhost", labelWidth:180, value:"localhost"},
			        {view: "text", label: "Database Username", name: "dbusername", labelWidth:180, value:"root"},
			        {view: "text", label: "Database Password", name: "dbpassword", labelWidth:180, value:""},
			        {view: "text", label: "Database Name", name: "dbname", labelWidth:180, value:"myfiles_sbrp1"},
			        {height:20},
			        {view: "text", label: "Site CODE", name: "sitecode", labelWidth:180,value:"SBRP1"},
			        {view: "text", label: "Tittle", name: "title", labelWidth:180, value:"MyFiles - South Bangkok Power Plant Replacment Project Phase I"},
			        {view: "text", label: "Copyright", name: "copyright", labelWidth:180,value:"MyFiles LEO (Light Easy OK)"},
                                {view: "select", label:"Theme", labelWidth:180, name:"theme", options:"functions/themelist.php",value:"2"},
			        {height:30},

			        {view:"toolbar", elements:[
						{},{ view:"button", value:"SUBMIT", with:100, click:"submitform()"},{}]
					},
	    		]
			});
        </script>

    </body> 
</html>