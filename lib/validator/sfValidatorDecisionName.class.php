<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorString validates a string. It also converts the input value to a string.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorString.class.php 12641 2008-11-04 18:22:00Z fabien $
 */
class sfValidatorDecisionName extends sfValidatorString
{

  /**
   * @param array $options
   * @param array $messages
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->addMessage('busy_name', 'A project with that name already exists');

    $this->addOption('user_id');
    $this->addOption('decision_id');
  }

  /**
   * @param mixed $value
   * @return mixed|string
   * @throws sfValidatorError
   */
  protected function doClean($value)
  {
    $clean = parent::doClean($value);

    if (!DecisionTable::getInstance()->verifyAvailableNameByUserId($this->getOption('user_id'), $clean, $this->getOption('decision_id'))){
      throw new sfValidatorError($this, 'busy_name');
    }

    return $clean;
  }
}
