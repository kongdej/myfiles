/* == Login Form== */

//console.log(firstdb);
var loginform = {
    view: "form",
    id: "log_form",
    width: 350,
    elements: [
        {view: "text", id: "username", name: "username", labelWidth:80, label: "Username:", placeholder: 'เลขประจำตัว',
            on: {
                onKeyPress: function (code, e) {
                    if (code === 13 && !e.ctrlKey && !e.shiftKey && !e.altKey) {
                        login();
                    }
                }
            }
        },
        {view: "text", id: "password", name: "password", type: 'password', labelWidth:80, label: "Password:", placeholder: 'รหัสผ่าน Email EGAT',
            on: {
                onKeyPress: function (code, e) {
                    if (code === 13 && !e.ctrlKey && !e.shiftKey && !e.altKey) {
                        login();
                    }
                }
            }
        },
        {height: 5},
        {margin: 5, cols: [
                {width:80},{view: "button", value: "Login", type: "form", click: "login()", width:130,height:35},{}
            ]}
    ],
    rules: {
        "username": webix.rules.isNotEmpty,
        "password": webix.rules.isNotEmpty
    },
    elementsConfig: {
        labelPosition: "left"
    },
    on: {
        onValidationError: function (id, value) {
            if (id == "username") {
                webix.message({type: "error", text: "Login can't be empty."});
            }
            else if (id == "password") {
                webix.message({type: "error", text: "Password can't be empty."});
            }
        }
    }

};
// == login form


/*== Layout section ==*/

// header
var header = {
    id: "head_main",
    body: {
        view: "toolbar",
        paddingY: 12,
        elements: [
            {view: "button", type: "icon", icon: "file-text-o", tooltip: title, css: "bt_1", label: org, click: "location.reload()", width: 240},
//            {},
            {view: "search", id: "search", placeholder: "Search..", width: 250},
            {view: "button", id: "search_button", css: 'bt_1', label: "Search", tooltip: "Click to Search", width: 60},
//            {view: "button", type: "htmlbutton", id: "sort", label: "Sort<span class='webix_icon fa-angle-down'></span>", tooltip: "Sort by", width: 80, popup: "sortpopup"},
            {view: "button", type: "htmlbutton", id: "advsearch", label: "More <span class='webix_icon fa-angle-down'></span>", tooltip: "Advanced search", width: 70, popup: "advsearchpopup"},
            {view: "icon", id: "btnDocadminadd", icon: "plus-circle", tooltip: "Add document", click: "uploadDocuments()", hidden:true},
            {view: "icon", id: "btnUser", icon: "user-plus", tooltip: "User Management", click: "editUser()", hidden:true},
            {view: "icon", id: "btnSetting", icon: "cog", tooltip: "System Settings", click: "setting()", hidden:true},
            {view: "icon", id: "btnLogging", icon: "clock-o", tooltip: "Logging", click: "logging()", hidden:true},
            {view: "icon", icon: "power-off", tooltip: "Logout", click: "logout()"}
        ]}
};

// footer
var footer = {
    id: "foot_main",
    height: 25,
    css: {"background-color": "#F1F2F3", "text-align": "left", "font-size": "12px;"},
    template: "&copy  " + copyright + " -- Login: " + username};

// tree folder
webix.protoUI({
    name: "edittree"
}, webix.EditAbility, webix.ui.tree);

var folder = {
    id: 'folder_main',
    header: "Folders",
    paddingY: 0,
    gravity: 1,
    template:"left", 
    collapsed: true,
    body: {
        rows: [
            {
                view: "edittree",
                id: 'folder',
                select: true,
//                drag: true,
//                editable: true,
                editor: "text",
                editValue: "text",
                editaction: "dblclick",
                css: "sMode",
                template: "{common.icon()}{common.folder()}&nbsp;#text#",
                url: "index.php?m=folder",
//                width:500,
                on : {
//                    onAfterSelect: function () {
//                        if (userlevel == 'user') {
//                            $$('folder_main').collapse();
//                            $$('grid_main').expand();
//                        }
//                    },
                    onAfterLoad: function() {
                       $$("folder").open(1);    
                    }
                }
            }
            ,
            {
                view: "toolbar",
                id: "toolbar_folder",
                scroll: false,
                elements: [
                    {view: "icon", id: "btnAddfolder", icon: "plus", tooltip: "Add folder", click: "addFolder()"},
                    {view: "icon", id: "btnDelFolder", icon: "minus", tooltip: "Delete folder", click: "delFolder()"},
                    {view: "icon", id: "btnEditFolder", icon: "lock", tooltip: "Permisson", click: "editFolder()"},
                    {view: "icon", id: "btnRefreshFolder", icon: "refresh", tooltip: "Refresh", click: "refreshFolder()"}
                ]
            },
        ]
    }
/*    
    on : {
       
        onItemClick: function (id,e,node) {
            $$("grid_main").collapse();
        }
    }
 */   
};

