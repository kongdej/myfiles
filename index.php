<?php
error_reporting(0);
include_once 'functions/init.php';   // init functions and routing module
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
            var config    = <?php echo json_encode($config);?>;
            var view      = <?php echo json_encode($view);?>;
            var session   = <?php echo json_encode($_SESSION);?>;
            var org = 'MyFiles - '+config.title;
            webix.codebase = "./";
        </script>
        <script src="layout.js" type="text/javascript" charset="utf-8"></script>
        <script src="logic.js" type="text/javascript" charset="utf-8"></script>
        <title><?php echo $config['var']['title']; ?></title>
    </head>
    <body>
        <script type="text/javascript" charset="utf-8">
            webix.ready(function () {
                <?php if (!isUserlogin()) { // show login form ?>
                    webix.ui({
                        view: "window",
                        position: "center",
                        modal: true,
                        head: {view: "toolbar", cols: [{view: "label", label: org, align: 'center'}]},
                        body: webix.copy(loginform)
                    }).show();
                <?php } else { // load ui scheme main content ?>
                    webix.ui(ui_scheme).show();
                    logic.init('<?php echo $_SESSION['level']; ?>');
                <?php } ?>
            });
        </script>
    </body>
</html>