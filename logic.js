/* Logic Section */
// == 
// submit data to database
function uploadFiles(action) {
    if (action == 'add') { // add new document
        var folder_id = $$('uform').getValues().folder_id;
        var name = $$('uform').getValues().name;
        var refno = $$('uform').getValues().refno;
        var revise_date = $$('uform').getValues().revise_date;
        var keyword = $$('uform').getValues().keyword;
        var description = $$('uform').getValues().description;
        if (folder_id) {
            // post data
            webix.ajax().post("index.php?m=upload&action=add",$$("uform").getValues(), function(text){ 
                // upload file
                if (!$$('uploadfiles').isUploaded()) {
                    $$("uploadfiles").send(function(){});
                }
                // refresh new data
                $$('uploadwindow').hide();
                $$("listdoc").clearAll();
                $$("listdoc").load("index.php?m=data&folder_id=" + folder_id);
                $$("listdoc").refresh();
            });
        }
        else {
            webix.alert('Error! <br>Please select a folder.');
        }
    }
    else if (action == 'edit') {
        var folder_id = $$('edit_form').getValues().folder_id;
        if (folder_id) {
            var id = $$('edit_form').getValues().id;
            webix.ajax().post("index.php?m=upload&action=edit&id="+id,
                $$("edit_form").getValues(), 
                function (text) {  
                    $$('editdocpopup').hide();
            });
            if (!$$('editfiles').isUploaded()) {
                $$("editfiles").define("upload", "index.php?m=upload&action=changefile&id="+id);
                $$("editfiles").send(function (response) {
                    console.log(response);
                    if (response) {
                        webix.message(response.sname);
                    }
                });
            }
        }
    }
}

function cancel(action) {
    if (action == 'add') {
        $$('uploadwindow').hide();
//        $$('mylist').clearAll();
    }
    else if (action == 'edit') {
        $$('editdocpopup').hide();
//        $$('mylist').clearAll();
    }
    else if (action == 'editfolder') {
        $$('editfolderpopup').hide();
    }
}

//=== Document ===

/* click fullscreen button */
var toggle = 0;
function fullscreen() {
    if (toggle === 0) { // click fullscreen
        $$('folder_main').hide();
        $$('grid_main').hide();
        $$('foot_main').hide();
        $$('head_main').hide();
        $$('edit_button').hide();
        $$('delete_button').hide();
        $$('download_button').hide();
        $$('properties_button').hide();
        $$('mail_button').hide();
        $$('group_mail_button').hide();        
        $$('fullscreen_button').define("align","center");
        $$('fullscreen_button').define("icon","close");
        $$('fullscreen_button').define("label","Close");
        $$('fullscreen_button').refresh();        
        toggle = 1;
    }
    else {
        $$('folder_main').show();
        $$('grid_main').show();
        $$('foot_main').show();
        $$('head_main').show();
        console.log(userlevel);
        if (userlevel == 'admin' || userlevel == 'docadmin') {
            $$('edit_button').show();
            $$('delete_button').show();
        }
        $$('download_button').show();
        $$('properties_button').show();
        $$('mail_button').show();
        $$('group_mail_button').show();
        $$('fullscreen_button').define("label","Full Screen");
        $$('fullscreen_button').refresh();        
        toggle = 0;
    }
}

/* click download button */
function download() {
    var id = $$('listdoc').getSelectedId();
    window.open('index.php?m=download&id=' + id);
}


/* click properties button */
function loadSettings(obj) {
    webix.ajax("index.php?m=settings" , function (text) {
        var d = JSON.parse(text);
        console.log(d);
        obj.setValues({
            title: d.title,
            copyright: d.copyright,
            cur_theme: d.cur_theme,
            type: "json"
        });
    });
}

function loadProperties(obj) {
    var docid = $$("listdoc").getSelectedId().id;
    webix.ajax("index.php?m=properties&id=" + docid, function (text) {
        var d = JSON.parse(text);
//        console.log(d);
        obj.setValues({
            id: config.site_name+'-'+d.data[0].id,
            name: d.data[0].name,
            revise_date: d.data[0].revise_date,
            created: d.data[0].created,
            modified: d.data[0].modified,
            keyword: d.data[0].keyword,
            description: d.data[0].description,
            refno: d.data[0].refno,
            creator: d.data[0].creator,
            folder: d.data[0].folder,
            folder_id: d.data[0].folder_id,
//            filepath: d.data[0].filepath,
            filename: d.data[0].filename,
            type: "json"
        });
    });
}

