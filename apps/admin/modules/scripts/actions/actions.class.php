<?php

require_once dirname(__FILE__).'/../lib/scriptsGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/scriptsGeneratorHelper.class.php';

/**
 * scripts actions.
 *
 * @package    dmp
 * @subpackage scripts
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class scriptsActions extends autoScriptsActions
{
  public function executeEdit(sfWebRequest $request)
  {
    $this->scripts = Doctrine::getTable("Scripts")->getSingleton();
    $this->form = $this->configuration->getForm($this->scripts);
  }
}
