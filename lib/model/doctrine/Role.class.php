<?php

/**
 * Role
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Role extends BaseRole
{
  private $measurements = array();

  /**
   * @param int $criterion_id
   * @param int $alternative_id
   * @return bool
   */
  public function hasPlannedMeasurement($criterion_id, $alternative_id)
  {
    return isset($this->measurements[$criterion_id][$alternative_id]);
  }

  public function loadPlannedMeasurements()
  {
    /** @var PlannedAlternativeMeasurement[]|Doctrine_Collection $collection */
    $collection = Doctrine::getTable('PlannedAlternativeMeasurement')->findBy('role_id', $this->id);
    foreach ($collection as $measurement) {
      $this->measurements[$measurement->criterion_id][$measurement->alternative_id] = true;
    }
  }

  public function postSave($event)
  {
    if ($this->prioritize) {
      if (!$this->PlannedCriteria->count()) {
        $criteria = CriterionTable::getInstance()->createQuery('c')
          ->select('c.id')
          ->where("c.decision_id = ? AND c.variable_type = 'Benefit'", $this->decision_id)
          ->fetchArray();

        foreach ($criteria as $criterion) {
          $plannedCriterionPrioritization               = new PlannedCriterionPrioritization();
          $plannedCriterionPrioritization->role_id      = $this->id;
          $plannedCriterionPrioritization->criterion_id = $criterion['id'];
          $plannedCriterionPrioritization->save();
        }
      }
    } else {
      Doctrine_Query::create()
        ->from('PlannedCriterionPrioritization pcp')
        ->where('pcp.role_id = ?', $this->id)
        ->delete()
        ->execute();
    }

    $this->refresh(true);
  }

  public function preInsert($event)
  {
    $token = substr(md5(uniqid()), 0, 6);
    while (RoleTable::getInstance()->findOneBy('token', $token)) {
      $token = substr(md5(uniqid()), 0, 6);
    }
    $this->token = $token;
  }

  public function postInsert($event)
  {
    $roleFilter              = new RoleFilter();
    $roleFilter->decision_id = $this->decision_id;
    $roleFilter->role_id     = $this->id;
    $roleFilter->save();
  }

  public function preDelete($event)
  {
    foreach ($this->Files as $file) {
      $file->delete();
    }
  }

  public function getMeasurements()
  {
    $collection   = Doctrine::getTable('PlannedAlternativeMeasurement')->getWithCriterionForRole($this->id);
    $measurements = array();
    foreach ($collection as $measurement) {
      $measurements[$measurement->criterion_id] = str_replace(' ', '', ucwords($measurement->method));
    }

    return $measurements;
  }

  public function getAPIData()
  {
    return array(
      'id'                      => $this->id,
      'name'                    => $this->name,
      'description'             => $this->comment,
      'continue_url'            => $this->continue_url,
      'collect_items'           => $this->collect_items,
      'allow_voting'            => $this->allow_voting,
      'anonymous'               => $this->anonymous,
      'show_comments'           => $this->show_comments,
      'show_criteria_weights'   => $this->show_criteria_weights,
      'show_alternatives_score' => $this->show_alternatives_score
    );
  }

  public function getRowData()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Escaping');
    $routing = sfContext::getInstance()->getRouting();

    $updated_at = new DateTime($this->updated_at);

    return array(
      '_element_type'   => 'role',
      'id'              => $this->id,
      'name'            => $this->name,
      'description'     => esc_raw($this->comment),
      'responses_count' => count($this->getResponse()),
      'dashboard'       => $this->dashboard,
      'active'          => $this->active,
      'updated_at'      => $updated_at->format('M, j H:i'), // Aug, 23 15:43
      'url'             => sfContext::getInstance()->getConfiguration()->generateFrontendUrl('measure', array('token' => $this->token)),
      'fetch_url'       => $routing->generate('role\fetch', array('id' => $this->id)),
      'edit_url'        => $routing->generate('role\edit', array('id' => $this->id)),
      'delete_url'      => $routing->generate('role\delete')
    );
  }
}
