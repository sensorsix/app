<?php

/**
 * criterion actions.
 *
 * @package    dmp
 * @subpackage criterion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class criterionActions extends BackendDefaultActions
{
  protected $model = 'Criterion';

  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id'))
    {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));
  }

  public function executeIndex(sfWebRequest $request)
  {
    parent::executeIndex($request);

    $this->popularCriteria = Doctrine::getTable('PopularCriterion')->findAll();
  }

  public function executeCreatePopular(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var PopularCriterion $popularCriterion */
    $popularCriterion = $this->getRoute()->getObject();

    $criterion = new Criterion();
    $criterion->name = $popularCriterion->name;
    $criterion->decision_id = $request->getParameter('decision_id');
    $criterion->description = $popularCriterion->description;
    $criterion->variable_type = $popularCriterion->variable_type;
    $criterion->measurement = $popularCriterion->measurement;

    $form = new CriterionForm($criterion);
    return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $criterion = new Criterion();
    $criterion->name = $request->getParameter('title', '');
    $criterion->decision_id = $request->getParameter('decision_id');

    $form = new CriterionForm($criterion);
    return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));

    $criterion = new Criterion();
    $criterion->decision_id = $request->getParameter('decision_id');

    $form = new CriterionForm($criterion);
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));

    if ($form->isValid()){
      $form->save();

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($criterion, $this->getUser()->getGuardUser(), array('action' => 'new'));

      return $this->renderText(json_encode($criterion->getRowData()));
    }else{
      return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
    }
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $output = parent::executeUpdate($request);
    if ($this->form_valid){
      return $this->renderText(json_encode($this->form->getObject()->getRowData()));
    }else{
      return $output;
    }
  }
}