function showDetails(id) {
    webix.ajax("index.php?m=details&id=" + id, function (text) {
        var d = JSON.parse(text);
        webix.alert({
            width:500,
            type:"warning",
            align:'left',
            text:d.res
        });
    });
}

// list grid document of selected folder
var grid = {
    id: 'grid_main',
    header: "<span class='webix_icon fa-clock-o'></span>Recent",
    gravity: 3,
//    template:"left",
//    resizeColumn:true,
    body: {
        rows: [{
                id: "listdoc",
                view: "datatable",
                header: false,
                select: "row",
                multiselect: false,
                scroll: "y",
                resizeColumn: true,
                autoconfig: true,
                columns: [
                    {id:"infodoc", header:"Info", width:25, template:"<span class='webix_icon fa-info' onmouseover='showDetails(\"#id#\");'></span>", hidden:true},
                    {id: "id", header: "id", 
                        format:function(value){ 
                            var pad = "00000";
                            var rtnvalue = pad.substring(0, pad.length - value.length) + value;
                            return rtnvalue; 
                        }
                        ,adjust: "data"},                    
                    {id:"name", header: "Title", fillspace:true, template: "#name#"},
                ],
                url: "index.php?m=data",
                pager: "pager",
                on: {
                    onBeforeLoad: function () {
                        this.showOverlay("<div style='margin-top:150px;'><img src='images/loading-bar-gif-blue.gif'></div>");
                    },
                    onAfterLoad: function () {
                        this.hideOverlay();
                        if (!this.count()) {
                            // webix.alert("Document not found!");
                        }
                        $$("grid_main").define("header", '<span class=\'webix_icon fa-clock-o\'></span>Documents (' + numberWithCommas(this.count()) + ')');
//                        $$("grid_main").define("header", 'Recent');
                        $$("grid_main").refresh();
                        //console.log('hi');
                    }}
            },
            {
                id: "pager",
                view: "pager",
                template: "{common.first()} {common.prev()} {common.pages()} {common.next()} {common.last()}",
                css: {"text-align": "center", "background-color": "#F7F8F9"},
                animate: {
                    direction: "left"
                },
                autosize: true,
                group: 10
            }
        ]
    },
    
    on : {
       
        onItemClick: function (id,e,node) {
            if (userlevel == 'user') {
               $$("folder_main").collapse();
            }
//            console.log(id);
//            console.log(e);
//            console.log(node);
            
        }
    }
    
};

// show content and footer menu
var content = {
    id: "content_main",
    header: "Document",
    gravity: 5,
    template:"right", 
   //hidden: true,
    collapsed: false,
    body: {
        rows: [{
                view: "iframe",
                id: "content",
                css: {"background-color": "#F4F5F6"},
                scroll: "y"
            },
            {
                view: "toolbar",
                scroll: false,
                elements: [
                    {id: "edit_button", width: 60, view: "button", type:'icon', icon: 'edit', label: "Edit", click: "editDocument()", disabled: true, hidden:true},
                    {id: "delete_button", width: 70, view: "button", type:'icon', icon: 'trash', label: "Delete", click: "deletedata()", disabled: true, hidden:true},
                    {id: "properties_button", width: 100, view: "button", type:'icon', icon: 'info', label: "Properties", click: "showProperties()", disabled: true, hidden:false},
                    {id: "download_button", width: 100, view: "button", type:'icon', icon: 'download', label: "Download", click: "download()", disabled: true, hidden:false},
                    {id: "mail_button", width: 60, view: "button", type:'icon', icon: 'envelope-o', label: "Mail", click: "mail()", disabled: true, hidden:false},
                    {id: "fullscreen_button", width: 100, view: "button", type:'icon', icon: 'expand', label: "Full Screen", click: "fullscreen();", disabled: true, hidden:false},

//                    {},
//                    {id: "back_button", view: "button", type: "prev", click: ("moveBack();"), label: "Back", width: 50, disabled: true , hidden:false},
//                    {id: "next_button", view: "button", type: "next", click: ("moveNext();"), label: "Next", width: 50, disabled: true , hidden:false}

                ]
            }
        ]
    }};

