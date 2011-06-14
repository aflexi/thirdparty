<html>
        <script type="text/javascript">
var bandwidth_package = <?php echo !empty($_REQUEST['callback_result']) ? urldecode($_REQUEST['callback_result']) : '{}' ?>;
// Making the top frame to redirect to package-list.php
window.parent.document.location = '/aflexi/index.php?module=settings&controller=package';
        </script>
    <body>
        CDN package <b><script type="text/javascript">document.write(bandwidth_package.name);</script></b> has been successfully updated.
    </body>
</html>