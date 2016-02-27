<?php

/**
 * analyze actions.
 *
 * @package    dmp
 * @subpackage analyze
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class analyzeActions extends sfActions
{
  /**
   *
   */
  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id')) {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($this->decision);

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));

    $this->criteriaAnalyze = new CriteriaAnalyze();
    $this->criteriaAnalyze->setDecisionId($decision_id);

    if ($this->decision->save_graph_weight) {
      $this->criteriaAnalyze->loadData();
    } else {
      $this->criteriaAnalyze->load();
    }

    $this->logicalFilter = new LogicalFilterView();
    $this->logicalFilter->setDecisionId($decision_id);
    $this->logicalFilter->load();

    $this->roleFilter = new RoleFilterView();
    $this->roleFilter->setDecisionId($decision_id);
    $this->roleFilter->load();

    $this->statusFilter = new StatusFilterView();
    $this->statusFilter->setDecisionId($decision_id);
    $this->statusFilter->load();

    $this->tagFilter = new TagFilterView();
    $this->tagFilter->setDecisionId($decision_id);
    $this->tagFilter->load();

    $this->stackedBarChart = new StackedBarChart();
    $this->stackedBarChart->setDecisionId($decision_id);
    $this->stackedBarChart->setCriteriaValues($this->criteriaAnalyze->getCriteriaValues());
    $this->stackedBarChart->setRoleFilterData($this->roleFilter->getData());
    $this->stackedBarChart->setStatusFilterData($this->statusFilter->getData());
    $this->stackedBarChart->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->stackedBarChart->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->stackedBarChart->load();

    $this->radarChart = new RadarChart();
    $this->radarChart->setData($this->stackedBarChart->getData());
    $this->radarChart->setAlternativeNames($this->stackedBarChart->getAlternativesNames());
    $this->radarChart->setCriteriaNames($this->stackedBarChart->getCriteriaNames());
    $this->radarChart->setAlternativesLabel($this->decision->getAlternativePluralAlias());

    $this->pointChart = new PointChart();
    $this->pointChart->setDecisionId($decision_id);
    $this->pointChart->setRoleFilterData($this->roleFilter->getData());
    $this->pointChart->setStatusFilterData($this->statusFilter->getData());
    $this->pointChart->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->pointChart->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->pointChart->load();
    $this->pointChart->setTotalBenefit($this->stackedBarChart->getTotalBenefit());

    $this->costAnalyze = new CostAnalyze();
    $this->costAnalyze->setDecisionId($decision_id);
    $this->costAnalyze->setSortedAlternativeIds($this->stackedBarChart->getSortedAlternativeIds());
    $this->costAnalyze->setRoleFilterData($this->roleFilter->getData());
    $this->costAnalyze->setStatusFilterData($this->statusFilter->getData());
    $this->costAnalyze->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->costAnalyze->setCumulativeData($this->stackedBarChart->getCumulativeData());
    $this->costAnalyze->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->costAnalyze->load();

    $this->cumulativeChart = new CumulativeGainChart();
    $this->cumulativeChart->setDecisionId($decision_id);
    $this->cumulativeChart->setSortedAlternativeIds($this->stackedBarChart->getSortedAlternativeIds());
    $this->cumulativeChart->setMeasurement($this->stackedBarChart->getCumulativeData());
    $this->cumulativeChart->setCostData($this->costAnalyze->getData());
    $this->cumulativeChart->setAlternativeNames($this->costAnalyze->getAlternativeNames());
    $this->cumulativeChart->setCriterionNames($this->costAnalyze->getCriteria());
    $this->cumulativeChart->load();

    $this->bubbleChart = new BubbleChart();
    $this->bubbleChart->setDecisionId($decision_id);
    $this->bubbleChart->setRoleFilterData($this->roleFilter->getData());
    $this->bubbleChart->setStatusFilterData($this->statusFilter->getData());
    $this->bubbleChart->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->bubbleChart->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->bubbleChart->setTotalBenefit($this->stackedBarChart->getTotalBenefit());
    $this->bubbleChart->load();

    $this->releases = ProjectReleaseTable::getInstance()->getList($decision_id);

    $this->comments = CommentTable::getInstance()->getList($decision_id);
  }

  /**
   * @param $criteriaAnalyze
   * @param $decision_id
   * @param bool $revert
   * @param array $response
   * @return sfView
   */
  private function getUpdateActionResponse($criteriaAnalyze, $decision_id, $revert = false, $response = array())
  {
    $logicalFilter = new LogicalFilterView();
    $logicalFilter->setDecisionId($decision_id);
    $logicalFilter->load();

    $roleFilter = new RoleFilterView();
    $roleFilter->setDecisionId($decision_id);
    $roleFilter->load();

    $statusFilter = new StatusFilterView();
    $statusFilter->setDecisionId($decision_id);
    $statusFilter->load();

    $tagFilter = new TagFilterView();
    $tagFilter->setDecisionId($decision_id);
    $tagFilter->load();

    // When user changes the weights of criteria all dependent values
    // should be recalculated automatically in the alternatives chart
    $stackedBarChart = new StackedBarChart();
    $stackedBarChart->setDecisionId($decision_id);
    $stackedBarChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
    $stackedBarChart->setRoleFilterData($roleFilter->getData());
    $stackedBarChart->setStatusFilterData($statusFilter->getData());
    $stackedBarChart->setTagFilterData($tagFilter->getDataForSQL());
    $stackedBarChart->setFilteredAlternativesIds($logicalFilter->getFilteredAlternativesIds());
    $stackedBarChart->load();

    $radarChart = new RadarChart();
    $radarChart->setData($stackedBarChart->getData());
    $radarChart->setAlternativeNames($stackedBarChart->getAlternativesNames());
    $radarChart->setCriteriaNames($stackedBarChart->getCriteriaNames());
    $radarChart->prepareData();

    $pointChart = new PointChart();
    $pointChart->setDecisionId($decision_id);
    $pointChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
    $pointChart->setRoleFilterData($roleFilter->getData());
    $pointChart->setStatusFilterData($statusFilter->getData());
    $pointChart->setTagFilterData($tagFilter->getDataForSQL());
    $pointChart->setFilteredAlternativesIds($logicalFilter->getFilteredAlternativesIds());
    $pointChart->load();
    $pointChart->setTotalBenefit($stackedBarChart->getTotalBenefit());

    $bubbleChart = new BubbleChart();
    $bubbleChart->setDecisionId($decision_id);
    $bubbleChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
    $bubbleChart->setRoleFilterData($roleFilter->getData());
    $bubbleChart->setStatusFilterData($statusFilter->getData());
    $bubbleChart->setTagFilterData($tagFilter->getDataForSQL());
    $bubbleChart->setFilteredAlternativesIds($logicalFilter->getFilteredAlternativesIds());
    $bubbleChart->setTotalBenefit($stackedBarChart->getTotalBenefit());
    $bubbleChart->load();

    $costAnalyze = new CostAnalyze();
    $costAnalyze->setDecisionId($decision_id);
    $costAnalyze->setSortedAlternativeIds($stackedBarChart->getSortedAlternativeIds());
    $costAnalyze->setFilteredAlternativesIds($logicalFilter->getFilteredAlternativesIds());
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

    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    $stacked_bar_chart[] = '"data":' . $stackedBarChart->getJsonData();
    $stacked_bar_chart[] = '"alternatives":' . $stackedBarChart->getAlternativesJson();
    $stacked_bar_chart[] = '"criteria":' . $stackedBarChart->getCriteriaJson();

    if ($revert)
    {
      $response[] = '"criteriaData":' . $criteriaAnalyze->getJsonData();
    }
    $point_chart[] = '"data":' . $pointChart->getJsonData();
    $point_chart[] = '"criteria":' . json_encode($pointChart->getCriteriaNames());
    $bubble_chart[] = '"data":' . $bubbleChart->getJsonData();
    $bubble_chart[] = '"criteria":' . json_encode($pointChart->getCriteriaNames());

    $response[] = '"stackedBarChart":{' . implode(',', $stacked_bar_chart) .'}';
    $response[] = '"pointChart":{' . implode(',', $point_chart) . '}';
    $response[] = '"bubbleChart":{' . implode(',', $bubble_chart) . '}';

    $cumulative_chart[] = '"costData":' . $cumulativeChart->getJsonCostData();
    $cumulative_chart[] = '"benefitData":' . $cumulativeChart->getJsonData();
    $cumulative_chart[] = '"criteria":' . json_encode($costAnalyze->getCriteria());
    $cumulative_chart[] = '"alternatives":' . $cumulativeChart->getAlternativesJson();

    $response[] = '"cumulativeChart":{' . implode(',', $cumulative_chart) .'}';

    $cost_analyze[] = '"alternatives":' . $costAnalyze->getAlternativesJson();
    $cost_analyze[] = '"order":' . $costAnalyze->getAlternativeOrderJson();
    $cost_analyze[] = '"data":' . $costAnalyze->getJsonData();
    $cost_analyze[] = '"bScoreData":' . $costAnalyze->getCumulativeJsonData();

    $response[] = '"radarChart":' . $radarChart->getJsonData();
    $response[] = '"costOrder":{' . implode(',', $cost_analyze) . '}';

    return $this->renderText('{' . implode(',', $response) .'}');
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->setData($request->getParameter('graph', array()));

    // save data to database
    if ($request->getParameter('save', false)) {
      $criteriaAnalyze->saveData();
    }

    // load data from database
    if ($request->getParameter('editable', false)) {
      $criteriaAnalyze = new CriteriaAnalyze();
      $criteriaAnalyze->setDecisionId($decision_id);
      $criteriaAnalyze->loadData();

      return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id, true);
    }

    return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id);
  }

  /**
   * Save on the database the state of checkbox "Save criteria weights"
   * No save other changes
   *
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeSaveCriteriaWeightState(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($decision instanceof Decision);

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));

    $decision->save_graph_weight = $request->getParameter('state', false);
    $decision->save();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeRevert(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->load();

    return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id, true);
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeNewLogicalFilter(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(Doctrine::getTable('Decision')->find($decision_id));

    $logicalFilter = new LogicalFilter();
    $logicalFilter->decision_id = $decision_id;

    $form = new LogicalFilterForm($logicalFilter);
    $form->bind($request->getParameter('logical_filter'));
    if ($form->isValid())
    {
      $logicalFilter = $form->save();

      $criteriaAnalyze = new CriteriaAnalyze();
      $criteriaAnalyze->setDecisionId($decision_id);
      $criteriaAnalyze->load();
      $response = array();
      $response[] =  '"logicalFilter":"' . addslashes(trim(preg_replace('/\s+/', ' ', $this->getPartial('logical_filter_item', array('item' => $logicalFilter))))) . '"';

      return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id, false, $response);
    }
    else
    {
      return $this->renderPartial('logical_filter_form', array('form' => $form));
    }
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeDeleteLogicalFilter(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));

    Doctrine_Query::create()->delete('LogicalFilter')->where('id = ?', $request->getParameter('id'))->execute();
    Doctrine_Query::create()->delete('Graph')->where('decision_id = ?', $decision_id)->execute();

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->load();

    return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id);
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeUpdateRoleFilter(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));

    if ($request->getParameter('filter_action') == 'add')
    {
      $roleFilter = new RoleFilter();
      $roleFilter->decision_id = $decision_id;
      $roleFilter->role_id = $request->getParameter('role_id');
      $roleFilter->save();
    }
    else if ($request->getParameter('filter_action') == 'delete')
    {
      Doctrine_Query::create()->delete('RoleFilter')
        ->where('role_id = ? AND decision_id = ?', array($request->getParameter('role_id'), $decision_id))->execute();
      Doctrine_Query::create()->delete('Graph')->where('decision_id = ?', $decision_id)->execute();
    }

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->load();

    return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id);
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeUpdateStatusFilter(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));

    if ($request->getParameter('filter_action') == 'add')
    {
      $roleFilter = new StatusFilter();
      $roleFilter->decision_id = $decision_id;
      $roleFilter->status = $request->getParameter('status');
      $roleFilter->save();
      Doctrine_Query::create()->delete('Graph')->where('decision_id = ?', $decision_id)->execute();
    }
    else if ($request->getParameter('filter_action') == 'delete')
    {
      Doctrine_Query::create()->delete('StatusFilter')
        ->where('status = ? AND decision_id = ?', array($request->getParameter('status'), $decision_id))->execute();
    }

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->load();

    return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id);
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeUpdateTagFilter(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));

    if ($request->getParameter('filter_action') == 'add')
    {
      $roleFilter = new TagFilter();
      $roleFilter->decision_id = $decision_id;
      $roleFilter->tag_id = $request->getParameter('tag_id');
      $roleFilter->save();
    }
    else if ($request->getParameter('filter_action') == 'delete')
    {

      $tf_by_name = Doctrine_Query::create()->from('TagFilter tf')
        ->select('tf.id')
        ->leftJoin('tf.Tag t')
        ->where("t.name = ?", $request->getParameter('tag_name'))
        ->andWhere('tf.decision_id = ?', $decision_id)
        ->execute();

      foreach($tf_by_name as $v){
        Doctrine_Query::create()->delete('TagFilter')
          ->where('id = ?', $v->id)->execute();
      }
    }

    Doctrine_Query::create()->delete('Graph')->where('decision_id = ?', $decision_id)->execute();

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->load();

    return $this->getUpdateActionResponse($criteriaAnalyze, $decision_id);
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeExcelExport(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);

    /** @var Decision $decision  */
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($decision);

    $objPHPExcel = new sfPhpExcel();

    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename="' . $decision->name . '.xlsx"');

    // Set properties
    $objPHPExcel->getProperties()->setCreator("SensorSix");
    $objPHPExcel->getProperties()->setLastModifiedBy("SensorSix");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    /** @var ProjectRelease[] $releases  */
    $releases = Doctrine_Query::create()
      ->from('ProjectRelease r')
      ->leftJoin('r.ProjectReleaseAlternative ra')
      ->leftJoin('ra.Alternative a')
      ->where('r.decision_id = ? AND r.criterion_id = ?', array($decision_id, $request->getParameter('criterion_id')))
      ->execute();

    $index = 1;
    $sheet = $objPHPExcel->getActiveSheet();
    foreach ($releases as $release)
    {
      $sheet->setCellValue('A' . $index++, $release->name);
      foreach ($release->ProjectReleaseAlternative as $releaseAlternative)
      {
        $sheet->setCellValue('B' . $index, $releaseAlternative->Alternative->name);
        $sheet->setCellValueExplicit('C' . $index, $releaseAlternative->value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $index++;
      }
      $sheet->setCellValue('B' . ++$index, 'Total:');
      $sheet->setCellValueExplicit('C' . $index, $release->value, PHPExcel_Cell_DataType::TYPE_NUMERIC);
      $index += 2;
    }

    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Releases');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel 2007 file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

    exit;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executePinToWall(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));

    $type = $request->getParameter('type');

    $wall = Doctrine::getTable('Wall')->findOneBy('decision_id', $decision_id);

    $wallPost = new WallPost();
    $wallPost->wall_id = $wall->id;
    $wallPost->save();

    $htmlToImage = new HtmlToImageService();
    $route = array_merge(array('sf_route' => 'analyze\generateImage', 'type' => $type));
    $htmlToImage->setUrl($this->getController()->genUrl($route, true));
    $htmlToImage->setData($request->getPostParameters());
    $htmlToImage->setImagePath($wallPost->getFile());
    $htmlToImage->setOptions(array('ignore-ssl-errors' => 'true'));
    $htmlToImage->run();

    $this->getContext()->getConfiguration()->loadHelpers(array('Asset', 'Tag'));
    $wallPost->content = image_tag($wallPost->getRelativeFilePath());
    $wallPost->save();

    // Create log
    $log = new Log();
    $log->injectDataAndPersist($wall, $this->getUser()->getGuardUser(), array('type' => $type, 'action' => 'edit'));

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeActivePinToWall(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(is_object(DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id)));
    $type = $request->getParameter('type');
    $this->forward404Unless(in_array($type, array('criteria', 'alternatives', 'cost', 'xy', 'bubble', 'partition', 'cumulative', 'radar')));

    $wall = Doctrine::getTable('Wall')->findOneBy('decision_id', $decision_id);

    $wallPost = new WallPost();
    $wallPost->wall_id = $wall->id;
    $wallPost->type = $type;
    $wallPost->params = json_encode($request->getParameter('params', array()));
    $wallPost->save();

    // Create log
    $log = new Log();
    $log->injectDataAndPersist($wall, $this->getUser()->getGuardUser(), array('type' => $type, 'action' => 'edit'));

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeGenerateImage(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->forward404Unless(Doctrine::getTable('Decision')->find($decision_id));

    $type = $request->getParameter('type');

    if ($type == 'xy')
    {
      $this->chart = new PointChart();
      $this->chart->setPointsXY($request->getParameter('data'));
      $this->chart->setXLabelById($request->getParameter('x'));
      $this->chart->setYLabelById($request->getParameter('y'));
    }
    else if ($type == 'bubble')
    {
      $this->chart = new BubbleChart();
      $this->chart->setPointsXY($request->getParameter('data'));
      $this->chart->setXLabelById($request->getParameter('x'));
      $this->chart->setYLabelById($request->getParameter('y'));
      $this->chart->setZLabelById($request->getParameter('z'));
    }
    else if ($type == 'cumulative')
    {
      $this->chart = new CumulativeGainChart();
      $this->chart->setPointsXY($request->getParameter('data'));
      $this->chart->setXLabelById($request->getParameter('x'));
    }
    else if ($type == 'cost')
    {
      $costAnalyze = new CostAnalyze();
      $costAnalyze->setCriterionId($request->getParameter('criterion_id'));
      $costAnalyze->setBScore($request->getParameter('b_score'));
      $costAnalyze->setPool($request->getParameter('pool'));
      $costAnalyze->setUnallocated($request->getParameter('unallocated'));
      $costAnalyze->setRedLine($request->getParameter('red_line', array()));
      $costAnalyze->setOrder($request->getParameter('order'));
      $this->analyze = $costAnalyze;
    }
    else if ($type == 'partition')
    {
      $this->releases = Doctrine_Query::create()
        ->from('ProjectRelease r')
        ->leftJoin('r.ProjectReleaseAlternative ra')
        ->leftJoin('ra.Alternative a')
        ->where('r.decision_id = ? AND r.criterion_id = ?', array($decision_id, $request->getParameter('criterion_id')))
        ->execute();
    }
    else
    {
      $criteriaAnalyze = new CriteriaAnalyze();
      $criteriaAnalyze->setDecisionId($decision_id);
      $criteriaAnalyze->setCriteriaValues($request->getParameter('graph', array()));

      if ($type == 'criteria')
      {
        $this->analyze = $criteriaAnalyze;
      }
      else
      {
        $stackedBarChart = new StackedBarChart();
        $stackedBarChart->setDecisionId($decision_id);
        $stackedBarChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
        $stackedBarChart->load();

        if ($type == 'radar')
        {
          $radarChart = new RadarChart();
          $radarChart->setData($stackedBarChart->getData());
          $radarChart->setAlternativeNames($stackedBarChart->getAlternativesNames());
          $radarChart->setCriteriaNames($stackedBarChart->getCriteriaNames());
          $radarChart->setFilterData($request->getParameter('filter'));
          $radarChart->prepareData();
          $this->analyze = $radarChart;
        }
        else
        {
          $this->analyze = $stackedBarChart;
        }
      }
    }

    $this->setTemplate($type . 'Image');
    $this->setLayout('html_to_image');
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeNewRelease(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    /** @var Decision $decision  */
    $decision = DecisionTable::getInstance()->find($request->getParameter('decision_id'));
    $this->forward404Unless($decision);

    $criterion_id = $request->getParameter('criterion_id');
    $release_number = Doctrine_Query::create()
        ->from('ProjectRelease')
        ->where('decision_id = ? AND criterion_id = ?', array($decision->id, $criterion_id))
        ->count() + 1;

    $this->release = new ProjectRelease();
    $this->release->decision_id = $decision->id;
    $this->release->criterion_id = $criterion_id;
    $this->release->name = $decision->getPartitionerAlias() . ' ' . $release_number;
    $this->release->save();

    // Create log
    $log = new Log();
    $log->injectDataAndPersist($this->release, $this->getUser()->getGuardUser(), array('action' => 'new'));
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeRemoveReleaseItem(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $release_id = $request->getParameter('release_id');

    /** @var ProjectRelease $projectRelease */
    $projectRelease = ProjectReleaseTable::getInstance()->find($release_id);
    /** @var ProjectReleaseAlternative $projectReleaseAlternative */
    $projectReleaseAlternative = ProjectReleaseAlternativeTable::getInstance()
      ->findByDql('release_id = ? AND alternative_id = ?', array($release_id, $request->getParameter('alternative_id')))
      ->getFirst();

    $projectRelease->value -= $projectReleaseAlternative->value;
    $projectRelease->save();

    $projectReleaseAlternative->delete();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeAddReleaseItem(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $releaseAlternative = new ProjectReleaseAlternative();
    $releaseAlternative->release_id = $request->getParameter('release_id');
    $releaseAlternative->alternative_id = $request->getParameter('alternative_id');
    $releaseAlternative->value = $request->getParameter('value');
    $releaseAlternative->save();

    $releaseAlternative->ProjectRelease->updateValue();
    $releaseAlternative->ProjectRelease->save();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeUpdateRelease(sfWebRequest $request)
  {
    $release = Doctrine::getTable('ProjectRelease')->find($request->getParameter('id'));
    $this->forward404Unless($release);
    $release->name = $request->getParameter('name');
    $release->save();

    // Create log
    $log = new Log();
    $log->injectDataAndPersist($release, $this->getUser()->getGuardUser(), array('action' => 'edit'));

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeDeleteRelease(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    ProjectReleaseTable::getInstance()->createQuery()
      ->delete()
      ->where('id = ?', $request->getParameter('id'))
      ->execute();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeExport(sfWebRequest $request)
  {
    //$this->forward404Unless(in_array($this->getUser()->getGuardUser()->account_type, array('Pro', 'Enterprice')));
    $decision_id = $request->getParameter('decision_id', false);
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($decision);

    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename="' . $decision->name . '.xlsx"');

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($decision_id);
    $criteriaAnalyze->load();

    $logicalFilter = new LogicalFilterView();
    $logicalFilter->setDecisionId($decision_id);
    $logicalFilter->load();

    $roleFilter = new RoleFilterView();
    $roleFilter->setDecisionId($decision_id);
    $roleFilter->load();

    $statusFilter = new StatusFilterView();
    $statusFilter->setDecisionId($decision_id);
    $statusFilter->load();

    $tagFilter = new TagFilterView();
    $tagFilter->setDecisionId($decision_id);
    $tagFilter->load();

    $stackedBarChart = new StackedBarChart();
    $stackedBarChart->setDecisionId($decision_id);
    $stackedBarChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
    $stackedBarChart->setRoleFilterData($roleFilter->getData());
    $stackedBarChart->setStatusFilterData($statusFilter->getData());
    $stackedBarChart->setTagFilterData($tagFilter->getDataForSQL());
    $stackedBarChart->setFilteredAlternativesIds($logicalFilter->getFilteredAlternativesIds());
    $stackedBarChart->load();

    $excelExporter = new AlternativesExcelExporter($stackedBarChart);
    $excelExporter->export();

    exit;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeCollapse(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $decision_id = $request->getParameter('decision_id', false);
    /** @var Decision $decision */
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($decision));

    $field = $request->getParameter('type');
    $analyzeCollapse = $decision->AnalyzeCollapse;
    $analyzeCollapse->{$field} = $request->getParameter('collapse') == 'true';
    $analyzeCollapse->save();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   */
  public function executePlanner(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($this->decision);

    $this->criteriaAnalyze = new CriteriaAnalyze();
    $this->criteriaAnalyze->setDecisionId($decision_id);
    $this->criteriaAnalyze->load();

    $this->logicalFilter = new LogicalFilterView();
    $this->logicalFilter->setDecisionId($decision_id);
    $this->logicalFilter->load();

    $this->roleFilter = new RoleFilterView();
    $this->roleFilter->setDecisionId($decision_id);
    $this->roleFilter->load();

    $this->statusFilter = new StatusFilterView();
    $this->statusFilter->setDecisionId($decision_id);
    $this->statusFilter->load();

    $this->tagFilter = new TagFilterView();
    $this->tagFilter->setDecisionId($decision_id);
    $this->tagFilter->load();

    $this->stackedBarChart = new StackedBarChart();
    $this->stackedBarChart->setDecisionId($decision_id);
    $this->stackedBarChart->setCriteriaValues($this->criteriaAnalyze->getCriteriaValues());
    $this->stackedBarChart->setRoleFilterData($this->roleFilter->getData());
    $this->stackedBarChart->setStatusFilterData($this->statusFilter->getData());
    $this->stackedBarChart->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->stackedBarChart->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->stackedBarChart->load();

    $this->costAnalyze = new CostAnalyze();
    $this->costAnalyze->setDecisionId($decision_id);
    $this->costAnalyze->setSortedAlternativeIds($this->stackedBarChart->getSortedAlternativeIds());
    $this->costAnalyze->setRoleFilterData($this->roleFilter->getData());
    $this->costAnalyze->setStatusFilterData($this->statusFilter->getData());
    $this->costAnalyze->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->costAnalyze->setCumulativeData($this->stackedBarChart->getCumulativeData());
    $this->costAnalyze->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->costAnalyze->load();


    // release planner
    $this->alternatives_json = AlternativeTable::getInstance()->getDashboardReleaseJSON($decision_id, $this->stackedBarChart->getSortedAlternativeIds());
    $this->releases = ProjectReleaseTable::getInstance()->getList($decision_id);
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeLogPinToWall(sfWebRequest $request){
    // Create log
    $log = new Log();
    $log->action = 'budget_create';
    $log->information = json_encode(array('criterion_id' => $request->getParameter('criterion_id')));
    $log->user_id = $this->getUser()->getGuardUser()->id;
    $log->save();

    return $this->renderText('');
  }
}
