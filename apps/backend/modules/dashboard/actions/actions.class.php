<?php

/**
 * dashboard actions.
 *
 * @package    dmp
 * @subpackage dashboard
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class dashboardActions extends sfActions
{
  /**
   *
   */
  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id'))
    {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($this->decision));

    $this->popularCriteria = Doctrine::getTable('PopularCriterion')->findAll();
    $this->dashboard = new Dashboard();
    $this->dashboard->load($this->getUser()->getGuardUser(), $request->getParameter('decision_id'));

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    if ($this->decision->save_graph_weight) {
      $criteriaAnalyze->loadData();
    } else {
      $criteriaAnalyze->load();
    }

    $this->stackedBarChart = new StackedBarChart();
    $this->stackedBarChart->setDecisionId($decision_id);
    $this->stackedBarChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
    $this->stackedBarChart->load();

    $costAnalyze = new CostAnalyze();
    $costAnalyze->setDecisionId($decision_id);
    $costAnalyze->setSortedAlternativeIds($this->stackedBarChart->getSortedAlternativeIds());
    $costAnalyze->setCumulativeData($this->stackedBarChart->getCumulativeData());
    $costAnalyze->load();

    $this->cumulativeChart = new CumulativeGainChart();
    $this->cumulativeChart->setDecisionId($decision_id);
    $this->cumulativeChart->setSortedAlternativeIds($this->stackedBarChart->getSortedAlternativeIds());
    $this->cumulativeChart->setMeasurement($this->stackedBarChart->getCumulativeData());
    $this->cumulativeChart->setCostData($costAnalyze->getData());
    $this->cumulativeChart->setAlternativeNames($costAnalyze->getAlternativeNames());
    $this->cumulativeChart->setCriterionNames($costAnalyze->getCriteria());
    $this->cumulativeChart->load();

    $this->alternatives_json = AlternativeTable::getInstance()->getDashboardReleaseJSON($decision_id, $this->stackedBarChart->getSortedAlternativeIds());
    $this->releases = ProjectReleaseTable::getInstance()->getList($decision_id);

    $this->upload_widget = new laWidgetFileUpload(array(
      'module_partial' => 'decision/upload_widget'
    ));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeSave(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $measurement = $request->getParameter('measurement');
    $decision_id = $request->getParameter('decision_id');
    $user = $this->getUser()->getGuardUser();

    $keys = array_keys($measurement);
    $alternative_id = array_shift($keys);
    $keys = array_keys($measurement[$alternative_id]);
    $criterion_id = array_shift($keys);
    $value = $measurement[$alternative_id][$criterion_id];

    $alternativeMeasurement = AlternativeMeasurementTable::getInstance()
      ->getOneForDashboard($user, $decision_id, $alternative_id, $criterion_id);

    if (is_object($alternativeMeasurement)) {
      $alternativeMeasurement->score = $value;
      $alternativeMeasurement->save();
    } else {
      $response = ResponseTable::getInstance()->getOneForDashboard($user, $decision_id);
      if (is_object($response)) {
        $alternativeMeasurement = new AlternativeMeasurement();
        $alternativeMeasurement->score = $value;
        $alternativeMeasurement->alternative_head_id = $alternative_id;
        $alternativeMeasurement->criterion_id = $criterion_id;
        $alternativeMeasurement->Response = $response;
        $alternativeMeasurement->save();
      }
    }

    Doctrine_Query::create()->delete()->from('Graph')->where('decision_id = ?', $decision_id)->execute();

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->load();

    $stackedBarChart = new StackedBarChart();
    $stackedBarChart->setDecisionId($decision_id);
    $stackedBarChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
    $stackedBarChart->load();

    $costAnalyze = new CostAnalyze();
    $costAnalyze->setDecisionId($decision_id);
    $costAnalyze->setSortedAlternativeIds($stackedBarChart->getSortedAlternativeIds());
    $costAnalyze->setCumulativeData($stackedBarChart->getCumulativeData());
    $costAnalyze->load();

    $cumulativeChart = new CumulativeGainChart();
    $cumulativeChart->setDecisionId($decision_id);
    $cumulativeChart->setSortedAlternativeIds($stackedBarChart->getSortedAlternativeIds());
    $cumulativeChart->setMeasurement($stackedBarChart->getCumulativeData());
    $cumulativeChart->setCostData($costAnalyze->getData());
    $cumulativeChart->setAlternativeNames($costAnalyze->getAlternativeNames());
    $cumulativeChart->setCriterionNames($costAnalyze->getCriteria());
    $cumulativeChart->load();

    $stacked_bar_chart[] = '"data":' . $stackedBarChart->getJsonData();
    $stacked_bar_chart[] = '"alternatives":' . $stackedBarChart->getAlternativesJson();
    $stacked_bar_chart[] = '"criteria":' . $stackedBarChart->getCriteriaJson();

    $cumulative_chart[] = '"costData":' . $cumulativeChart->getJsonCostData();
    $cumulative_chart[] = '"benefitData":' . $cumulativeChart->getJsonData();
    $cumulative_chart[] = '"criteria":' . json_encode($costAnalyze->getCriteria());
    $cumulative_chart[] = '"alternatives":' . $cumulativeChart->getAlternativesJson();

    $response = array();
    $response[] = '"cumulativeChart":{' . implode(',', $cumulative_chart) .'}';
    $response[] = '"stackedBarChart":{' . implode(',', $stacked_bar_chart) .'}';

    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText('{' . implode(',', $response) .'}');
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
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
    $criterion->save();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   * @throws Doctrine_Collection_Exception
   * @throws sfError404Exception
   */
  public function executeImportFromTrello(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($decision));

    $decision->setExternalId($request->getParameter('board_id'));

    $decision->save();

    $dashboard_role = $decision->getDashboardRole();

    foreach (json_decode($request->getParameter('cards', '{}')) as $card) {
      $alternative = new Alternative();
      $alternative->setName($card->name);
      $alternative->setDecisionId($decision->getId());
      $alternative->setAdditionalInfo($card->desc);
      $alternative->setNotes($card->notes);
      $alternative->setExternalId($card->id);
      $alternative->setCreatedBy($request->getParameter('full_name') . ' (via Trello)');
      $alternative->setUpdatedBy($request->getParameter('full_name') . ' (via Trello)');

      if ($card->due) {
        $date = DateTime::createFromFormat('Y-m-d\TH:i:s.000Z', $card->due);
        $alternative->setDueDate($date->format('Y-m-d H:i:s'));
        $alternative->setNotifyDate($date->format('Y-m-d H:i:s'));
      }

      $alternative->save();

      foreach ($card->labels as $label) {
        Tag::newTag($this->getUser()->getGuardUser(), $alternative->getId(), $label, 'alternative');
      }

      if ($dashboard_role){
        foreach ($decision->getCriterion() as $criterion){
          $planned_alternative_measurement = new PlannedAlternativeMeasurement();
          $planned_alternative_measurement->setAlternative($alternative);
          $planned_alternative_measurement->setCriterion($criterion);
          $dashboard_role->PlannedAlternativeMeasurement->add($planned_alternative_measurement);
        }
      }
    }

    $dashboard_role->PlannedAlternativeMeasurement->save();

    return $this->renderText(json_encode(array(
      'status' => 'success'
    )));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   * @throws Doctrine_Collection_Exception
   * @throws sfError404Exception
   */
  public function executeImportFromExcel(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($decision));

    $fileValidator = new sfValidatorFile(array(
      'required'        => true,
      'mime_types' => array(
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/msexcel',
        'application/x-msexcel',
        'application/x-ms-excel',
        'application/x-excel',
        'application/x-dos_ms_excel',
        'application/xls',
        'application/x-xls',
        'application/vnd.ms-office',
        'application/zip',
      )
    ));

    try {
      $validatedFile = $fileValidator->clean($request->getFiles('file'));
    }catch (Exception $e) {
      return $this->renderText(json_encode(array(
        'status'  => 'error',
        'message' => $e->getMessage()
      )));
    }

    $importer = new AlternativeImporter();
    $importer->setDecision($decision);
    $importer->setFile($validatedFile);

    return $this->renderText(json_encode(array(
      'status'  => 'success',
      'html'    => $this->getComponent('alternative', 'importCustomFields', array('data' => $importer->prepareData(), 'decision_id' => $decision_id))
    )));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeImportFromCustomFields(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($decision));

    $header = $request->getParameter('header');
    $data = $request->getParameter('data');

    if (array_search('name', $header) === false) {
      return $this->renderText(json_encode(array(
        'status'  => 'error',
        'text'    => 'Please ensure that data field "Name" is mapped to a column in your SpreadSheet.'
      )));
    }

    foreach ($data as $item) {
      $alternative = null;
      if (array_key_exists('id', $item) && !empty($item['id'])) {
        $alternative = AlternativeTable::getInstance()->createQuery('a')
          ->leftJoin('a.Decision d')
          ->leftJoin('d.User u')
          ->leftJoin('u.TeamMember tm')
          ->whereIn('d.user_id', sfGuardUserTable::getInstance()->getUsersInTeamIDs($this->getUser()->getGuardUser()))
          ->andWhere('a.item_id = ?', $item['id'])
          ->andWhere('d.id = ?', $decision->getId())
          ->fetchOne();
      }
      if (!is_object($alternative)) {
        $alternative = new Alternative();
        $alternative->setDecision($decision);
        $alternative->setCreatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
        $alternative->setUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
      }

      $custom_fields = array();

      foreach ($item as $prop => $value) {
        if (array_key_exists($prop, $header)) {
          if ($header[$prop] == '_new') {
            $custom_fields[$prop] = $value;
          } elseif (
            !in_array($header[$prop], array('id')) &&
            in_array($header[$prop], array('name', 'status', 'work progress', 'additional info', 'notes', 'due date', 'notify date', 'tags'))
          ) {
            if ($header[$prop] == 'tags') {
              // Process tags
              $tags_request = array_map('trim', explode(',', $value));
              $tags         = array();
              foreach ($alternative->getTagAlternative() as $tag) {
                $tags[] = $tag->Tag->name;
              }

              foreach (array_diff($tags_request, $tags) as $result) {
                Tag::newTag($this->getUser()->getGuardUser(), $alternative->getId(), $result, 'alternative');
              }

              foreach (array_diff($tags, $tags_request) as $result) {
                Tag::removeTag($this->getUser()->getGuardUser(), $alternative->getId(), $result, 'alternative');
              }
            } else {
              $alternative->{str_replace(' ', '_', $header[$prop])} = $value;
            }
          }
        }
      }

      if ($custom_fields) {
        $alternative->setCustomFields(json_encode($custom_fields));
      }

      if (!$alternative->getName()) {
        $alternative->setName('New ' . InterfaceLabelTable::getInstance()->get($this->getUser()->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) );
      }

      $alternative->save();
    }

    return $this->renderText(json_encode(array(
      'status' => 'success'
    )));
  }
}
