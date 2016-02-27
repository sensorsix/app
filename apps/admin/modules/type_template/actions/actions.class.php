<?php

/**
 * type_template actions.
 *
 * @package    dmp
 * @subpackage type_template
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class type_templateActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->page = $request->getParameter('page') ?: 1;

    $this->type_templates = Doctrine_Core::getTable('TypeTemplate')
      ->createQuery('a')
      ->execute();

    $this->element_count = Doctrine_Core::getTable('PromoCode')
      ->createQuery('a')
      ->execute()->count();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new TypeTemplateForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new TypeTemplateForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($type_template = Doctrine_Core::getTable('TypeTemplate')->find(array($request->getParameter('id'))), sprintf('Object type_template does not exist (%s).', $request->getParameter('id')));
    $this->form = new TypeTemplateForm($type_template);

    $this->criteria = Doctrine_Core::getTable('CriteriaTemplate')->findBy('template_id', $request->getParameter('id'));
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($type_template = Doctrine_Core::getTable('TypeTemplate')->find(array($request->getParameter('id'))), sprintf('Object type_template does not exist (%s).', $request->getParameter('id')));
    $this->form = new TypeTemplateForm($type_template);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($type_template = Doctrine_Core::getTable('TypeTemplate')->find(array($request->getParameter('id'))), sprintf('Object type_template does not exist (%s).', $request->getParameter('id')));
    $type_template->delete();

    $this->redirect('type_template/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $type_template = $form->save();

      $this->redirect('type_template/edit?id='.$type_template->getId());
    }
  }

  protected function processCriteriaForm(sfWebRequest $request, sfForm $form, $template_id = 0)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($template_id){
      $form->getObject()->setTemplateId($template_id);
    }
    if ($form->isValid())
    {
      $criteria_template = $form->save();

      $this->redirect('type_template/criteriaEdit?id='.$criteria_template->getId());
    }
  }

  public function executeCriteriaEdit(sfWebRequest $request)
  {
    $this->forward404Unless($criteria_template = Doctrine_Core::getTable('CriteriaTemplate')->find(array($request->getParameter('id'))), sprintf('Object criteria_template does not exist (%s).', $request->getParameter('id')));
    $this->form = new CriteriaTemplateForm($criteria_template);
  }

  public function executeCriteriaUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($criteria_template = Doctrine_Core::getTable('CriteriaTemplate')->find(array($request->getParameter('id'))), sprintf('Object criteria_template does not exist (%s).', $request->getParameter('id')));
    $this->form = new CriteriaTemplateForm($criteria_template);

    $this->processCriteriaForm($request, $this->form);

    $this->setTemplate('criteriaEdit');
  }

  public function executeCriteriaDelete(sfWebRequest $request)
  {
    $this->forward404Unless($criteria_template = Doctrine_Core::getTable('CriteriaTemplate')->find(array($request->getParameter('id'))), sprintf('Object criteria_template does not exist (%s).', $request->getParameter('id')));
    $criteria_template->delete();

    $this->redirect('type_template/edit?id='.$criteria_template->Template->getId());
  }

  public function executeCriteriaNew(sfWebRequest $request)
  {
    $this->form = new CriteriaTemplateForm();
    $this->template_id = $request->getParameter('template_id');
  }


  public function executeCriteriaCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new CriteriaTemplateForm();

    $this->processCriteriaForm($request, $this->form, $request->getParameter('template_id'));

    $this->setTemplate('criteriaNew');
  }
}
