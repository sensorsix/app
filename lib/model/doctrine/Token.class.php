<?php

/**
 * Token
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Token extends BaseToken
{
  const STATUS_ACCESS = 'access';
  const STATUS_REQUEST = 'request';

  public function getParams()
  {
    $params = $this->_get('params');

    return (array) json_decode($params);
  }

  public function getParam($key, $default = null)
  {
    $params = $this->getParams();

    return isset($params[$key])?$params[$key]:$default;
  }

  public function setParams($params)
  {
    $this->_set('params', json_encode($params), false);
  }

  public function setParam($key, $value)
  {
    $params = $this->getParams();
    $params[$key] = $value;

    $this->setParams($params);
  }
}