// == End Layout Section ====

/* Pop Up window */
// advanced search form
var advsearchform = {
    view: "form",
    id: "adv_form",
    elements: [
        {view: "text", name: "docno", label: 'Document No.'},
        {view: "text", name: "title", label: 'Tittle'},
        {view: "text", name: "refno", label: 'Reference No.'},
        {view: "text", name: "keyword", label: 'Keyword'},
        {view: "text", name: "description", label: 'Remark'},
        //{view: "select", name: "folder", label: 'Folder'},
        {view: "datepicker", name: "start", label: "Start date", stringResult: true, inputWidth: 300},
        {view: "datepicker", name: "end", label: "End date", stringResult: true, inputWidth: 300},
        {margin: 5, cols: [
                {view: "select", name: "sortby", label: 'Sort By', value: "id", labelWidth: 120, options: [
                        {id: "id", value: "Document No."},
                        {id: "name", value: "Title"},
                        {id: "revise_date", value: "Date"}
                    ]},
                {view: "radio", name: "dir", label: "", value: "DESC", options: [
                        {id: "ASC", value: "ASC"},
                        {id: "DESC", value: "DESC"}
                    ]}
            ]},
        {margin: 5, cols: [
                {view: "button", type: "form", value: "Submit", inputWidth: 122, align: "right", click: function () {
                        var form = $$("adv_form");
                        if (form.validate()) {
                            var data = JSON.stringify($$("adv_form").getValues());
                            $$("listdoc").clearAll();
                            //$$("listdoc").load("data/data.php?advs=" + data + "&db=" + webix.storage.session.get('user').database);
                            $$("listdoc").load("index.php?m=data&advs=" + data);
                            $$("listdoc").refresh();
                            $$("folder").unselect();
                            $$('advsearchpopup').hide();
                        }
                        else
                            webix.message({type: "error", text: "Form data is invalid"});
                    }},
                {view: "button", label: "Cancel", inputWidth: 122, click: function () {
                        $$('advsearchpopup').hide();
                    }}
            ]}
    ],
    rules: {
        $obj: function () {
            return true;
        }
    },
    elementsConfig: {
        labelPosition: "left",
        labelWidth: 120
    }
};

// search form 
var sorttoggle = 1;
var sortform = {
    view: "list",
    scroll: false,
    yCount: 8,
    select: true,
    template: "#lang#",
    data: [
        {id: "id.ASC", lang: "Document No.: Lowest First"},
        {id: "id.DESC", lang: "Document No.: Highest First"},
        {id: "revise_date.ASC", lang: "Document Date: ASC"},
        {id: "revise_date.DESC", lang: "Document Date: DESC"},
        {id: "refno.ASC", lang: "Ref No.: ASC"},
        {id: "refno.DESC", lang: "Ref No.: DESC"},
        {id: "name.ASC", lang: "Tittle: ASC"},
        {id: "name.DESC", lang: "Title.: DESC"}
    ],
    on: {"onAfterSelect": function (data) {
            var folder = $$('folder').getSelectedId();
            var search = $$('search').getValue().toLowerCase();
            $$("listdoc").clearAll();
//            $$("listdoc").load("data/data.php?srt=" + data + "&folder_id=" + folder + "&s=" + search + "&db=" + webix.storage.session.get('user').database);
            $$("listdoc").load("index.php?m=data&srt=" + data + "&folder_id=" + folder + "&s=" + search);
            $$("listdoc").refresh();
            $$("sortpopup").hide();
            webix.message("Sorting by " + data);
        }}
};

