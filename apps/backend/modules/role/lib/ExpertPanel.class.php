<?php
 
class ExpertPanel
{
  const PRICE_RATIO = 20;

  /**
   * @var int
   */
  private $decision_id;

  /**
   * @var int
   */
  private $role_id;

  /**
   * @var int
   */
  private $products_number;

  /**
   * @var int
   */
  private $criteria_number;

  /**
   * @var float
   */
  private $price;

  /**
   * @var int
   */
  private $days_number;

  public function __construct($decision_id, $role_id)
  {
    $this->decision_id = $decision_id;
    $this->role_id = $role_id;
  }

  public function load()
  {
    $this->products_number = AlternativeTable::getInstance()->countForDecision($this->decision_id);
    $this->criteria_number = PlannedAlternativeMeasurementTable::getInstance()->countForRole($this->role_id);
    $this->price = $this->products_number * $this->criteria_number * self::PRICE_RATIO;

    if ($this->price <= 500)
    {
      $this->days_number = 7;
    }
    else if ($this->price > 500 && $this->price <= 2000)
    {
      $this->days_number = 14;
    }
    else
    {
      $this->days_number = 21;
    }
  }

  public function render()
  {
    include_partial('expert_panel', array('panel' => $this));
  }

  /**
   * @param int $decision_id
   */
  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }

  /**
   * @return int
   */
  public function getDecisionId()
  {
    return $this->decision_id;
  }

  /**
   * @param int $role_id
   */
  public function setRoleId($role_id)
  {
    $this->role_id = $role_id;
  }

  /**
   * @return int
   */
  public function getRoleId()
  {
    return $this->role_id;
  }

  /**
   * @return int
   */
  public function getCriteriaNumber()
  {
    return $this->criteria_number;
  }

  /**
   * @return int
   */
  public function getDaysNumber()
  {
    return $this->days_number;
  }

  /**
   * @return float
   */
  public function getPrice()
  {
    return $this->price;
  }

  /**
   * @return int
   */
  public function getProductsNumber()
  {
    return $this->products_number;
  }
}
 