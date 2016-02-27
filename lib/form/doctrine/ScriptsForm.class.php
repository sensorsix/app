<?php

/**
 * Scripts form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ScriptsForm extends BaseScriptsForm
{
  public function configure()
  {
    $this->widgetSchema['backend_top']    = new sfWidgetFormTextarea();
    $this->widgetSchema['backend_bottom'] = new sfWidgetFormTextarea();
    $this->widgetSchema['frontend_top']   = new sfWidgetFormTextarea();
    $this->widgetSchema['frontend_bottom']= new sfWidgetFormTextarea();
  }
}