function loadProfile(obj) {
    webix.ajax("index.php?m=profile&username=" + session.username, function (text) {
        var d = JSON.parse(text);
        console.log(d[0]);
        
        obj.setValues({
            id: d[0].id,
            username: d[0].username,
            name: d[0].name,
            password: d[0].password,
            email: d[0].email,
            position: d[0].position
        });
    });    
}

function loadFolderProperties(obj) {
    var folder_id = $$("folder").getSelectedId();
    webix.ajax("index.php?m=folder&action=data&folder_id=" + folder_id, function (text) {
        var d = JSON.parse(text);
//        console.log(d);
        obj.setValues({
            id: d.id,
            text: 'Folder : '+d.text,
            orderfield: d.orderfield,
            parent_id: d.parent_id,
            permission: d.permission
        });
    });
}
function loadFolderPermission(obj) {
    var folder_id = $$("folder").getSelectedId();
    $$("susertable").load("index.php?m=folderpermission&action=listsource&folder_id=" + folder_id);
    $$("tusertable").load("index.php?m=folderpermission&action=listtarget&&folder_id=" + folder_id);
    // load user list into userlist list
    // $$('userlist').load("index.php?m=permission&action=list&folder_id=" + folder_id);
    // select user permission
//    obj.setValues({
//        permission: "539953,54001"
//    });

}

/* click back button - todo: hide when first page */
function moveBack() {
    var id = $$('listdoc').getSelectedId();
    var prvid = $$('listdoc').getPrevId(id);
    $$('listdoc').select(prvid);
    var docid = $$('listdoc').getSelectedId();
    webix.ajax("index.php?m=getdoc&id=" + docid, function (text) {
        var d = JSON.parse(text);
        var path = docpath + d.data[0].filepath;
        if (d.data[0].filepath) {
            $$("content").define("src", path);
        }
        else {
            webix.alert("Document is not found!");
        }
    });
}

/* click next button - todo: hide whene last page */
function moveNext() {
    var id = $$('listdoc').getSelectedId();
    var nextid = $$('listdoc').getNextId(id);
    $$('listdoc').select(nextid);
    var docid = $$('listdoc').getSelectedId();
    webix.ajax("data/getdoc.php?id=" + docid, function (text) {
        var d = JSON.parse(text);
        var path = docpath + d.data[0].filepath;
        if (d.data[0].filepath) {
            $$("content").define("src", path);
        }
        else {
            webix.alert("Document is not found!");
        }
    });
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function uploadDocuments() {
    webix.ui({
        id: "uploadwindow",
        view: "window",
        position: "center",
        width: 680,
        move: true,
        modal: true,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", align: "center", label: "Upload Document"},
                {view: "icon", icon: "times-circle",
                    click: "$$('uploadwindow').hide();"}
            ]
        },
        body: webix.copy(uploadform)
    });
    var folder_id = $$('folder').getSelectedId();
    $$('uform').setValues({
        folder_id: folder_id
    });
    $$('uploadwindow').show();
}

function editDocument() {
    webix.ui({
        id: "editdocpopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: true,
        width: 600,
//        head: "Document",
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", align: "center", label: "Edit Document"},
                {view: "icon", icon: "times-circle",
                    click: "$$('editdocpopup').close();"}
            ]
        },
        body: webix.copy(editform)
    });
    $$('editdocpopup').attachEvent("onShow", function (data, prevent) {
        loadProperties($$('edit_form'));
    });
    $$('editdocpopup').show();
}

