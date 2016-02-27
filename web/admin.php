<?php


require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('admin', 'prod', true);
$context = sfContext::createInstance($configuration);

// set separate prefixes for assets and links
$routing = $context->getRouting();
$routingOptions = $routing->getOptions();
$routingOptions['context']['prefix'] = '/administration';
$routing->initialize($context->getEventDispatcher(), $routing->getCache(), $routingOptions);

$context->dispatch();