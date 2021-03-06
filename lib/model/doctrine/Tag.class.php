<?php

/**
 * Tag
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Tag extends BaseTag
{
  const MAX_LENGTH = 25;

  /**
   * @param sfGuardUser $user
   * @param integer $element_id
   * @param string $name
   * @param string $type
   */
  public static function newTag($user, $element_id, $name, $type)
  {
    $tag = TagTable::getInstance()->findOneByNameAndUserId($name, $user->id);
    if (!$tag) {
      $tag = new Tag();
      $tag->setName(substr($name, 0, self::MAX_LENGTH));
      $tag->setUserId($user->id);
      $tag->save();
    }

    if ($type == 'decision') {
      $tagDecision = new TagDecision();
      $tagDecision->setDecisionId($element_id);
      $tagDecision->setTagId($tag->id);
      $tagDecision->save();
    } else if ($type == 'release') {
      $tagRelease = new TagRelease();
      $tagRelease->setReleaseId($element_id);
      $tagRelease->setTagId($tag->id);
      $tagRelease->save();
    } else {
      $tagAlternative = new TagAlternative();
      $tagAlternative->setAlternativeId($element_id);
      $tagAlternative->setTagId($tag->id);
      $tagAlternative->save();

      Doctrine_Query::create()->delete('Graph')->where('decision_id = ?', $tagAlternative->Alternative->decision_id)->execute();
    }

    return;
  }

  /**
   * @param sfGuardUser $user
   * @param integer $element_id
   * @param string $name
   * @param string $type
   */
  public static function removeTag($user, $element_id, $name, $type)
  {
    if ($type == 'decision'){
      $tagBridge = Doctrine_Core::getTable('TagDecision')
        ->createQuery('td')
        ->leftJoin('td.Tag t')
        ->where('td.decision_id = ?', $element_id)
        ->andWhere('t.name = ?', $name)
        ->andWhere('t.user_id = ?', $user->id)
        ->fetchOne();

    } else if ($type == 'release') {
      $tagBridge = Doctrine_Core::getTable('TagRelease')
          ->createQuery('td')
          ->leftJoin('td.Tag t')
          ->where('td.release_id = ?', $element_id)
          ->andWhere('t.name = ?', $name)
          ->andWhere('t.user_id = ?', $user->id)
          ->fetchOne();
    }else{
      $tagBridge = Doctrine_Core::getTable('TagAlternative')
        ->createQuery('ta')
        ->leftJoin('ta.Tag t')
        ->where('ta.alternative_id = ?', $element_id)
        ->andWhere('t.name = ?', $name)
        ->andWhere('t.user_id = ?', $user->id)
        ->fetchOne();
    }

    if ($tagBridge) {
      $tagBridge->delete();

      $tagDecision = TagDecisionTable::getInstance()->findOneByTagId($tagBridge->tag_id);
      if (!$tagDecision) {
        $tagAlternative = TagAlternativeTable::getInstance()->findOneByTagId($tagBridge->tag_id);
        if (!$tagAlternative) {
          TagTable::getInstance()->find($tagBridge->tag_id)->delete();
        }
      }
    }

    return;
  }
}
