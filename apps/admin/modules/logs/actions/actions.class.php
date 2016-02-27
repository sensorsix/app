<?php

/**
 * logs actions.
 *
 * @package    dmp
 * @subpackage logs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class logsActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->page = $request->getParameter('page') ?: 1;

    $this->logs = Doctrine_Core::getTable('Log')
      ->createQuery('a')->limit('10')->orderBy('id DESC')->offset(($this->page - 1) * 10)
      ->execute();

    $this->logs_count = Doctrine_Core::getTable('Log')
      ->createQuery('a')
      ->execute()->count();
  }
}
