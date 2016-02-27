<?php
 
class RadarChart
{
  private $data = array();

  private $datasets = array();

  private $alternative_names = array();

  private $criteria_names = array();

  private $legend = array();

  private $filter = array();

  private $alternatives_label;

  public function render()
  {
    $this->prepareData();
    include_partial('radar_chart', array('chart' => $this));
  }

  public function prepareData()
  {
    $alternative_names = array_values($this->alternative_names);
    foreach ($this->data as $i => $value) {
      $color = $this->getColor($i);
      $this->legend[] = array('name' => $alternative_names[$i], 'color' => 'rgb(' . $color . ')');
      $this->datasets[] = array(
        'fillColor' => 'rgba(' . $color . ',0.5)',
        'strokeColor' => 'rgba(' . $color . ',1)',
        'pointColor' => 'rgba(' . $color . ',1)',
        'pointStrokeColor' => '#fff',
        'data' => array_values($value)
      );
    }
  }

  /**
   * @return bool
   */
  public function hasData()
  {
    return count($this->data) > 0;
  }

  /**
   * @param array $data
   */
  public function setData($data)
  {
    // Data transform
    foreach ($data as $criterion_id => $alternatives_values) {
      foreach ($alternatives_values as $alternative_id => $value) {
        $this->data[$alternative_id][$criterion_id] = $value;
      }
    }
  }

  /**
   * @param $num
   * @return string
   */
  private function getColor($num)
  {
    $hash = md5($num + 103);
    $r = hexdec(substr($hash, 0, 2));
    $g = hexdec(substr($hash, 2, 2));
    $b = hexdec(substr($hash, 4, 2));
    $r = intval((255 - $r) / 2) + $r;
    $g = intval((255 - $g) / 3) + $g;
    $b = intval((255 - $b) / 5) + $b;

    return $r . ',' . $g . ',' . $b;
  }

  /**
   * @param array $alternative_names
   */
  public function setAlternativeNames($alternative_names)
  {
    $this->alternative_names = $alternative_names;
  }

  /**
   * @param array $criteria_names
   */
  public function setCriteriaNames($criteria_names)
  {
    $this->criteria_names = $criteria_names;
  }

  /**
   * @return array
   */
  public function getAlternativeNames()
  {
    return array_values($this->alternative_names);
  }

  /**
   * @return array
   */
  public function getCriteriaNames()
  {
    return array_values($this->criteria_names);
  }

  /**
   * @return string
   */
  public function getJsonData()
  {
    return json_encode($this->datasets);
  }

  /**
   * @return string
   */
  public function getCriteriaJson()
  {
    return json_encode(array_values($this->criteria_names));
  }

  /**
   * @return array
   */
  public function getLegend()
  {
    return $this->legend;
  }

  /**
   * @param array $data
   */
  public function setFilterData($data)
  {
    $this->filter = $data;
  }

  /**
   * @return array
   */
  public function getFilterJson()
  {
    return json_encode($this->filter, JSON_NUMERIC_CHECK);
  }

  /**
   * @param string $alternatives_label
   */
  public function setAlternativesLabel($alternatives_label)
  {
    $this->alternatives_label = $alternatives_label;
  }

  /**
   * @return string
   */
  public function getAlternativesLabel()
  {
    return $this->alternatives_label;
  }
}
