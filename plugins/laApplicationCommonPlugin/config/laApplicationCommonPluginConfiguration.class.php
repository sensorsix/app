<?php

class laApplicationCommonPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    foreach (array('user_profile') as $module)
    {
      if (in_array($module, sfConfig::get('sf_enabled_modules', array())))
      {
        $this->dispatcher->connect('routing.load_configuration', array('laApplicationCommonRouting', 'addRouteFor' . ucwords(str_replace('_', '', $module))));
      }
    }
  }
}
