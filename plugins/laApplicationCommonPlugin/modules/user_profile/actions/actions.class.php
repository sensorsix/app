<?php

/**
 * user_profile actions.
 *
 * @package    dmp
 * @subpackage user_profile
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class user_profileActions extends sfActions
{
  public function preExecute()
  {
    $this->getResponse()->setSlot('my_account', true);
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->getResponse()->setSlot('disable_support', true);
    $this->form = new UserProfileForm($this->getUser()->getGuardUser());

    if ($request->getMethod() == sfRequest::POST) {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $this->form->save();
        $this->getUser()->setFlash('notice', 'Your profile has been successfully updated.');
        $this->redirect('@user_profile');
      }
    }
  }

  public function executeApi(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->getGuardUser()->hasAPIAccess());
  }

  public function executeTemplateEditor(sfWebRequest $request)
  {
    $this->forward404Unless($this->getUser()->isSuperAdmin() || in_array($this->getUser()->getGuardUser()->account_type, array('Trial', 'Enterprise')));
    $this->collection_json = TypeTemplateTable::getInstance()->getListJSON($this->getUser()->getGuardUser()->getId());
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeTemplateNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $typeTemplate = new TypeTemplate();
    $typeTemplate->type_id = 1; // Generic
    $typeTemplate->user_id = $this->getUser()->getGuardUser()->getId();
    $typeTemplate->save();
    $form = new TypeTemplateForm($typeTemplate);
    $collection_json = CriteriaTemplateTable::getInstance()->getListJSON($typeTemplate);

    return $this->renderPartial('template_form', array('form' => $form, 'collection_json' => $collection_json));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeTemplateEdit(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var TypeTemplate $typeTemplate */
    $typeTemplate = $this->getRoute()->getObject();
    $form = new TypeTemplateForm($typeTemplate);
    $collection_json = CriteriaTemplateTable::getInstance()->getListJSON($typeTemplate);

    return $this->renderPartial('template_form', array('form' => $form, 'collection_json' => $collection_json));
  }

  public function executeTemplateUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var TypeTemplate $typeTemplate */
    $typeTemplate = $this->getRoute()->getObject();

    $form = new TypeTemplateForm($typeTemplate);

    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid()) {
      $form->save();
      return sfView::NONE;
    }

    return $this->renderPartial('template_form', array('form' => $form));
  }

  public function executeTemplateDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var TypeTemplate $typeTemplate */
    $typeTemplate = TypeTemplateTable::getInstance()->findByIdAndUserId($request->getParameter('id'), $this->getUser()->getGuardUser()->getId());
    $this->forward404Unless(is_object($typeTemplate));
    $typeTemplate->delete();
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    return $this->renderText(json_encode(array('status' => 1)));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeTemplateFetch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var TypeTemplate $typeTemplate */
    $typeTemplate = $this->getRoute()->getObject();
    $this->forward404Unless(is_object($typeTemplate));
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode($typeTemplate->getRowData()));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeCriteriaTemplateNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $criteriaTemplate = new CriteriaTemplate();
    $criteriaTemplate->template_id = $request->getParameter('template_id');
    $criteriaTemplate->save();
    $form = new CriteriaTemplateForm($criteriaTemplate);

