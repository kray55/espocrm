<?php

include '../site/bootstrap.php';

$app = new \Espo\Core\Application();
$app->setupSystemUser();

return $app;
