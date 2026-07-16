<?php

/** @var \Espo\Core\Application $app */
$app = include __DIR__ . '/bootstrap_app.php';

require __DIR__ . '/run_extension_script.php';

runExtensionScript($app, '../src/scripts/AfterInstall.php', 'AfterInstall');
