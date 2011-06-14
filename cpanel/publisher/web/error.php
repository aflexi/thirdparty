<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Oops! An Error Has Occurred</title>
        <!--[if IE]>
            <script src="js/base/vendor/html5.js"></script>
        <![endif]-->
        <link rel="stylesheet" type="text/css" media="screen" href="css/base/base.css" />
        <link rel="stylesheet/less" type="text/css" media="screen" href="css/base/error.less" />
        <script type="text/javascript" src="js/base/vendor/less.js"></script>
    </head>
    <body id="error">
        <section>
            <h1>Oops! An error has occurred</h1>
            <p>
                Your request could not be processed at this time due to an error<?php echo @$_REQUEST['error_message'] ? " (<em>{$_REQUEST['error_message']}</em>)" : '' ?>.
                It is likely that the configurations are incorrect or you are not granted with the required access to access this cPanel feature.
            </p>
            <p>
                You may retry a few minutes later or contact the administrator if problem persists.
            </p>
        </section>
    </body>
</html>