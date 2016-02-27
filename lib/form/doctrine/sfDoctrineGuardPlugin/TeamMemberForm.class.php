<?php

/**
 * TeamMember form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrinePluginFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TeamMemberForm extends PluginTeamMemberForm
{
  public function configure()
  {
    unset($this['manager_id'], $this['user_id']);

    $this->embedForm('user', new UserEmbedForm($this->getObject()->User));
  }
}
