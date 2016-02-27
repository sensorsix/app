<?php
 
class TeamForm extends sfForm
{
  public function configure()
  {
    /** @var sfGuardUser $object */
    $object = $this->getOption('object');
    /** @var TeamMember[]|Doctrine_Collection $links */
    $team = Doctrine_Collection::create('TeamMember');
    if (!$object->isNew())
    {
      $team = $object->Team;
      foreach ($team as $i => $teamMember)
      {
        $this->embedForm($i, new TeamMemberForm($teamMember));
      }
    }

    for ($i = $team->count(); $i < 20; $i++)
    {
      $teamMember = new TeamMember();
      $teamMember->Manager = $object;
      $this->embedForm($i, new TeamMemberForm($teamMember));
    }
  }
}
 