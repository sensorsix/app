<?php

/**
 * Tag form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TagForm extends BaseTagForm
{
  public function configure()
  {
	  $this->widgetSchema['user_id']->setOption('query', sfGuardUserTable::getInstance()->getUsersInTeamQuery(sfContext::getInstance()->getUser()->getGuardUser()));
	  $this->validatorSchema['user_id']->setOption('query', sfGuardUserTable::getInstance()->getUsersInTeamQuery(sfContext::getInstance()->getUser()->getGuardUser()));
  }
}
