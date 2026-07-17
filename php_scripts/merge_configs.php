<?php

use Espo\Core\InjectableFactory;
use Espo\Core\Utils\Config\ConfigWriter;

/** @var \Espo\Core\Application $app */
$app = include __DIR__ . '/bootstrap_app.php';

$configWriter = $app->getContainer()->getByClass(InjectableFactory::class)->create(ConfigWriter::class);

if (file_exists('../config.php')) {
    $override = include('../config.php');

    foreach ($override as $key => $value) {
        $configWriter->set($key, $value);
    }

    $configWriter->save();
}
