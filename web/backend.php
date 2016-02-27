<?php


require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'prod', false);
$context = sfContext::createInstance($configuration);

// set separate prefixes for assets and links
$routing = $context->getRouting();
$routingOptions = $routing->getOptions();
$routingOptions['context']['prefix'] = '/project';
$routing->initialize($context->getEventDispatcher(), $routing->getCache(), $routingOptions);

$context->dispatch();