// upload new document form
var uploadform = {
    padding: 5,
    id: "uform",
    width: 650,
    view: "form", type: "line", rows: [
        {view: "text", name: 'name', label: "Title"},
        {view: "richselect",
            label: 'Folder *',
            name: "folder_id",
            options: {
                body: {
                    url: "index.php?m=selectfolder"
                }
            },
            iconCss: "combo_icon",
            iconWidth: 20
        },
        {    
        cols:[{view: "label", label:'File',width:83},{
            view: "uploader",
            id: "uploadfiles",
            name: "uploadfiles",
            height: 37,
            width:150,
            align: "center",
            type: "iconButton",
            icon: "file-o",
            label: "Click to select",
            autosend: false,
            link: "mylist",
            upload: "index.php?m=upload",
            multiple: false,
            on : {
                onUploadComplete: function(res) {
                     //webix.message("done");
                     this.hide();
                },
                onFileUpload: function(res) {
                     webix.message("File upload.");                    
                }
                
            }
        },{}]
        },
        {cols:[{view: "label", label:'',width:82},{
            
            borderless: true,
            view: "template", id: "mylist", type: "myUploader",
            autoheight: true, minHeight: 30,
            template:function(data){
                var filedesc='...';
                var filedate;
                var filename;
                if (data.each) {
                    data.each(function(obj){
//                        console.log(obj);
                        filedesc = obj.name + ' (' + obj.sizetext+')';
                        filedate = obj.file.lastModified;
                        filename = obj.name;
                    });
                }
//               console.log(filedate);
//                console.log($$('uform').getValues().name);
                if ($$('uform').getValues().name !='') {
                    filename = $$('uform').getValues().name; 
                }
                $$('uform').setValues({
                    name : filename,
                    revise_date: new Date(filedate)
                }, true);
                return filedesc;
            }
            
        }]
        },
        {view: "datepicker", value: new Date(), format: webix.Date.dateToStr("%d/%m/%Y"), name: "revise_date", label: "Date", stringResult: true, width: 300},
        {view: "text", name: 'refno', label: "Ref No."},
        {view: "text", name: 'keyword', label: "Keyword"},
        {view: "textarea", name: 'description', label: "Description"},
        {view: "label", label: ""},
        /*
        {    
        cols:[{view: "label", label:'Attachment',width:83},{
            view: "uploader",
            id:   "uploadfilesattachment",
            name: "uploadfilesattachment",
            height: 37,
            width:150,
            align: "center",
            type: "iconButton",
            icon: "file-o",
            label: "Click to select(s)",
            autosend: false,
            link: "mylistattachment",
            upload: "index.php?m=upload",
            multiple: true,
            on : {
                onUploadComplete: function(res) {
                     webix.message("done");
                     this.hide();
                },
                onFileUpload: function(res) {
                     webix.message("file upload done");                    
                }
                
            }
        },{}]
        },
        
        {cols:[{view: "label", label:'',width:82},{
            
            borderless: true,
            view: "template", id: "mylistattachment", type: "myUploader",
            autoheight: true, minHeight: 80,
            template:function(data){
                var filedesc='';
                var filedate;
                var filename;
                if (data.each) {
                    data.each(function(obj){
                        console.log(obj);
                        filedesc += obj.name + ',';
//                        filedate = obj.file.lastModified;
//                        filename = obj.name;
                    });
                }
//               console.log(filedate);
//                console.log($$('uform').getValues().name);
//                if ($$('uform').getValues().name !='') {
//                    filename = $$('uform').getValues().name; 
//                }
//                $$('uform').setValues({
//                    name : filename,
//                    revise_date: new Date(filedate)
//                }, true);
                return filedesc;
            }
            
        }]
        },
        */
        {
            id: "uploadButtons",
            cols: [{},
                {view: "button", label: "Submit", type: "iconButton", icon: "save", click: "uploadFiles('add');", align: "center"},
                {width: 5},
                {view: "button", label: "Cancel", type: "iconButton", icon: "close", click: "cancel('add')", align: "center"},
                {}
            ]
        }        
    ]
};
// edit document form
var editform = {
    id: 'edit_form',
    width: 700,
    view: "form", rows: [
        {view: "text", name: 'name', label: "Title"},
        {view: "richselect",
            label: 'Folder',
            name: "folder_id",
            options: {
                body: {
                    url: "index.php?m=selectfolder"
                }
            },
            iconCss: "combo_icon",
            iconWidth: 20
        },
        {    
        cols:[{view: "label", label:'File',width:90},{
            view: "uploader",
            id: "editfiles",
            name: "editfiles",
            height: 37,
            width:200,
            align: "center",
            type: "iconButton",
            icon: "file-o",
            label: "Upload new document",
            autosend: false,
            link: "mylist",
            multiple: false,
            on : {
                onUploadComplete: function(res) {
//                     webix.message("done");
                     this.hide();
                },
                onFileUpload: function(res) {
                     webix.message("File upload.");                    
                }
                
            }
        },{}]
        },
         {cols:[{view: "label", label:'',width:82},{
            borderless: true,
            view: "template", id: "mylist", type: "myUploader",
            template:function(data){
                if (data.each) {
                    var filedesc="..";
                    data.each(function(obj){
//                        console.log(obj.name);
                        if (obj.name != undefined) {
                            filedesc=obj.name + ' (' + obj.sizetext+')';
                        }
                    });
                }
                return filedesc;
            }    
        }]
        },
        {cols:[{view: "label", label:'',width:82},{view: "label", name: 'filename', label: " ", disabled: true}]},
        
        {view: "text", name: 'refno', label: "Ref No."},
        {view: "datepicker", name: "revise_date", label: "Date", stringResult: true, width: 300},
        {view: "text", name: 'keyword', label: "Keyword"},
        {view: "textarea", name: 'description', label: "Description"},
        {view: "label",label:""},
        /*
        {
            view: "uploader",
            id: "editfiles",
            name: "editfiles",
            height: 37,
            align: "center",
            type: "iconButton",
            icon: "plus-circle",
            label: "Click to change a new file.",
            autosend: false,
            link: "mylist2",
            upload: "index.php?m=upload&action=changefile",
            multiple: false
        },
        {
            borderless: false,
            view: "list", id: "mylist2", type: "myUploader",
            autoheight: true, minHeight: 50
        },
 */  


        {
            id: "uploadButtons",
            cols: [{},
                {view: "button", label: "Submit", type: "iconButton", icon: "save", click: "uploadFiles('edit');", align: "center"},
                {width: 5},
                {view: "button", label: "Cancel", type: "iconButton", icon: "close", click: "cancel('edit')", align: "center"},
                {}    
            ]
        }
    ]
};

