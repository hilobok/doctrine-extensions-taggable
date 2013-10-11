<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

if (!class_exists('PHPUnit_Framework_TestCase') ||
    version_compare(PHPUnit_Runner_Version::id(), '3.5') < 0
) {
    die('PHPUnit framework is required, at least 3.5 version');
}

if (!class_exists('PHPUnit_Framework_MockObject_MockBuilder')) {
    die('PHPUnit MockObject plugin is required, at least 1.0.8 version');
}

/** @var $loader ClassLoader */
$loader = require __DIR__ . '/../../../autoload.php';
$loader->add('Anh\Taggable\Fixtures', __DIR__);
$loader->add('Anh\Taggable\Tool', __DIR__);

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
