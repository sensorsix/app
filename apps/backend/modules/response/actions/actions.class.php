<?php

/**
 * response actions.
 *
 * @package    dmp
 * @subpackage response
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class responseActions extends sfActions
{
  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id'))
    {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }
  }

 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($this->decision);

    $this->table = new ResponseTableView();
    $this->table->load($this->decision->id);
  }

  public function executeExport(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($decision);

    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename="' . $decision->name . '.xlsx"');

    $table = new ResponseTableView();
    $table->load($decision_id);

    $excelExporter = new ResponseExcelExporter($table);
    $excelExporter->export();

    exit;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Response $response  */
    $response = Doctrine::getTable('Response')->find($request->getParameter('id'));
    $this->forward404Unless($response);

    // Update charts in "Analyze" tab
    Doctrine_Query::create()
      ->delete()
      ->from('Graph')
      ->where('decision_id = ?', $response->decision_id)
      ->execute();

    $response->delete();

    $this->setTemplate(false);
    return sfView::NONE;
  }
}
