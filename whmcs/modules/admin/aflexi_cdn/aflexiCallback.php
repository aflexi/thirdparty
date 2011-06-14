<?php
$context['callback'] = $_REQUEST['callback'];

echo $afx_template->renderTemplate('callback.html', $context);
?>