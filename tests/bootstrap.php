<?php

// ensure we get report on all possible php errors
error_reporting(-1);

const YII_ENABLE_ERROR_HANDLER = false;
const YII_DEBUG = true;
const YII_ENV = 'test';

$_SERVER['SCRIPT_NAME'] = '/' . __DIR__;
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

if (is_dir(__DIR__ . '/../vendor/')) {
    $vendorRoot = __DIR__ . '/../vendor'; //this extension has its own vendor folder
} else {
    $vendorRoot = __DIR__ . '/../../..'; //this extension is part of a project vendor folder
}
require_once($vendorRoot . '/autoload.php');
require_once($vendorRoot . '/yiisoft/yii2/Yii.php');
Yii::setAlias('@yiiunit/extensions/statscore', __DIR__);
Yii::setAlias('@simialbi/yii2/statscore', dirname(__DIR__) . '/src');

require_once __DIR__ . '/TestCase.php';
