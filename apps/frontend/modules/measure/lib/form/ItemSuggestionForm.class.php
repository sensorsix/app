<?php

/**
 * Class ItemSuggestionForm
 */
class ItemSuggestionForm extends AlternativeForm
{
  public function configure()
  {
    parent::configure();

    $this->widgetSchema['additional_info'] = new sfWidgetFormTextarea();

    $this->useFields(array('name', 'additional_info'));
  }
}
 