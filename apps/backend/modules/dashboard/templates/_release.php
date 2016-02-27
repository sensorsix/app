<?php
  /** @var ProjectRelease $release */
?>

<div class="release release-criterion-<?php echo $release->criterion_id ?>" id="release-<?php echo $release->id ?>">
  <div class="form-group">
    <div class="col-xs-4">
      <input class="autosave form-control" type="text" value="<?php echo $release->name ?>" name="name"/>
    </div>
    <div class="col-xs-2 col-xs-offset-5">
      <a href="javascript:void(0)" data-release_id="<?php echo $release->id ?>" class="right release-delete btn btn-primary"><?php echo __('Delete') ?></a>
    </div>
  </div>
  <div>
    <ol class="list-release" data-release_id="<?php echo $release->id ?>" data-criterion_id="<?php echo $release->criterion_id ?>">
      <?php if ($release->ProjectReleaseAlternative->count()) : ?>
        <?php foreach ($release->ProjectReleaseAlternative as $releaseAlternative) : ?>
          <li data-alternative_id="<?php echo $releaseAlternative->alternative_id ?>" data-value="<?php echo $releaseAlternative->value ?>"><?php echo $releaseAlternative->Alternative->name ?></li>
        <?php endforeach ?>
      <?php endif ?>
    </ol>
  </div>
</div>