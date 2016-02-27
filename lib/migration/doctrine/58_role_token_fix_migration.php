<?php

class RoleTokenFixMigration extends Doctrine_Migration_Base
{
  public function up()
  {
    $roles = RoleTable::getInstance()->createQuery('r')
      ->groupBy('token')
      ->having('COUNT(id) >= 2')
      ->execute();

    while($roles->count())
    {
      foreach ($roles as $role)
      {
        $token = substr(md5(uniqid()), 0, 6);
        while (RoleTable::getInstance()->findOneBy('token', $token))
        {
          $token = substr(md5(uniqid()), 0, 6);
        }

        $role->token = $token;
        $role->save();
      }

      $roles = RoleTable::getInstance()->createQuery('r')
        ->groupBy('token')
        ->having('COUNT(id) >= 2')
        ->execute();
    }
  }

  public function down()
  {
  }
}
 