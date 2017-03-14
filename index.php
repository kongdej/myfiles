<?php
// Turn off all error reporting
error_reporting(0);

include_once 'functions/init.php';   // init database and functions 
getSettings();                  // get setting from table settings
// head control module
$module = isset($_GET['m']) ? $_GET['m'] : '';

// no heading module
switch ($module) {
    case 'login':
        if (!isUserlogin()) {
            include_once 'functions/' . $module . '.php';
            exit;
        }
        break;
    case 'logout':
        if (isUserlogin()) {
            include_once 'functions/' . $module . '.php';  //remote server provide ajax json
            header('Location: index.php?');
        }
        break;
    case 'database': //list database for login selection
        include_once 'functions/' . $module . '.php';  //remote server provide ajax json
        exit;
        break;
    default:
        if (!empty($module) && isUserlogin()) {  // if user login, do /functions/module.php
            include_once 'functions/' . $module . '.php';  //remote server provide ajax json
            exit;
        }
}
// --- End
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link href="codebase/skins/<?php echo $config['cur_theme']; ?>" rel="stylesheet" type="text/css">
        <link href="default.css" rel="stylesheet" type="text/css">
        <script src="codebase/webix.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="./ckeditor.js"></script>
        
        <script type="text/javascript" charset="utf-8">
            var org       = "MyFiles - <?php echo $config['site_name'];?>";
            var title     = "<?php echo $config['title']; ?>";
            var doccode   = "<?php echo $config['site_name']; ?>-";
            //var docpath   = "<?php echo $config['documentpath']; ?>";
            var firstdb   = "<?php echo $conig['database']['default_database']; ?>";
            var copyright = "<?php echo $config['copyright']; ?>";
            var username  = "<?php echo $_SESSION['name'].' ('.$_SESSION['level'].')'; ?>";
            webix.codebase = "./";
        </script>

        <script src="layout.js" type="text/javascript" charset="utf-8"></script>
        <script src="logic.js" type="text/javascript" charset="utf-8"></script>
        <title><?php echo $config['var']['title']; ?></title>
    </head>
    <body>
        <script type="text/javascript" charset="utf-8">
            webix.ready(function () {
                <?php
                // Not login show login form
                if (!isUserlogin()) {
                    ?>
                        webix.ui({
                            view: "window",
                            position: "center",
                            modal: true,
                            head: {view: "toolbar", cols: [{view: "label", label: "Login: "+org, align: 'center'}]},
                            body: webix.copy(loginform)
                        }).show();
                    <?php
                // User Logged in
                } else {
                    ?>
                        webix.ui(ui_scheme).show();
                        logic.init('<?php echo $_SESSION['level']; ?>');
                    <?php
                }
                ?>
            });
        </script>
    </body>
</html>