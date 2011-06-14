<?php
$context['packages'] = $packageHelper->getPackages();

echo $afx_template->renderTemplate('listPackages.html', $context);
?>