var griduser = {
    view: "datatable",
    id: "usertable",
    columns: [
        {id: "username", editor: "text", header: "EMPN", width: 90, sort: "string"},
        {id: "name", editor: "text", header: "Name", width: 300, sort: "string"},
        {id: "password", editor: "text", header: "Password", width: 100, sort: "string"},
        {id: "email", editor: "text", header: "Email", width: 150, sort: "string"},
        {id: "position", editor: "select", header: "User Level", sort: "string", options: ["User", "DocAdmin", "Admin"], width: 100},
//        {id: "name", editor: "text", header: ["Name", {content: "textFilter"}], width: 400, sort: "string"},
//        {id: "position", editor: "select", header: ["User Level", {content: "selectFilter"}], sort: "string", options: ["User", "DocAdmin", "Admin"], width: 110},
    ],
    height: 450,
    autowidth: true,
    select: "row",
    editable: true,
    editaction: "dblclick",
    save: "connector->index.php?m=user",
    url: "index.php?m=user",
    on :{
/*        onBeforeEditStart : function (data,row){
            
         var dp = webix.dp.$$('usertable');

            dp.attachEvent('onBeforeDataSend', function(obj){
                console.log(obj.data.id);
                var record = $$('usertable').getItem(obj.data.id);
                console.log(record);
                webix.ajax("index.php?m=finddupuser&username="+obj.data.username, function(text,res){
                    console.log('..'+text);
                    if (text) {
                        console.log(record.username);
                        webix.message({type:'error',text:'Duplicate username'});
                    }
                });                
            });   
        },
        
        onBeforeDataSend : function (data,row) {
            webix.message(data.value);
        },
      
 */     
        onBeforeEditStop : function (data,row){
            if (row.column == 'username' && data.value != data.old) {
                // check duplicate user
//                console.log('..check dup');
                webix.ajax("index.php?m=finddupuser&username="+data.value, function(text,res){
//                    console.log('..'+text);
                    if (text=='1') {
                        webix.message({type:'error',text:'Duplicated user!'});
                        $$('usertable').load('index.php?m=user');
                        $$('usertable').refresh();
                    }
                    else {
//                        console.log('..find egat userinfo');
                         webix.ajax("functions/findegatuser.php?id="+data.value, function(text,res){
                            if (text) {
                                //webix.message(res.json()[0].name);               
                                 var record = $$('usertable').getItem(row.row);
                                 record['email']= res.json()[0].email;
                                 record['name']= res.json()[0].name;
                                 $$('usertable').updateItem(row.row, record);
                                 $$('usertable').refresh();
                             }
                         });
                    }
                });
            }
        }
        
    }
};


