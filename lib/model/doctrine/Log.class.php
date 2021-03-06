<?php

/**
 * Log
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Log extends BaseLog
{
  /**
   * @param $object
   * @param sfGuardUser $user
   * @param array $additional_info
   */
  public function injectDataAndPersist($object, sfGuardUser $user, $additional_info = array())
  {
    switch (get_class($object)) {
      case 'Folder':
        /** @var Folder $object */
        if ($additional_info['action'] == 'new') {
          $this->action = 'folder_create';
        }else{

        }
        $this->information = json_encode(array('folder_id' => $object->getId(), 'folder_name' => $object->getName(), 'folder_type' => $object->getType()));
        break;
      case 'Criterion':
        /** @var Criterion $object */
        if ($additional_info['action'] == 'new') {
          $this->action = 'criteria_create';
        } else {
          $this->action = 'criteria_update';
        }
        $this->information = json_encode(array('criteria_id' => $object->getId(), 'criteria_name' => $object->getName(), 'decision_id' => $object->getDecisionId()));
        break;
      case 'Role':
        /** @var Role $object */
        if ($additional_info['action'] == 'new') {
          $this->action = 'survey_create';
        } else {
          $this->action = 'survey_update';
        }
        $this->information = json_encode(array('survey_id' => $object->getId(), 'survey_name' => $object->getName(), 'decision_id' => $object->getDecisionId()));
        break;
      case 'Alternative':
        /** @var Alternative $object */
        if ($additional_info['action'] == 'new') {
          $this->action = 'item_create';
        } else {
          $this->action = 'item_update';
        }
        $this->information = json_encode(array('item_id' => $object->getId(), 'item_name' => $object->getName(), 'decision_id' => $object->getDecisionId()));
        break;
      case 'Wall':
        /** @var Wall $object */
        if ($additional_info['action'] == 'new') {
          $this->action = '';
        } else {
          $this->action = 'wall_update';
        }
        $this->information = json_encode(array('wall_id' => $object->getId(), 'type' => $additional_info['type']));
        break;
      case 'ProjectRelease':
        /** @var ProjectRelease $object */
        if ($additional_info['action'] == 'new') {
          $this->action = 'release_create';
        } else {
          $this->action = 'release_update';
        }
        $this->information = json_encode(array('release_name' => $object->getName(), 'release_id' => $object->getId(), 'decision_id' => $object->getDecisionId(), 'criterion_id' => $object->getCriterionId()));
        break;
      case 'Roadmap':
        /** @var Roadmap $object */
        if ($additional_info['action'] == 'new') {
          $this->action = 'roadmap_create';
        } else {
          $this->action = 'roadmap_update';
        }
        $this->information = json_encode(array('roadmap_id' => $object->getId(), 'roadmap_name' => $object->getName(), 'folder_id' => $object->getFolderId()));
        break;
    }

    $this->user_id = $user->id;
    $this->save();
  }

  /**
   * @param $action
   * @return string
   */
  public function actionToStr($action)
  {
    switch ($action) {
      case "login":
        return "User login";
      case "project_create":
        return "Project is created";
      case "folder_create":
        return "Folder is created";
      case "item_create":
        return "Item is created";
      case "item_update":
        return "Item is updated";
      case "criteria_create":
        return "Criteria is created";
      case "criteria_update":
        return "Criteria is updated";
      case "survey_create":
        return "Survey is created";
      case "survey_update":
        return "Survey is updated";
      case "survey_answered":
        return "Survey is answered";
      case "budget_create":
        return "Budget is created";
      case "budget_update":
        return "Budget is updated";
      case "release_create":
        return "Release is created";
      case "release_update":
        return "Release if updated";
      case "wall_update":
        return "Wall is updated";
      case "wall_visit":
        return "Wall is visited";
      case "roadmap_create":
        return "Roadmap is created";
      case "roadmap_update":
        return "Roadmap is updated";
      default:
        return $action;
    }
  }
}
