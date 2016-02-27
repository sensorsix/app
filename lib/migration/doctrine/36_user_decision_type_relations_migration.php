<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class UserDecisionTypeRelationsMigration extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->createForeignKey(
      'user_decision_type',
      'user_decision_type_type_id_decision_type_id',
      array(
        'name'         => 'user_decision_type_type_id_decision_type_id',
        'local'        => 'type_id',
        'foreign'      => 'id',
        'foreignTable' => 'decision_type',
        'onUpdate'     => '',
        'onDelete'     => 'CASCADE',
      )
    );
    $this->createForeignKey(
      'user_decision_type',
      'user_decision_type_user_id_sf_guard_user_id',
      array(
        'name'         => 'user_decision_type_user_id_sf_guard_user_id',
        'local'        => 'user_id',
        'foreign'      => 'id',
        'foreignTable' => 'sf_guard_user',
        'onUpdate'     => '',
        'onDelete'     => 'CASCADE',
      )
    );
    $this->addIndex(
      'user_decision_type',
      'type_id',
      array(
        'fields' =>
        array(
          0 => 'type_id',
        ),
      )
    );
    $this->addIndex(
      'user_decision_type',
      'user_id',
      array(
        'fields' =>
        array(
          0 => 'user_id',
        ),
      )
    );
  }

  public function postUp()
  {
    $types = DecisionTypeTable::getInstance()->findAll();
    $users = sfGuardUserTable::getInstance()->findAll();

    foreach ($types as $type)
    {
      foreach ($users as $user)
      {
        $userType = new UserDecisionType();
        $userType->Type = $type;
        $userType->User = $user;
        $userType->save();
      }
    }
  }

  public function down()
  {
    $this->dropForeignKey('user_decision_type', 'user_decision_type_type_id_decision_type_id');
    $this->dropForeignKey('user_decision_type', 'user_decision_type_user_id_sf_guard_user_id');
    $this->removeIndex(
      'user_decision_type',
      'type_id',
      array(
        'fields' =>
        array(
          0 => 'type_id',
        ),
      )
    );
    $this->removeIndex(
      'user_decision_type',
      'user_id',
      array(
        'fields' =>
        array(
          0 => 'user_id',
        ),
      )
    );
  }
}