function deletedata() {
    var document_id = $$('listdoc').getSelectedId();
    var folder_id = $$('folder').getSelectedId();
    if (document_id) {
        webix.confirm("Delete document?", function (result) {
            if (result) {

                webix.ajax("index.php?m=deletedocument&id=" + document_id);
                $$("listdoc").clearAll();
                $$("listdoc").load("index.php?m=data&folder_id=" + folder_id);
                $$("listdoc").refresh();
                $$("content").define("src", "empty.html");
                $$("fullscreen_button").disable();
                $$("download_button").disable();
                $$("mail_button").disable();
                $$("group_mail_button").disable();
                $$("properties_button").disable();
                $$("delete_button").disable();
                $$("edit_button").disable();
//                $$("back_button").disable();
//                $$("next_button").disable();
//                $$("content_main").collapse()();
            }
        });
    }
    else {
        webix.alert({
            title: "ERROR",
            text: "Please select a document before delete!",
            type: "alert-error"
        });
    }
}

function showProfile() {
    webix.ui({
        id: "profilepopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: true,
        width: 600,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", label: "User Profile"},
                {view: "icon", icon: "times-circle",
                    click: "$$('profilepopup').hide();"}
            ]
        },
        body: webix.copy(profilesheet)
    });
    $$('profilepopup').attachEvent("onShow", function (data, prevent) {
        loadProfile($$('profile_data'));
    });
    $$('profilepopup').show();
}

function showProperties() {
    webix.ui({
        id: "propertiespopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: true,
        width: 600,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", label: "Properties"},
                {view: "icon", icon: "times-circle",
                    click: "$$('propertiespopup').hide();"}
            ]
        },
        body: webix.copy(propertysheet)
    });
    $$('propertiespopup').attachEvent("onShow", function (data, prevent) {
        loadProperties($$('properties_data'));
    });
    $$('propertiespopup').attachEvent("onHide", function (data, prevent) {
        $$('properties_button').refresh();
//        console.log('here');
    });
    $$('propertiespopup').show();
}


function mail() {
    webix.ui({
        id: "mailpopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: true,
        width: 600,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", label: "Mail Document"},
                {view: "icon", icon: "times-circle", click: "$$('mailpopup').hide();"}
            ]
        },
        body: webix.copy(mailform)
    });
    
    $$('mailpopup').attachEvent("onShow", function (data, prevent) {
//        loadProperties($$('properties_data'));
    });
    $$('mailpopup').attachEvent("onHide", function (data, prevent) {
//        $$('properties_button').refresh();
//        console.log('here');
    });
    $$('mailpopup').show();    
}

function mailGroup() {
    webix.ui({
        id: "groupmailpopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: true,
        width: 600,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", label: "Notify Document"},
                {view: "icon", icon: "times-circle", click: "$$('groupmailpopup').hide();"}
            ]
        },
        body: webix.copy(groupmailform)
    });
    $$('groupmailpopup').show();
//    console.log($$('groupmailform').getValues());
//    $$('listgroup').setValue($$('listgroup').getValue());
}

function addmember() {
    var form = $$('editmemberform').getValues();
//        console.log(form.member);
    var contacts = form.member.split('<');
    name = contacts[0];
    email= contacts[1].substring(0, contacts[1].length - 1);
    $$('editmemberform').setValues({
        name:form.name,
        member:''
    });
    $$('membertable').add({
        name: name,
        email: email
    }); 
}

function delmember() {
    var id = $$('membertable').getSelectedId();
    if (id) {
        $$('membertable').remove(id);
    }
    else {
        webix.alert({type:"error",text:"Please select any user!"});
    } 
}

function savemember(id) {
    var form = $$('editmemberform').getValues();
    if (form.name == '') {
       webix.alert({type:"error",text:"Please enter group name."}); 
    }
    else {
        var dtable = $$('membertable');
        var emails = [];
        dtable.eachRow( 
            function (row){ 
                //console.log( dtable.getItem(row).email );
                emails.push(dtable.getItem(row).email);
            }
        )
        var email = emails.join(',');
        if (id) {
            webix.ajax("index.php?m=member&id="+id+"&name="+form.name+"&email="+email, function(text,res){
                $$('newMemberpopup').hide();
                $$('grouptable').load('index.php?m=group');
            });            
        }
        else {
            webix.ajax("index.php?m=member&name="+form.name+"&email="+email, function(text,res){
                $$('newMemberpopup').hide();
                $$('grouptable').load('index.php?m=group');
            });
        }
        //console.log(email);    
    }
}

