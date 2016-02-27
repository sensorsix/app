<?php

/**
 * api actions.
 *
 * @package    dmp
 * @subpackage api
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apiActions extends sfActions
{
  private function getAccessToken()
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $token = false;
    if ($request->hasParameter('access_token'))
    {
      $token = $request->getParameter('access_token');
    }
    else
    {
      $path_info = $request->getPathInfoArray();
      if (array_key_exists('HTTP_HTTP_AUTHORIZATION', $path_info))
      {
        $token = preg_replace('/^(\+s)?\w+\s+/i', '', $path_info['HTTP_HTTP_AUTHORIZATION']);
      }
    }

    return $token;
  }

  private function prepareResponse($type)
  {
    $user = sfGuardUserTable::getInstance()->getUserByToken($this->getAccessToken());

    if (is_object($user))
    {
      $method = 'get' . $type . 'Response';
      $response = $this->$method($user);
    }
    else
    {
      $response = array('status' => 'error', 'error' => 'The access token is missing or invalid');
      $this->getResponse()->setStatusCode(401);
    }

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode($response));
  }

  public function getUserResponse(sfGuardUser $user)
  {
	  $request = $this->getRequest();
	  if ($user->is_super_admin && $request->hasParameter('user_id')){
		  $request_user = sfGuardUserTable::getInstance()->find($request->getParameter('user_id'));
		  if ($request_user){
			  return array(
				  'status' => 'success',
				  'result' => array(
					  'user_id' => $request_user->id,
					  'email' => $request_user->email_address,
					  'account_type' => $request_user->account_type
				  )
			  );
		  }else{
			  return array('status' => 'error', 'error' => 'User not found');
		  }
	  }

    return array(
      'status' => 'success',
      'result' => array(
        'user_id' => $user->id,
        'email' => $user->email_address,
        'account_type' => $user->account_type
      )
    );
  }

  public function getProjectListResponse(sfGuardUser $user)
  {
    return array(
      'status' => 'success',
      'result' => DecisionTable::getInstance()->getListForAPI($user)
    );
  }

  public function getProjectDetailsResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $decision = DecisionTable::getInstance()->getDecisionForUser($user, $request->getParameter('id'));
    if (is_object($decision))
    {
      $result = array(
        'status' => 'success',
        'result' => $decision->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Project with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getProjectCreateResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    $decision = new Decision();
    $decision->User = $user;
    $decision->name = $request->getParameter('name', 'New project (API)');
    $decision->objective = $request->getParameter('description', '');
    $decision->type_id = 1;
    $decision->template_id = 1;

    try
    {
      $decision->save();

      $result = array(
        'status' => 'success',
        'result' => $decision->getAPIData()
      );
    }
    catch (sfException $ex)
    {
      $result = array(
        'status' => 'error',
        'error' => $ex->getMessage()
      );
    }

    return $result;
  }

  public function getProjectUpdateResponse(sfGuardUser $user)
  {
    $request = $this->getRequest();
    $decision = DecisionTable::getInstance()->getDecisionForUser($user, $request->getParameter('id'));

    if (is_object($decision))
    {
      if ($request->hasParameter('name'))
      {
        $decision->name = $request->getParameter('name');
      }

      if ($request->hasParameter('description'))
      {
        $decision->objective = $request->getParameter('description');
      }

      if ($decision->isModified())
      {
        $decision->save();
      }

      $result = array(
        'status' => 'success',
        'result' => $decision->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Project with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getItemListResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    return array(
      'status' => 'success',
      'result' => AlternativeTable::getInstance()->getListForAPI($user, $request->getParameter('decision_id'))
    );
  }

  public function getItemDetailsResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $alternative = AlternativeTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($alternative))
    {
      $result = array(
        'status' => 'success',
        'result' => $alternative->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getItemCreateResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    $decision = DecisionTable::getInstance()->getDecisionForUser($user, $request->getParameter('decision_id'));
    if (is_object($decision))
    {
      $alternative = new Alternative();
      $alternative->Decision = $decision;
      $alternative->name = $request->getParameter('name', 'New ' . $decision->getAlternativeAlias());
      $alternative->additional_info = $request->getParameter('description', '');
      $alternative->status = $request->getParameter('status', 'New');
      $alternative->setCreatedBy(Alternative::generateUpdateAndCreatedBy($user));
      $alternative->setUpdatedBy(Alternative::generateUpdateAndCreatedBy($user));

      try
      {
        $alternative->save();

        $result = array(
          'status' => 'success',
          'result' => $alternative->getAPIData()
        );
      }
      catch (sfException $ex)
      {
        $result = array(
          'status' => 'error',
          'error' => $ex->getMessage()
        );
      }
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Project with id %d does not exist', $request->getParameter('decision_id'))
      );
    }

    return $result;
  }

  public function getItemUpdateResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $alternative = AlternativeTable::getInstance()->getOneForUser($user, $request->getParameter('id'));

    if (is_object($alternative))
    {
      if ($request->hasParameter('name'))
      {
        $alternative->name = $request->getParameter('name');
      }

      if ($request->hasParameter('description'))
      {
        $alternative->additional_info = $request->getParameter('description');
      }

      if ($request->hasParameter('status') && in_array($request->getParameter('status'), array('Draft', 'Reviewed', 'Planned', 'Doing', 'Finished', 'Parked')))
      {
        $alternative->status = $request->getParameter('status');
      }

      if ($alternative->isModified())
      {
        $alternative->save();
      }

      $result = array(
        'status' => 'success',
        'result' => $alternative->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getItemDeleteResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $alternative = AlternativeTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($alternative))
    {
      $alternative->delete();
      $result = array(
        'status' => 'success'
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getCriterionListResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    return array(
      'status' => 'success',
      'result' => CriterionTable::getInstance()->getListForAPI($user, $request->getParameter('decision_id'))
    );
  }

  public function getCriterionDetailsResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $criterion = CriterionTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($criterion))
    {
      $result = array(
        'status' => 'success',
        'result' => $criterion->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getCriterionCreateResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    $decision = DecisionTable::getInstance()->getDecisionForUser($user, $request->getParameter('decision_id'));
    if (is_object($decision))
    {
      $criterion = new Criterion();
      $criterion->Decision = $decision;
      $criterion->name = $request->getParameter('name', 'New criterion');
      $criterion->description = $request->getParameter('description', '');
      $criterion->variable_type = $request->getParameter('type', 'Benefit');
      $criterion->measurement = $request->getParameter('measure', 'five point scale');

      try
      {
        $criterion->save();

        $result = array(
          'status' => 'success',
          'result' => $criterion->getAPIData()
        );
      }
      catch (sfException $ex)
      {
        $result = array(
          'status' => 'error',
          'error' => $ex->getMessage()
        );
      }
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Project with id %d does not exist', $request->getParameter('decision_id'))
      );
    }

    return $result;
  }

  public function getRoleListResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    return array(
      'status' => 'success',
      'result' => RoleTable::getInstance()->getListForAPI($user, $request->getParameter('decision_id'))
    );
  }

  public function getRoleDetailsResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $role = RoleTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($role))
    {
      $result = array(
        'status' => 'success',
        'result' => $role->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getRoleCreateResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    $decision = DecisionTable::getInstance()->getDecisionForUser($user, $request->getParameter('decision_id'));
    if (is_object($decision))
    {
      $role = new Role();
      $role->Decision = $decision;
      $role->name = $request->getParameter('name', 'New role');
      $role->comment = $request->getParameter('description', '');

      try
      {
        $role->save();

        $result = array(
          'status' => 'success',
          'result' => $role->getAPIData()
        );
      }
      catch (sfException $ex)
      {
        $result = array(
          'status' => 'error',
          'error' => $ex->getMessage()
        );
      }
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Project with id %d does not exist', $request->getParameter('decision_id'))
      );
    }

    return $result;
  }

  public function getResponseListResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    return array(
      'status' => 'success',
      'result' => ResponseTable::getInstance()->getListForAPI($user, $request->getParameter('decision_id'))
    );
  }

  public function getResponseDetailsResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $role = RoleTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($role))
    {
      $result = array(
        'status' => 'success',
        'result' => $role->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }
  
  public function getCriterionPrioritizationListResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    return array(
      'status' => 'success',
      'result' => PlannedCriterionPrioritizationTable::getInstance()->getListForAPI($user, $request->getParameter('role_id'))
    );
  }

  public function getCriterionPrioritizationDetailsResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $alternative = PlannedCriterionPrioritizationTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($alternative))
    {
      $result = array(
        'status' => 'success',
        'result' => $alternative->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getCriterionPrioritizationCreateResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    $role = RoleTable::getInstance()->getOneForUser($user, $request->getParameter('role_id'));
    if (is_object($role))
    {
      $plannedCriterion = new PlannedCriterionPrioritization();
      $plannedCriterion->Role = $role;
      $plannedCriterion->criterion_id = $request->getParameter('criterion_id');

      try
      {
        $plannedCriterion->save();

        $result = array(
          'status' => 'success',
          'result' => $plannedCriterion->getAPIData()
        );
      }
      catch (sfException $ex)
      {
        $result = array(
          'status' => 'error',
          'error' => $ex->getMessage()
        );
      }
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Project with id %d does not exist', $request->getParameter('decision_id'))
      );
    }

    return $result;
  }


  public function getCriterionPrioritizationDeleteResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $plannedCriterion = PlannedCriterionPrioritizationTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($plannedCriterion))
    {
      $plannedCriterion->delete();
      $result = array(
        'status' => 'success'
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }
  
  public function getAlternativeMeasurementListResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    return array(
      'status' => 'success',
      'result' => PlannedAlternativeMeasurementTable::getInstance()->getListForAPI($user, $request->getParameter('role_id'))
    );
  }

  public function getAlternativeMeasurementDetailsResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $alternative = PlannedAlternativeMeasurementTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($alternative))
    {
      $result = array(
        'status' => 'success',
        'result' => $alternative->getAPIData()
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function getAlternativeMeasurementCreateResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();

    $role = RoleTable::getInstance()->getOneForUser($user, $request->getParameter('role_id'));
    if (is_object($role))
    {
      $plannedMeasurement = new PlannedAlternativeMeasurement();
      $plannedMeasurement->Role = $role;
      $plannedMeasurement->criterion_id = $request->getParameter('criterion_id');
      $plannedMeasurement->alternative_id = $request->getParameter('alternative_id');

      try
      {
        $plannedMeasurement->save();

        $result = array(
          'status' => 'success',
          'result' => $plannedMeasurement->getAPIData()
        );
      }
      catch (sfException $ex)
      {
        $result = array(
          'status' => 'error',
          'error' => $ex->getMessage()
        );
      }
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Project with id %d does not exist', $request->getParameter('decision_id'))
      );
    }

    return $result;
  }

  public function getAlternativeMeasurementDeleteResponse(sfGuardUser $user)
  {
    /** @var sfWebRequest $request */
    $request = $this->getRequest();
    $plannedMeasurement = PlannedAlternativeMeasurementTable::getInstance()->getOneForUser($user, $request->getParameter('id'));
    if (is_object($plannedMeasurement))
    {
      $plannedMeasurement->delete();
      $result = array(
        'status' => 'success'
      );
    }
    else
    {
      $result = array(
        'status' => 'error',
        'error' => sprintf('Item with id %d does not exist', $request->getParameter('id'))
      );
    }

    return $result;
  }

  public function executeUser(sfWebRequest $request)
  {
    return $this->prepareResponse('User');
  }

  public function executeProjectList(sfWebRequest $request)
  {
    return $this->prepareResponse('ProjectList');
  }

  public function executeProjectDetails(sfWebRequest $request)
  {
    return $this->prepareResponse('ProjectDetails');
  }

  public function executeProjectCreate(sfWebRequest $request)
  {
    return $this->prepareResponse('ProjectCreate');
  }

  public function executeProjectUpdate(sfWebRequest $request)
  {
    return $this->prepareResponse('ProjectUpdate');
  }

  public function executeItemList(sfWebRequest $request)
  {
    return $this->prepareResponse('ItemList');
  }

  public function executeItemDetails(sfWebRequest $request)
  {
    return $this->prepareResponse('ItemDetails');
  }

  public function executeItemCreate(sfWebRequest $request)
  {
    return $this->prepareResponse('ItemCreate');
  }

  public function executeItemUpdate(sfWebRequest $request)
  {
    return $this->prepareResponse('ItemUpdate');
  }

  public function executeItemDelete(sfWebRequest $request)
  {
    return $this->prepareResponse('ItemDelete');
  }

  public function executeCriterionList(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionList');
  }

  public function executeCriterionDetails(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionDetails');
  }

  public function executeCriterionCreate(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionCreate');
  }

  public function executeRoleList(sfWebRequest $request)
  {
    return $this->prepareResponse('RoleList');
  }

  public function executeRoleDetails(sfWebRequest $request)
  {
    return $this->prepareResponse('RoleDetails');
  }

  public function executeRoleCreate(sfWebRequest $request)
  {
    return $this->prepareResponse('RoleCreate');
  }

  public function executeResponseList(sfWebRequest $request)
  {
    return $this->prepareResponse('ResponseList');
  }

  public function executeResponseDetails(sfWebRequest $request)
  {
    return $this->prepareResponse('ResponseDetails');
  }

  public function executeCriterionPrioritizationList(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionPrioritizationList');
  }

  public function executeCriterionPrioritizationDetails(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionPrioritizationDetails');
  }

  public function executeCriterionPrioritizationCreate(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionPrioritizationCreate');
  }

  public function executeCriterionPrioritizationUpdate(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionPrioritizationUpdate');
  }

  public function executeCriterionPrioritizationDelete(sfWebRequest $request)
  {
    return $this->prepareResponse('CriterionPrioritizationDelete');
  }

  public function executeAlternativeMeasurementList(sfWebRequest $request)
  {
    return $this->prepareResponse('AlternativeMeasurementList');
  }

  public function executeAlternativeMeasurementDetails(sfWebRequest $request)
  {
    return $this->prepareResponse('AlternativeMeasurementDetails');
  }

  public function executeAlternativeMeasurementCreate(sfWebRequest $request)
  {
    return $this->prepareResponse('AlternativeMeasurementCreate');
  }

  public function executeAlternativeMeasurementUpdate(sfWebRequest $request)
  {
    return $this->prepareResponse('AlternativeMeasurementUpdate');
  }

  public function executeAlternativeMeasurementDelete(sfWebRequest $request)
  {
    return $this->prepareResponse('AlternativeMeasurementDelete');
  }
}
