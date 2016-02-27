<?php

/**
 * Roadmap form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RoadmapCreateFirstStepForm extends BaseRoadmapForm
{
  public function configure()
  {
    unset($this['folder_id'], $this['user_id'], $this['description'], $this['active'], $this['show_items'], $this['show_releases'], $this['show_dependencies'], $this['show_description'], $this['workspace_mode']);

    $this->setValidators(array(
      'name' => new sfValidatorString(array('required' => true)),
      'token' => new sfValidatorString(array('required' => false))
    ));

    $this->disableCSRFProtection();
  }
}