/*    $log = new Log();
    $log->action = 'criteria_create';  // TODO change name
    $log->information = json_encode(array('criteria_name' => '', 'criteria_id' => $criteriaTemplate->id, 'template_id' => $criteriaTemplate->template_id));
    $log->user_id = $this->getUser()->getGuardUser()->id;
    $log->save();*/

    return $this->renderPartial('criteria_template_form', array('form' => $form));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeCriteriaTemplateEdit(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var CriteriaTemplate $criteriaTemplate */
    $criteriaTemplate = $this->getRoute()->getObject();
    $form = new CriteriaTemplateForm($criteriaTemplate);

    return $this->renderPartial('criteria_template_form', array('form' => $form));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView|string
   */
  public function executeCriteriaTemplateUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var TypeTemplate $typeTemplate */
    $typeTemplate = $this->getRoute()->getObject();

    $form = new CriteriaTemplateForm($typeTemplate);

    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      $form->save();

/*      $log = new Log();
      $log->action = 'criteria_update'; // TODO change name
      $log->information = json_encode(array('criteria_name' => $form['name']->getValue(), 'criteria_id' => $request->getParameter('id'), 'template_id' => $request->getParameter('criteria_template')['id']));
      $log->user_id = $this->getUser()->getGuardUser()->id;
      $log->save();*/

      return sfView::NONE;
    }

    return $this->renderPartial('criteria_template_form', array('form' => $form));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeCriteriaTemplateDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var TypeTemplate $typeTemplate */
    $typeTemplate = CriteriaTemplateTable::getInstance()->find($request->getParameter('id'));
    $this->forward404Unless(is_object($typeTemplate));
    $typeTemplate->delete();
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    return $this->renderText(json_encode(array('status' => 1)));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeCriteriaTemplateFetch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var CriteriaTemplate $criteriaTemplate */
    $criteriaTemplate = $this->getRoute()->getObject();

    $this->forward404Unless(is_object($criteriaTemplate));
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode($criteriaTemplate->getRowData()));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeGenerateToken(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var sfGuardUser $user */
    $user = $this->getUser()->getGuardUser();
    if ($user->Tokens->getLast()) {
      $user->Tokens->getLast()->delete();
    }

    /** @var Token $token */
    $token = $this->getUser()->getGuardUser()->generateToken();
    $token->save();

    return $this->renderText($token->token_key);
  }

  public function executeMembers(sfWebRequest $request){
    $this->forward404Unless($this->getUser()->isSuperAdmin() || in_array($this->getUser()->getGuardUser()->account_type, array('Trial', 'Enterprise')));

    $this->form = new UserMemberForm($this->getUser()->getGuardUser());

    if ($request->getMethod() == sfRequest::POST) {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $this->form->save();
        $this->getUser()->setFlash('notice', 'Members have been successfully updated.');
        $this->redirect('@user_profile\members');
      }
    }
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeDesign(sfWebRequest $request){
    $this->forward404Unless($this->getUser()->isSuperAdmin() || in_array($this->getUser()->getGuardUser()->account_type, array('Trial', 'Enterprise')));

    $this->form = new UserDesignForm($this->getUser()->getGuardUser());

    if ($request->getMethod() == sfRequest::POST) {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $this->form->save();
        $this->getUser()->setFlash('notice', 'Design has been successfully updated.');
        $this->redirect('@user_profile\design');
      }
    }
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeLabels(sfWebRequest $request)
  {

  }

  public function executeEditLabel(sfWebRequest $request)
  {
    $pk = explode('_', $request->getParameter('pk'));

    if (in_array($pk[0], array(1, 2, 3, 4)) && in_array($pk[1], array('singular', 'plural')) && $request->hasParameter('value')) {
      $exist = InterfaceLabelTable::getInstance()->createQuery('il')
        ->where('il.type = ?', $pk[0])
        ->andWhere('il.user_id = ?', $this->getUser()->getGuardUser()->getId())
        ->count();

      if ($exist) {
        InterfaceLabelTable::getInstance()->createQuery('il')->update()
          ->set($pk[1], '?', $request->getParameter('value'))
          ->where('il.type = ?', $pk[0])
          ->andWhere('il.user_id = ?', $this->getUser()->getGuardUser()->getId())
          ->execute();
      }else{
        $interface_label = new InterfaceLabel();
        $interface_label->type = $pk[0];
        $interface_label->user_id = $this->getUser()->getGuardUser()->getId();
        $interface_label->{$pk[1]} = $request->getParameter('value');
        $interface_label->save();
      }
    }

    return sfView::NONE;
  }
}