<?php

$prompt = function (string $message): string {
    fwrite(\STDOUT, $message . "\n");

    $fh = fopen('php://stdin', 'r');
    $value = trim(fgets($fh));
    fclose($fh);

    return $value;
};

$name = $prompt("Enter an extension name:");

$nameLabel = $name;

$name = ucfirst($name);

$name = str_replace(' ', '', ucwords(preg_replace('/[^a-z0-9]+/i', ' ', $name)));
$nameHyphen = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));

$description = $prompt("Enter a description text:");

if (substr($description, -1) !== '.') $description .= '.';

$author = $prompt("Enter an author name:");

$es6 = $prompt("Do you want to use ES6 modules in frontend? [y/n]") === 'y';
$bundled = $es6 ? "true" : "false";
$jsTranspiled = $es6 ? "true" : "false";

$replacePlaceholders = function (string $file) use ($name, $nameHyphen, $nameLabel, $description, $author, $bundled, $jsTranspiled)
{
    $content = file_get_contents($file);

    if ($content === false) {
        throw new \RuntimeException("Unable to read file '{$file}'.");
    }

    $content = str_replace('{@name}', $name, $content);
    $content = str_replace('{@nameHyphen}', $nameHyphen, $content);
    $content = str_replace('{@nameLabel}', $nameLabel, $content);
    $content = str_replace('{@description}', $description, $content);
    $content = str_replace('{@author}', $author, $content);
    $content = str_replace('{@bundled}', $bundled, $content);
    $content = str_replace('{@jsTranspiled}', $jsTranspiled, $content);

    if (file_put_contents($file, $content) === false) {
        throw new \RuntimeException("Unable to write file '{$file}'.");
    }
};

$replacePlaceholders('package.json');
$replacePlaceholders('extension.json');
$replacePlaceholders('jsconfig.json');
$replacePlaceholders('config-default.json');
$replacePlaceholders('README.md');
$replacePlaceholders('src/files/custom/Espo/Modules/MyModuleName/Resources/module.json');

if ($es6) {
    $content = <<<CLIENT_JSON
{
  "scriptList": [
      "__APPEND__",
      "client/custom/modules/{@nameHyphen}/lib/init.js"
  ]
}
CLIENT_JSON;

    $path = 'src/files/custom/Espo/Modules/MyModuleName/Resources/metadata/app/';

    if (!is_dir($path) && !mkdir($path, 0755, true) && !is_dir($path)) {
        throw new \RuntimeException("Unable to create directory '{$path}'.");
    }

    $path .= "client.json";

    if (file_put_contents($path, $content) === false) {
        throw new \RuntimeException("Unable to write file '{$path}'.");
    }

    $replacePlaceholders($path);
}

$renameOrFail = function (string $from, string $to): void {
    if (!rename($from, $to)) {
        throw new \RuntimeException("Unable to rename '{$from}' to '{$to}'.");
    }
};

$renameOrFail('src/files/custom/Espo/Modules/MyModuleName', 'src/files/custom/Espo/Modules/'. $name);
$renameOrFail('src/files/client/custom/modules/my-module-name', 'src/files/client/custom/modules/'. $nameHyphen);

$renameOrFail('tests/unit/Espo/Modules/MyModuleName', 'tests/unit/Espo/Modules/'. $name);
$renameOrFail('tests/integration/Espo/Modules/MyModuleName', 'tests/integration/Espo/Modules/'. $name);

echo "Ready. Now you need to run 'npm install'.\n";
