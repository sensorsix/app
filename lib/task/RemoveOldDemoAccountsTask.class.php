<?php

class RemoveDemoAccountsTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'dmp';
    $this->name             = 'clean-demo';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [RemoveDemoAccounts|INFO] task does things.
Call it with:

  [php symfony RemoveDemoAccounts|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    // add your code here
    $result = Doctrine_Query::create()->addFrom('sfGuardUser u')->where('TIMESTAMPDIFF(HOUR, u.created_at, NOW()) > 24')
      ->delete()
      ->execute();

    $this->logSection('dmp', 'Demo accounts deleted: ' . number_format($result));
  }
}
