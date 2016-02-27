<?php

class AverageAnalyze extends AlternativesAnalyze
{
  public function load()
  {
    $this->prepareData();
  }

  public function prepareData()
  {
    /** @var Response[]|Doctrine_Collection $responses  */
    $responses = $this->getBaseQuery()->execute();

    foreach ($responses as $response) {
      foreach ($response->AlternativeMeasurement as $alternativeMeasurement) {
        /** @var AlternativeMeasurement $alternativeMeasurement */
        $alternative = $alternativeMeasurement->Alternative;
        $criterion = $alternativeMeasurement->Criterion;

        if (!isset($this->criteria_names[$criterion->id])) {
          $this->criteria_names[$criterion->id] = Utility::teaser($criterion->name, 55);
        }

        if (isset($this->measurement[$criterion->id][$alternative->id])) {
          $this->measurement[$criterion->id][$alternative->id]->addScore($alternativeMeasurement->score);
        } else {
          $this->measurement[$criterion->id][$alternative->id] = new AnalyzeAverageScore();
          $this->measurement[$criterion->id][$alternative->id]->addScore($alternativeMeasurement->score);
        }
      }
    }
  }

  /**
   * @return array
   */
  public function getMeasurement() {
    return $this->measurement;
  }

  public function render() {}
}
