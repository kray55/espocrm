<?php

/**
 * Includes an extension script file and runs its class against the container.
 */
function runExtensionScript(\Espo\Core\Application $app, string $scriptFile, string $className): void
{
    if (!file_exists($scriptFile)) {
        return;
    }

    include $scriptFile;

    $script = new $className();
    $script->run($app->getContainer());
}
