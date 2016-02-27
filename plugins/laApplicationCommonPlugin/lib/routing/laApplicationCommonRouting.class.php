<?php

class laApplicationCommonRouting
{
  public static function addRouteForUserProfile(sfEvent $event)
  {
    $r = $event->getSubject();
    $r->prependRoute('user_profile', new sfRoute('/user/account', array('module' => 'user_profile', 'action' => 'index')));
    $r->prependRoute('user_profile\save', new sfRoute('/user/account/save', array('module' => 'user_profile', 'action' => 'save')));
  }
}