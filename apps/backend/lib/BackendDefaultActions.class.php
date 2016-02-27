<?php

/**
 * criterion actions.
 *
 * @package    dmp
 * @subpackage criterion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BackendDefaultActions extends sfActions
{
  protected $model = null;

  /**
   * @throws sfStopException
   */
  public function preExecute()
  {
    if (!$this->model) {
      throw new sfStopException('The model is not set.');
    }

    if (!class_exists($this->model)) {
      throw new sfStopException('The model does not exist.');
    }

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));
  }

  public function executeIndex(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($this->decision));

    $tableClass = $this->model . 'Table';
    $this->collection_json = $tableClass::getInstance()
      ->getForUserJSON($this->getUser()->getGuardUser(), $decision_id);
    $this->upload_widget = new laWidgetFileUpload(array(
      'module_partial' => 'decision/upload_widget'
    ));
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $object = new $this->model();
    $object->name = $request->getParameter('title', '');
    $object->decision_id = $request->getParameter('decision_id');
    $object->save();

    $formClass = $this->model . 'Form';
    $form = new $formClass($object, array('user' => $this->getUser()));
    return $this->renderPartial('form', array('form' => $form));
  }

  public function executeBulkDelete(sfWebRequest $request)
  {
    $ids = array_merge(array(0), $request->getParameter('ids'));

    $objects = Doctrine_Query::create()
      ->from($this->model . ' m')
      ->leftJoin('m.Decision d')
      ->whereIn('m.id', $ids)
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->execute();

    foreach ($objects as $object) {
      $object->delete();
    }

    return sfView::NONE;
  }

  public function executeBulkMove(sfWebRequest $request)
  {
    $ids = array_merge(array(0), $request->getParameter('ids'));
    $decision_id = $request->getParameter('decision_id');

    $objects = Doctrine_Query::create()
      ->from($this->model . ' m')
      ->leftJoin('m.Decision d')
      ->whereIn('m.id', $ids)
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->execute();

    foreach ($objects as $object) {
      $object->decision_id = $decision_id;
      $object->save();
    }

    return sfView::NONE;
  }

  public function executeBulkCopy(sfWebRequest $request)
  {
    $ids = array_merge(array(0), $request->getParameter('ids'));
    $decision_id = $request->getParameter('decision_id');

    $objects = Doctrine_Query::create()
      ->from($this->model . ' m')
      ->leftJoin('m.Decision d')
      ->whereIn('m.id', $ids)
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->execute();

    foreach ($objects as $object) {
      $item = Doctrine_Core::getTable($this->model)->find($object->id);
      /** @var Alternative $copy */
      $copy = $item->copy();
      $copy->decision_id = $decision_id;

      $date_now = new DateTime('now');
      $copy->created_at = $copy->updated_at = $date_now->format('Y-m-d H:i:s');
      $copy->setCreatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
      $copy->setUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));

      $copy->save();
    }

    return sfView::NONE;
  }

  public function executeFetch(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($object = Doctrine_Core::getTable($this->model)->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    $result = $object->getRowData();
    if ($this->model == 'Alternative') {
      $tags = array();
      foreach ($object->getTagAlternative() as $tag) {
        $tags[] = $tag->Tag->name;
      }
      $result['tags'] = implode(', ', $tags);
    }

    return $this->renderText(json_encode($result));
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($object = Doctrine_Core::getTable($this->model)->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $formClass = $this->model . 'Form';
    $form = new $formClass($object, array('user' => $this->getUser()));

    return $this->renderPartial('form', array('form' => $form));
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($object = Doctrine_Core::getTable($this->model)->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $formClass = $this->model . 'Form';
    $this->form = new $formClass($object, array('user' => $this->getUser()));

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid()) {
      $this->form->save();
      $this->form_valid = true;

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($object, $this->getUser()->getGuardUser(), array('action' => 'edit'));

      return sfView::NONE;
    }

    return $this->renderPartial('form', array('form' => $this->form));
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($object = Doctrine_Core::getTable($this->model)->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $object->delete();
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode(array('status' => 1)));
  }

  public function executeImport(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    /** @var Decision $decision */
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);

    $fileValidator = new sfValidatorFile(array(
      'required'   => true
    ));

    $importerClass = $this->model . 'Importer';
    $importer = new $importerClass();
    $importer->setDecision($decision);
    if ($this->model == 'Alternative'){
      $importer->setCreatedAndUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
    }

    foreach ($request->getFiles('files') as $file) {
      $validatedFile = $fileValidator->clean($file);
      $importer->setFile($validatedFile);
      $importer->import();
    }

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode(array(array())));
  }
}
