<?php

class ChartsHandler{

  private $decision;

  public function __construct(Decision $decision){
    $this->decision = $decision;
  }

  public function generateChart($post_id)
  {
    $post = Doctrine_Core::getTable('WallPost')->find($post_id);
    $params = json_decode($post->params, true);

    if ($post->type == 'xy') {
      $chart = new PointChart();
      $chart->setPointsXY($params['data']);
      $chart->setXLabelById($params['x']);
      $chart->setYLabelById($params['y']);

      include_partial('xyImage', array('chart' => $chart));
    } else if ($post->type == 'bubble') {
      $chart = new BubbleChart();
      $chart->setPointsXY($params['data']);
      $chart->setXLabelById($params['x']);
      $chart->setYLabelById($params['y']);
      $chart->setZLabelById($params['z']);

      include_partial('bubbleImage', array('chart' => $chart));
    } else if ($post->type == 'cumulative') {
      $chart = new CumulativeGainChart();
      $chart->setPointsXY($params['data']);
      $chart->setXLabelById($params['x']);

      include_partial('cumulativeImage', array('chart' => $chart));
    } else if ($post->type == 'cost') {
      $costAnalyze = new CostAnalyze();
      $costAnalyze->setCriterionId($params['criterion_id']);
      $costAnalyze->setBScore($params['b_score']);
      $costAnalyze->setPool($params['pool']);
      $costAnalyze->setUnallocated($params['unallocated']);
      if (array_key_exists('red_line', $params)){
        $costAnalyze->setRedLine($params['red_line']);
      }
      $costAnalyze->setOrder($params['order']);
      $analyze = $costAnalyze;

      include_partial('costImage', array('analyze' => $analyze));
    } else if ($post->type == 'partition') {
      $releases = Doctrine_Query::create()
        ->from('ProjectRelease r')
        ->leftJoin('r.ProjectReleaseAlternative ra')
        ->leftJoin('ra.Alternative a')
        ->where('r.decision_id = ? AND r.criterion_id = ?', array($this->decision->id, $params['criterion_id']))
        ->execute();

      include_partial('partitionImage', array('releases' => $releases));
    } else {
      $stackedBarChart = $radarChart = null;

      $criteriaAnalyze = new CriteriaAnalyze();
      $criteriaAnalyze->setDecisionId($this->decision->id);
      $criteriaAnalyze->setCriteriaValues($params['graph']);

      if ($post->type == 'criteria') {
        $analyze = $criteriaAnalyze;
      } else {
        $stackedBarChart = new StackedBarChart();
        $stackedBarChart->setDecisionId($this->decision->id);
        $stackedBarChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
        $stackedBarChart->load();

        if ($post->type == 'radar') {
          $radarChart = new RadarChart();
          $radarChart->setData($stackedBarChart->getData());
          $radarChart->setAlternativeNames($stackedBarChart->getAlternativesNames());
          $radarChart->setCriteriaNames($stackedBarChart->getCriteriaNames());
          if (array_key_exists('filter', $params)){
            $radarChart->setFilterData($params['filter']);
          }
          $radarChart->prepareData();
          $analyze = $radarChart;
        } else {
          $analyze = $stackedBarChart;
        }
      }

      include_partial($post->type . 'Image', array(
        'analyze' => $analyze,
        'radarChart' => $radarChart,
        'stackedBarChart' => $stackedBarChart,
        'criteriaAnalyze' => $criteriaAnalyze,
      ));
    }
  }
}