<?php

/**
 * @property Decision $decision
 * @property PlannedCriterionPrioritization|int $head
 * @property PlannedCriterionPrioritization|int $tail
 */
class CriterionPairwiseComparisonMeasurement extends PairwiseComparisonMeasurement
{
  private $decision = null;

  public function __construct($map_data, $load = true)
  {
    parent::__construct($map_data, 'criterion');

    $this->head = $map_data['object_head_id'];
    $this->tail = $map_data['object_tail_id'];
  }

  public function load()
  {
    $collection = Doctrine_Query::create()
      ->from('Criterion c')
      ->andWhereIn('c.id', array($this->head, $this->tail))
      ->limit(2)
      ->execute();

    $this->head = $collection->getFirst();
    $this->tail = $collection->getLast();
    $this->decision = Doctrine_Query::create()
      ->from('Decision d')
      ->where('d.Roles.id = ?', $this->role->id)
      ->fetchOne();
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    $text = 'Which is most important?';
    include_partial('criterion_prioritization', array('decision' => $this->decision, 'text' => $text));
    parent::render();
  }

  public function save()
  {
    $criterionPrioritization = new CriterionPrioritization();
    $criterionPrioritization->criterion_head_id = is_object($this->head) ? $this->head->id : $this->head;
    $criterionPrioritization->criterion_tail_id = is_object($this->tail) ? $this->tail->id : $this->tail;
    $criterionPrioritization->rating_method = 'pairwise comparison';
    $criterionPrioritization->response_id = $this->response_id;
    $criterionPrioritization->score = $this->values;
    $criterionPrioritization->save();
  }
}