var buttons = {
    view: "toolbar",
    paddingY: 10,
    margin:15, 
    elements: [{},
        {view: "button", width: 100, value: "Add User", click: function () {
                var id = $$('usertable').add({
                    name: "",
                    position: "User",
                    username: ""
                }, 0);
                $$('usertable').select(1,true);
                $$('usertable').moveSelection("top");
                $$('usertable').eachRow( function (row){ 
                    if ($$('usertable').getItem(row).username == '') {
//                        console.log( $$('usertable').getItem(row).username ) 
                        $$('usertable').edit({
                            row:row,
                            column:'username'
                });
                    }
                    
                });
 /*
                $$('usertable').edit({
                    row:0,
                    column:'name'
                });
 
                webix.message( {
                    type : "error",
                    text: "Dbclick to edit user."
                    }
                );
 */
            }},
        {view: "button", width: 100, value: "Delete User", click: function () {
                var id = $$('usertable').getSelectedId();
                if (id) {
                    webix.confirm("Delete User?", function (result) {
                        if (result) {
                            $$('usertable').remove(id);
                            webix.message("User deleted successfully.");
                        }
                    });
                }
                else {
                    webix.alert({type:"error",text:"No user is selected!"});
                }
            }
        },
        {}
    ]
};
// todo close window, add goto bottom
var edituserform = {
    rows: [
        griduser,
        buttons
    ]
};

// edit folder form
var editfolderform = {
    id: 'edit_folderform',
    view: "form",
    rows: [
        {view: "label", name: 'text', labelWidth:100},        
        {cols:[{width:1 },
                {
                    view: "datatable",
                    id: "susertable",
                    columns: [
                        {id: "name", header: "Drag authorized user to right table->", width:270, sort: "string"}
                    ],
                    css:{"border":"1px solid gray"},
                    height:300,
                    width:270,
                    select:true,
                    drag:true,
//                    url: "index.php?m=user"
                    on: {
                        onAfterDrop: function (context,event) {
                             var folder_id = $$('folder').getSelectedId();
                             var uid = this.getItem(context.start).uid;
                             console.log('remove user = ' + this.getItem(context.start).uid +'  fid ='+ folder_id);
                            // remove user from folder
                            webix.ajax("index.php?m=folderpermission&action=del&folder_id=" + folder_id +"&uid="+uid, function (text) {
                                 
                            });
                        }
                    }
                },
                {
                    width:10
                },
                {
                    view: "datatable",
                    id: "tusertable",
                    columns: [
                        {id: "name", header: "+ Authorized Users", width:270, sort: "string"},
                        {id: "uid", editor: "text", hidden: true}
                    ],
                    css:{"border":"1px solid gray"},
                    height:300,
                    width:270,
                    select:true,
                    drag:true,
                    on: {
                        onAfterDrop: function (context,event) {
                             var folder_id = $$('folder').getSelectedId();
                             var uid = this.getItem(context.start).uid;
                             console.log('add user = '+this.getItem(context.start).uid +'  fid ='+ folder_id);
                             // add user to folder
                             webix.ajax("index.php?m=folderpermission&action=add&folder_id=" + folder_id +"&uid="+uid, function (text) {
                                 
                             });
                        }
                    }
                },
                {
                    width:1
                }
            ]},

        {height:10}
/*        
        {
            id: "editfolderButtons",
            margin:15,
            cols: [{},
                {view: "button", label: "Submit", width:100,  click: "updateFolder();"},
                {view: "button", label: "Cancel", width:100,  click: "cancel('editfolder')"},{}
            ]
        }
*/        
    ],
    
    
    rules: {
        "text": webix.rules.isNotEmpty
    },
    
    on: {
        onValidationError: function (id, value) {
            if (id == "text") {
                webix.message({type: "error", text: "Folder name can't be empty."});
            }

        }
    }      
};