function sendMail() {
    var id = $$("listdoc").getSelectedId().id;
    var values = $$("mailform").getValues();
    console.log(values);    
    webix.ajax().post(
        "index.php?m=mail&id=" + id,
        values,
        function (text) {  //responce
            var d = JSON.parse(text);
            console.log(d);
            if (d.status == 'ok') {
                webix.message(d.sname);
                $$('mailpopup').hide();
            }
            if (d.status == 'err') { 
                webix.message({type: "error", text: d.sname});                
            }
        }
    );
}

function setting() {
    webix.ui({
        id: "settingpopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: true,
        width: 600,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", label: "System Settings"},
                {view: "icon", icon: "times-circle", click: "$$('settingpopup').hide();"}
            ]
        },
        body: webix.copy(settingform)
    });    
    $$('settingpopup').attachEvent("onShow", function (data, prevent) {
        loadSettings($$('settingform'));
    });
    $$('settingpopup').show();    
}

function logging() {
    webix.ui({
        id: "loggingpopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: true,
        width: 712,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", label: "Event Logging"},
                {view: "icon", icon: "times-circle", click: "$$('loggingpopup').hide();"}
            ]
        },
        body: webix.copy(loggingform)
    });    
    $$('loggingpopup').attachEvent("onShow", function (data, prevent) {
//        loadSettings($$('settingform'));
    });
    $$('loggingpopup').show();    
}

function sendMail() {
    var id = $$("listdoc").getSelectedId().id;
    var values = $$("mailform").getValues();
    console.log(values);    
    webix.ajax().post(
        "index.php?m=mail&id=" + id,
        values,
        function (text) {  //responce
            var d = JSON.parse(text);
            console.log(d);
            if (d.status == 'ok') {
                webix.message(d.sname);
                $$('mailpopup').hide();
            }
            if (d.status == 'err') { 
                webix.message({type: "error", text: d.sname});                
            }
        }
    );
}

function sendGroupMail() {
    var id = $$("listdoc").getSelectedId().id;
    var values = $$("groupmailform").getValues();
    console.log(values); 
    webix.ajax().post(
        "index.php?m=mailgroup&id=" + id,
        values,
        function (text) {  //responce
            var d = JSON.parse(text);
            console.log(d);
            if (d.status == 'ok') {
                webix.message(d.sname);
                $$('groupmailpopup').hide();
            }
            if (d.status == 'err') { 
                webix.message({type: "error", text: d.sname});                
            }
        }
    );
    
}

function settingSubmit() {
    var values = $$("settingform").getValues();
    console.log(values);    
    webix.ajax().post(
        "index.php?m=settings&action=update",
        values,
        function (text) {  //responce
            var d = JSON.parse(text);
            console.log(d);
            if (d.status == 'ok') {
                webix.message(d.sname);
                $$('settingpopup').hide();
            }
            if (d.status == 'err') { 
                webix.message({type: "error", text: d.sname});                
            }
        }
    );
}

//!== document ==

//=== Folder management ===
function addFolder() {
    var parentId = $$('folder').getSelectedId();
    if (!parentId) {
        parentId = 0;
    }
    webix.ajax("index.php?m=folder&action=add&parent_id=" + parentId, function (text) {
        var d = JSON.parse(text);
//        console.log(d.folder_id);
        var id = $$('folder').data.add({id: d.folder_id, text: "New Folder"}, 0, parentId);
//        console.log(id);
        $$('folder').open(parentId);
        $$('folder').edit(d.folder_id);
    });
}

function delFolder() {
    var nodeId = $$('folder').getSelectedId();
    if (nodeId) {
        webix.confirm("Delete folder?", function (result) {
            if (result) {
                webix.ajax("index.php?m=folder&action=del&folder_id=" + nodeId, function(text,obj){
                    if (text) {
                        webix.alert({
                            type:'error',
                            text:obj.json().msg
                        });
                    }
                    else {
                        $$('folder').remove(nodeId);
                    }
                });
                
            }
        });
    }
    else
        webix.alert("Select Folder");

}

function delLog() {
    webix.confirm("Clear all event logging?", function (result) {
        if (result) {
            webix.ajax("index.php?m=logging&action=del", function(text,obj){
                if (text) {
                    webix.alert({type:'error', text:obj.json().msg});
                    $$("listlogging").load("index.php?m=logging");
                    $$("listlogging").refresh();
                    $$('loggingpopup').hide();
                }
            });
        }
    });
}

