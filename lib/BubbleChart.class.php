<?php

class BubbleChart extends PointChart
{
  private $z_label = '';

  protected $graph_type_id = 4;

  public function render()
  {
    include_partial('bubble_chart', array('chart' => $this));
  }

  /**
   * @param $data
   */
  public function setPointsXY($data)
  {
    foreach ($data as $point) {
      $this->data[] = array(floatval($point[0]), floatval($point[1]), floatval($point[2]), $point[3]);
    }
  }

  /**
   * @param $criterion_id
   */
  public function setZLabelById($criterion_id)
  {
    if ($criterion_id) {
      if ($criterion = Doctrine::getTable('Criterion')->find($criterion_id)) {
        $this->z_label = $criterion->name;
      }
    } else {
      $this->z_label = 'Total benefit';
    }
  }

  /**
   * @return string
   */
  public function getZLabel()
  {
    return $this->z_label;
  }
}
