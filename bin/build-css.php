<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;

$compiler = new Compiler();
$compiler->setOutputStyle(OutputStyle::EXPANDED);
$compiler->setImportPaths(dirname(__DIR__) . '/resources/scss');

$scss = file_get_contents(dirname(__DIR__) . '/resources/scss/app.scss');
$css = $compiler->compileString($scss)->getCss();

file_put_contents(dirname(__DIR__) . '/public/css/app.css', $css);

echo "Готово: resources/scss/app.scss -> public/css/app.css\n";
