<?php

/**
 * promo actions.
 *
 * @package    dmp
 * @subpackage promo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class promoActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->page = $request->getParameter('page') ?: 1;

    $this->promocodes = Doctrine_Core::getTable('PromoCode')
      ->createQuery('a')
      ->limit('10')->orderBy('id DESC')->offset(($this->page - 1) * 10)
      ->execute();

    $this->promocodes_count = Doctrine_Core::getTable('PromoCode')
      ->createQuery('a')
      ->execute()->count();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new PromocodeForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new PromocodeForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($promocode = Doctrine_Core::getTable('Promocode')->find(array($request->getParameter('id'))), sprintf('Object promocode does not exist (%s).', $request->getParameter('id')));
    $this->form = new PromocodeForm($promocode);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($promocode = Doctrine_Core::getTable('Promocode')->find(array($request->getParameter('id'))), sprintf('Object promocode does not exist (%s).', $request->getParameter('id')));
    $this->form = new PromocodeForm($promocode);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($promocode = Doctrine_Core::getTable('Promocode')->find(array($request->getParameter('id'))), sprintf('Object promocode does not exist (%s).', $request->getParameter('id')));
    $promocode->delete();

    $this->redirect('promo/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $form->save();
      $this->redirect('@promo');
    }
  }
}
