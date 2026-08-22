<?php

declare(strict_types=1);

use App\Config\Database;
use App\Support\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

$smarty = new Smarty();
$smarty->setTemplateDir(dirname(__DIR__) . '/templates');
$smarty->setCompileDir(dirname(__DIR__) . '/templates_c');
$smarty->setCacheDir(dirname(__DIR__) . '/cache');
$smarty->caching = false;

$dbStatus = 'not checked';

try {
    Database::connection();
    $dbStatus = 'connected';
} catch (\Throwable $e) {
    $dbStatus = 'error: ' . $e->getMessage();
}

$smarty->assign('phpVersion', PHP_VERSION);
$smarty->assign('smartyVersion', Smarty::SMARTY_VERSION);
$smarty->assign('dbStatus', $dbStatus);
$smarty->display('status.tpl');
