<?php
  /** @var ProjectRelease $release */
?>

<div class="release release-criterion-<?php echo $release->criterion_id ?>" data-release_id="<?php echo $release->id ?>" id="release-<?php echo $release->id ?>">
  <div class="form-group row">
    <div class="col-md-4">
      <input class="autosave form-control" type="text" value="<?php echo $release->name ?>" name="name"/>
    </div>
    <div class="col-md-2 col-md-offset-5">
      <a href="javascript:void(0)" data-release_id="<?php echo $release->id ?>" class="right release-delete btn btn-danger"><?php echo __('Delete') ?></a>
    </div>
  </div>
  <div>
    <ol class="list-release list-group" data-release_id="<?php echo $release->id ?>" data-criterion_id="<?php echo $release->criterion_id ?>">
      <?php if ($release->ProjectReleaseAlternative->count()) : ?>
        <?php foreach ($release->ProjectReleaseAlternative as $releaseAlternative) : ?>
          <li class="list-group-item" data-alternative_id="<?php echo $releaseAlternative->alternative_id ?>" data-value="<?php echo $releaseAlternative->value ?>"> <i class="fa fa-align-justify"></i> <?php echo $releaseAlternative->Alternative, ' ', intval($releaseAlternative->value) ?></li>
        <?php endforeach ?>
      <?php endif ?>
    </ol>
  </div>
  <div class="release-total">
    Total: <span id="release-total-<?php echo $release->id ?>"><?php echo intval($release->value) ?></span>
  </div>
</div>
