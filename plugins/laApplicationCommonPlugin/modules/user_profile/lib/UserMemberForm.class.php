<?php

class UserMemberForm extends BasesfGuardUserForm
{
  protected $formatterName = 'bootstrap_horizontal';
  
  private $team_members_to_delete_ids = array();

  public function configure()
  {
    $this->embedForm('team', new TeamForm(array(), array('object' => $this->getObject())));

    $this->mergePostValidator(
      new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array( 'invalid' => 'The two passwords must be the same.' ))
    );

    $this->useFields(array(
      'team'
    ));

    foreach ($this->widgetSchema->getFields() as $widget) {
      if (in_array($widget->getOption('type'), array( 'text', 'password' )) || $widget instanceof sfWidgetFormChoiceBase) {
        $widget->setAttribute('class', 'form-control');
      }
    }
  }
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if (isset($taintedValues['team'])) {
      foreach($taintedValues['team'] as $i => $team) {
        // When all fields are empty and slot is not the first then slot is not taken into account
        if (!$team['user']['email_address'] && !$team['user']['password']) {
          $this->validatorSchema['team'][$i]['user']['email_address']->setOption('required', false);
          $this->validatorSchema['team'][$i]['user']['password']->setOption('required', false);
          if (!$this['team'][$i]['id']->getValue()) {
            unset($taintedValues['team'][$i], $this->embeddedForms['team'][$i]);
          } else {
            $this->team_members_to_delete_ids[] = $taintedValues['team'][$i]['id'];
            unset($taintedValues['team'][$i], $this->embeddedForms['team'][$i]);
          }
        }
      }
    }

    parent::bind($taintedValues, $taintedFiles);
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $con) {
      $con = $this->getConnection();
    }

    if (null === $forms) {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $form) {
      if ($form instanceof TeamMemberForm) {
        /** @var TeamMember $teamMember  */
        $teamMember = $form->getObject();

        if (!in_array($teamMember->id, $this->team_members_to_delete_ids)) {
          $teamMember->save($con);
          $form->saveEmbeddedForms($con);
        }
      } else {
        $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
      }
    }

    if (count($this->team_members_to_delete_ids)) {
      $conn = Doctrine_Manager::connection();
      foreach ($this->team_members_to_delete_ids as $id){
        $conn->exec('DELETE t, s FROM team_member t LEFT JOIN sf_guard_user s ON t.user_id = s.id WHERE t.id = :user_id  AND t.manager_id = :manager_id', array(':user_id' => $id, ':manager_id' => sfContext::getInstance()->getUser()->getGuardUser()->getId()) );
      }
    }
  }
}