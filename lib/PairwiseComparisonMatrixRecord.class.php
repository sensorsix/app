<?php

class PairwiseComparisonMatrixRecord
{
  private
    $score_sum = 0,
    $counter = 0;

  public function addScore($score)
  {
    $this->score_sum += $score;
    $this->counter++;
  }

  public function getAverage()
  {
    return $this->score_sum / $this->counter;
  }
}