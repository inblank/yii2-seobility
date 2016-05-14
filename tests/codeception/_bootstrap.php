<?php
// This is global bootstrap for autoloading

// fix problems with very slow test
\Codeception\Specify\Config::setDeepClone(false);

// prepare yii2 console application structure
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', __DIR__ . '/../');
