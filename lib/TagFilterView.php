<?php

class TagFilterView
{
  /**
   * @var array
   */
  private $data = array();

  /**
   * @var Tags[]
   */
  private $tags = array();

  private $data_for_sql = array();

  /**
   * @var int
   */
  private $decision_id;

  public function load()
  {
    /** @var RoleFilter[] $roleFilters  */
    $alternatives = AlternativeTable::getInstance()->findBy('decision_id', $this->decision_id);

    foreach ($alternatives as $alternative) {
      $tagAlternatives = TagAlternativeTable::getInstance()->findBy('alternative_id', $alternative->id);
      foreach ($tagAlternatives as $tagAlternative) {
        $this->tags[$tagAlternative->Tag->id] = $tagAlternative->Tag->name;
      }
    }

    /** @var RoleFilter[] $roleFilters  */
    $tagFilters = TagFilterTable::getInstance()->findBy('decision_id', $this->decision_id);

    foreach ($tagFilters as $tagFilter) {
      $this->data[] = $tagFilter->Tag->name;

      $ta_by_name = Doctrine_Query::create()->from('TagAlternative ta')
        ->select('ta.id as id, a.id as a_id')
        ->leftJoin('ta.Tag t')
        ->leftJoin('ta.Alternative a')
        ->where("t.name = ?", $tagFilter->Tag->name)
        ->andWhere('a.decision_id = ?', $this->decision_id)
        ->execute();

      foreach($ta_by_name as $v) {
        $this->data_for_sql[] = $v->a_id;
      }
    }

    $this->tags = array_unique($this->tags);
  }

  public function render()
  {
    include_partial('tag_filter', array('tags' => $this->tags, 'data' => $this->data));
  }

  /**
   * @param int $decision_id
   */
  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }

  /**
   * @return array
   */
  public function getData()
  {
    return $this->data;
  }

  /**
   * @return array
   */
  public function getDataForSQL()
  {
    return $this->data_for_sql;
  }
}