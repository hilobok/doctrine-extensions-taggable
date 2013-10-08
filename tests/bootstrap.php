<?php

echo __DIR__;

define('VENDOR_PATH', realpath(__DIR__ . '/../vendor'));

if (!class_exists('PHPUnit_Framework_TestCase') ||
    version_compare(PHPUnit_Runner_Version::id(), '3.5') < 0
) {
    die('PHPUnit framework is required, at least 3.5 version');
}

if (!class_exists('PHPUnit_Framework_MockObject_MockBuilder')) {
    die('PHPUnit MockObject plugin is required, at least 1.0.8 version');
}

/** @var $loader ClassLoader */
$loader = require VENDOR_PATH . '/autoload.php';

// AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
// AnnotationRegistry::registerFile(__DIR__ . '/Mapping/Annotation/All.php');