var mailform = {
    id: 'mailform',
    view: "form",
    width: 680,
    rows: [
        {view: "text", label: "To..", name: "to", labelWidth:60, suggest: "index.php?m=mailaddress"},
        {view: "text", label: "Cc..", name: "cc", labelWidth:60, suggest: "index.php?m=mailaddress"},
        {view: "text", label: "Subject:", name: "subject", labelWidth:60},
	{view:"ckeditor", name:"message", height:300,value:''},
        {
            margin:15,
            cols:[
                {},
                {view: "button", label: "Send", width:100,  click: "sendMail()"},
                {view: "button", label: "Cancel", width:100,  click: "$$('mailpopup').hide();"},
                {}
        ]}
    ]
};

var settingform = {
    id: 'settingform',
    view: "form",
    width: 680,
    rows: [
        {view: "select", label:"Theme:", labelWidth:80, width:250, name:"cur_theme", options:"functions/themelist.php"},
        {view: "text", label: "Title:", name: "title", labelWidth:80},
        {view: "text", label: "Copyright:", name: "copyright", labelWidth:80},
        {
            margin:15,
            cols:[
                {},
                {view: "button", label: "OK", width:100,  click: "settingSubmit()"},
                {view: "button", label: "Cancel", width:100,  click: "$$('settingpopup').hide();"},
                {}
        ]}
    ]
};

var loggingform = {
    id: 'loggingform',
    view: "form",
    rows: [
        {
            id: "listlogging",
            view: "datatable",
            height:400,
            header: true,
            select: "row",
            multiselect: false,
            scroll: "y",
            resizeColumn: true,
            autoconfig: true,
            columns: [
                {id: "modified", name:"modified", header: "Time",adjust: "data"},                    
                {id: "name", name: "name", header:"User"},
                {id: "event", name:"event", header: "Event", fillspace:true}
            ],
            url: "index.php?m=logging",
            pager: "pagerlogging",
            on: {
                onBeforeLoad: function () {
                    this.showOverlay("<div style='margin-top:150px;'><img src='images/loading-bar-gif-blue.gif'></div>");
                },
                onAfterLoad: function () {
                    this.hideOverlay();
//                    if (!this.count()) {
//                        // webix.alert("Document not found!");
//                    }
//                    $$("grid_main").define("header", '<span class=\'webix_icon fa-clock-o\'></span>Documents (' + numberWithCommas(this.count()) + ')');
//                        $$("grid_main").define("header", 'Recent');
//                    $$("grid_main").refresh();
                    //console.log('hi');
                }}
            },
            {
                id: "pagerlogging",
                view: "pager",
                template: "{common.first()} {common.prev()} {common.pages()} {common.next()} {common.last()}",
                css: {"text-align": "center", "background-color": "#F7F8F9"},
                animate: {
                    direction: "left"
                },
                autosize: true,
                group: 10
            },
        
        
        {
            margin:15,
            cols:[
                {},
                {view: "button", label: "Clear Logging", width:100,  click: "delLog()"},
                {view: "button", label: "Close", width:100,  click: "$$('loggingpopup').hide();"},
                {}
        ]}
    ]
};

// show docuemnt properties 
var propertysheet = {
    view: "property",
    id: "properties_data",
    width: 600,
    height: 280,
    editable: false,
    elements: [
        //{label: "Properties", type: "label"},
        {label: "Doc No.", id: "id", type: 'text'},
        {label: "Title", id: "name", type: "text"},
        {label: "Ref No.", id: "refno", type: "text"},
        {label: "Keyword", id: "keyword", type: "text"},
        {label: "Remark", id: "description", type: "text"},        
        {label: "Folder", id: "folder", type: "select"},
        {label: "Doc Date", id: "revise_date", type: "date", format: webix.i18n.dateFormatStr},
        {label: "Created", id: "created", type: "text"},
        {label: "Modified", id: "modified", type: "text"},
        {label: "Creator", id: "creator", type: "text"}
    ]
};

/*== Main layout ==*/
var w = window.innerWidth;
var ui_scheme = {
    type: "space", //list,space
    responsive:true,
    id: 'main_scheme',
    paddingY: 0,
    paddingX: 0,
    width: w,
    rows: [
        header,
        {
            type: "space",
            responsive:"main_scheme",
            cols: [               
                folder, 
                {view: "resizer"},
                grid, 
                {view: "resizer"},
                content
          ]
        },
        footer
   ]
};
/*== End Main layout ==*/