function editFolder() {
    var nodeId = $$('folder').getSelectedId();
    if (nodeId) {
        webix.ui({
            id: "editfolderpopup",
            view: "window",
            position: "center",
            modal: true,
            scroll: false,
            move: true,
            borderless: true,
            width: 600,
//            head: "Folder",
            head: {
                view: "toolbar", margin: -4, cols: [
                    {view: "label", align: "center", label: "Folder Permission"},
                    {view: "icon", icon: "times-circle",
                        click: "$$('editfolderpopup').close();"}
                ]
            },
            body: webix.copy(editfolderform)
        });
        $$('editfolderpopup').attachEvent("onShow", function (data, prevent) {
            loadFolderProperties($$('edit_folderform'));
            loadFolderPermission($$('edit_folderform'));
        });
        $$('editfolderpopup').show();
    }
    else {
        webix.alert('Select folder!');
    }
}
function updateFolder() {
    if ($$('edit_folderform').validate()) {
        var folder_id = $$('folder').getSelectedId();
//        var values = $$("tusertable").getValues();
        console.log($$("tusertable"));
        
        // update folder table
/*
        webix.ajax().post(
                "index.php?m=folder&action=update&folder_id=" + folder_id,
                $$("edit_folderform").getValues(),
                function (text) {  //responce
                    var d = JSON.parse(text);
                    console.log(d);
                    if (d.ret) {
                        $$('editfolderpopup').hide();
                        refreshFolder();
                        //$$('folder').data.add({id: d.folder_id, text: $$("edit_folderform").getValues().text}, 0, $$("edit_folderform").getValues().parent_id);
                    }
                }
        );
*/
        // updata permission
//        $$('editfolderpopup').hide();
    }
}

function refreshFolder() {
    var folderId = $$('folder').getSelectedId();
//    var parentId = $$('folder').getParentId(folderId);
    $$("folder").clearAll();
    $$("folder").load("index.php?m=folder");
    $$("folder").refresh();
//    console.log(folderId);
//    console.log(parentId);
//    $$("folder").open(parentId);    
    $$("folder").select(folderId);
}
//!== folder ===

//== Login ===
function login() {
    if ($$('log_form').validate()) {
        webix.ajax().post(
                "index.php?m=login",
                $$("log_form").getValues(),
                function (text) {  //responce
                    var d = JSON.parse(text);
                    //console.log(d[0].uid);
                    if (text == '[]') {
                        webix.message({type: "error", text: "You are not member in this site."});
                    }
                    else if (d[0].uid == 0) {
                        webix.message({type: "error", text: "Invalid Username or Password.<br/>ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"});
                    }
                    else if (d[0].uid == -1) {
                        webix.message({type: "error", text: "EGAT e-Mail is incorrect."});
                    }
                    else if (d[0].uid == -2) {
                        webix.message({type: "error", css: "", text: "Not found user in this site.<br/>ไม่พบผู้ใช้งานในโครงการนี้"});
                    }
                    else {
                        location.reload();
                    }
                }
        );
    }
}

function logout() {
    window.open('index.php?m=logout', '_self');
}
//!== Login&logout

function editUser() {
    webix.ui({
        id: "edituserpopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: false,
        width: 900,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", align: "center", label: "User Management"},
                {view: "icon", icon: "times-circle",
                    click: "$$('edituserpopup').close();"}
            ]
        },
        body: webix.copy(edituserform)
    });
//    $$('edituserpopup').attachEvent("onShow", function (data, prevent) {
    //loadProperties($$('edit_form'));
//    });
    $$('edituserpopup').show();
}


function newMember(action) {
    
        webix.ui({
            id: "newMemberpopup",
            view: "window",
            position: "center",
            modal: true,
            scroll: false,
            move: true,
            borderless: false,
            width: 900,
            head: {
                view: "toolbar", margin: -4, cols: [
                    {view: "label", align: "center", label: "Group"},
                    {view: "icon", icon: "times-circle",
                        click: "$$('newMemberpopup').close();"}
                ]
            },
            body: webix.copy(editmemberform)
        });
    if (action == 'edit') {
        var id = $$('grouptable').getSelectedId();
        webix.ajax("index.php?m=memberedit&id="+id , function (text) {
            var res = JSON.parse(text);
            console.log(res.name);
            $$('editmemberform').setValues({
                name:res.name
            });
            for(var i=0; i < res.data.length; i++) {
                var contact = JSON.parse(res.data[i]);
                $$('membertable').add({
                    name: contact.name,
                    email: contact.email
                }); 
            }
            $$('membertable').refresh();  
        });
    }
    $$('newMemberpopup').show();
}

