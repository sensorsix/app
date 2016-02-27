<?php

/**
 * LogicalFilter form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LogicalFilterForm extends BaseLogicalFilterForm
{
  public function configure()
  {
    unset($this['decision_id']);

    $this->widgetSchema['criterion_id']->setOption('query', CriterionTable::getInstance()->createQuery()->where('decision_id = ?', $this->getObject()->decision_id));
    $this->validatorSchema['criterion_id']->setOption('query', CriterionTable::getInstance()->createQuery()->where('decision_id = ?', $this->getObject()->decision_id));
  }
}