function editGroup() {
    webix.ui({
        id: "editgrouppopup",
        view: "window",
        position: "center",
        modal: true,
        scroll: false,
        move: true,
        borderless: false,
        width: 900,
        head: {
            view: "toolbar", margin: -4, cols: [
                {view: "label", align: "center", label: "Group Management"},
                {view: "icon", icon: "times-circle",
                    click: "$$('editgrouppopup').close();"}
            ]
        },
        body: webix.copy(editgroupform)
    });
    $$('editgrouppopup').show();
}

//============================================================================
/* main function */
var userlevel = '';
var logic = {
    /* init */
    init: function (user) {
//      check mobile device
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            $$("folder_main").hide();
            $$('content_main').hide();
        }
        userlevel = user;
        switch (user) {
            case 'admin':
                $$('folder').define("drag", true);
                $$('folder').define("editable", true);
                $$("edit_button").show();
                $$("delete_button").show();
                $$("content_main").collapse();
                $$("folder_main").expand();
                $$("btnUser").show();
                $$("btnDocadminadd").show();
                $$("btnSetting").show();
                $$("btnLogging").show();
                $$("listdoc").showColumn("infodoc");
                if (!view.adminview.id) $$('listdoc').hideColumn("id");
                if (!view.adminview.name) $$('listdoc').hideColumn("name");
                if (!view.adminview.refno) $$('listdoc').hideColumn("refno");
                if (!view.adminview.path) $$('listdoc').hideColumn("path");
                if (!view.adminview.revise_date) $$('listdoc').hideColumn("revise_date");
            break;
            case 'docadmin' :
                $$('toolbar_folder').hide();
                $$("btnUser").hide();
                $$("content_main").collapse();
                $$("folder_main").expand();
                $$("btnDocadminadd").show();
                $$("listdoc").showColumn("infodoc");                
                if (!view.docadminview.id) $$('listdoc').hideColumn("id");
                if (!view.docadminview.name) $$('listdoc').hideColumn("name");
                if (!view.docadminview.refno) $$('listdoc').hideColumn("refno");
                if (!view.docadminview.path) $$('listdoc').hideColumn("path");
                if (!view.docadminview.revise_date) $$('listdoc').hideColumn("revise_date");
                break;
            default:
                $$('toolbar_folder').hide();
                $$('btnDocadminadd').hide();
                $$("btnUser").hide();
                $$("folder_main").expand();
                $$("grid_main").expand();
                $$("content_main").collapse();
                $$('folder_main').define("gravity", 1);
                $$('grid_main').define("gravity",1 );
                $$('content_main').define("gravity", 2);
                $$('folder_main').refresh();
                $$('grid_main').refresh();
                $$('content_main').refresh();
                if (!view.userview.id) $$('listdoc').hideColumn("id");
                if (!view.userview.name) $$('listdoc').hideColumn("name");
                if (!view.userview.refno) $$('listdoc').hideColumn("refno");
                if (!view.userview.path) $$('listdoc').hideColumn("path");
                if (!view.userview.revise_date) $$('listdoc').hideColumn("revise_date");

        }
        
        // popup windows
        // order by
        webix.ui({
            view: "popup",
            id: "sortpopup",
            scroll: false,
            borderless: true,
            head: "Submenu",
            width: 250,
            body: webix.copy(sortform)

        });
        // advanced search
        webix.ui({
            id: "advsearchpopup",
            view: "popup",
            scroll: false,
            borderless: true,
            width: 600,
            head: "Submenu",
            body: webix.copy(advsearchform)
        });

        webix.ui( {
                view: "submenu",
                id: "profilePopup",
                width: 200,
                padding:0,
                data: [
                        {id: 1, icon: "user", value: "My Profile"},
                        {id: 2, icon: "users", value: "My Group"},
                        { $template:"Separator" },
                        {id: 4, icon: "sign-out", value: "Logout", click: "logout()"}
                ],
                on:{
                        onMenuItemClick:function(id){
                           // webix.message("Click: "+this.getMenuItem(id).value);
                            if (this.getMenuItem(id).id === 1) {
                                showProfile();
                            }
                            else if (this.getMenuItem(id).id === 2) {
                                editGroup();
                            }
                            else if (this.getMenuItem(id).id === 4) {
                                logout();
                            }
                        }
                },
                type:{
                        template: function(obj){
                                if(obj.type)
                                        return "<div class='separator'></div>";
                                return "<span class='webix_icon alerts fa-"+obj.icon+"'></span><span>"+obj.value+"</span>";
                        }
                }
        });

        // Events Attached ===     
        // click document list then show content
        $$("listdoc").attachEvent("onAfterSelect", function (data, prevent) {
            var title = this.getItem(data.id).name;
            var docid = this.getItem(data.id).id;
            webix.ajax("index.php?m=getdoc&id=" + docid, function (text) {
                var d = JSON.parse(text);
                //var path = docpath + d.filepath;
                console.log(d.filetype);
                if (d.filetype == 'application/pdf' || d.filetype == "application/octet-stream") {
                    path = 'pdfjs/web/viewer.html?file=../../' + d.filepath+'&r='+Math.random();
                }
//                if (d.filetype == 'video/mp4') {
//                    path = 'index.php?m=vjs&f='+d.filepath+'&r='+Math.random();
//                }
                else if (d.filetype.indexOf('html')!= -1) {
                    path = d.filepath;
                }
                else if (d.filetype.indexOf('image')!= -1) {
                    path = d.filepath;
                }
                else {
                    path = 'index.php?m=view&file=' + d.filepath+'&type='+d.filetype;
                }
                $$("content").define("src", path);
                $$("content_main").define("header", title);
                $$("content_main").show();
                $$("content_main").expand();
                $$("folder_main").collapse();
                $$("properties_button").enable();
                $$("download_button").enable();
                $$("mail_button").show();                
                $$("mail_button").enable();
                $$("group_mail_button").show();                
                $$("group_mail_button").enable();
                $$("fullscreen_button").enable();
                $$('listdoc').hideColumn("path");
                $$('listdoc').hideColumn("refno");
                $$('listdoc').hideColumn("revise_date");

                if (user == 'docadmin' || user == 'admin') {
                    $$("delete_button").show();
                    $$("delete_button").enable();
                    $$("edit_button").show();
                    $$("edit_button").enable();
                }
            });
        });

        // click folder, show list documents
        $$("folder").attachEvent("onAfterSelect", function (data, prevent) {
            var id = this.getSelectedId();
            $$("listdoc").clearAll();
            $$("listdoc").load("index.php?m=data&folder_id=" + id);
            $$("listdoc").refresh();
            $$("grid_main").define("header", "<span class='webix_icon fa-angle-double-right'></span>"+$$("folder").getSelectedItem().text);
        });
       
      
        //  click search button to search 
        $$("search_button").attachEvent("onItemClick", function () {
            normalSearch();
        });

        // press enter key to search
        $$("search").attachEvent("onKeyPress", function (code, e) {
            if (code === 13 && !e.ctrlKey && !e.shiftKey && !e.altKey) {
                normalSearch();
            }
        });

        function normalSearch() {
            var value = $$('search').getValue().toLowerCase(); //input data is derived
            webix.message("Search.." + value);
            $$("listdoc").clearAll();
            $$("listdoc").load("index.php?m=data&s=" + value);
            $$("listdoc").refresh();
            $$("folder").unselect();
        }

        // Folder Management 
        $$('folder').attachEvent("onAfterEditStop", function (data, obj) {
            var folder_id = obj.id;
            webix.ajax("index.php?m=folder&action=edit&folder_id=" + folder_id + "&data=" + data.value);
        });

        $$('folder').attachEvent("onAfterDrop", function (id, native_event) {
            webix.ajax("index.php?m=folder&action=move&folder_id=" + id.start + "&parent_id=" + id.parent + "&index="+id.index);
            $$("folder").refresh();
        });
    }
};
//!=== END main ===============================================================