<?php
  /** @var ProjectRelease $release */
?>

<div class="col-release release release-criterion-<?php echo $release->criterion_id ?>" data-criterion_id="<?php echo $release->criterion_id ?>" data-release_id="<?php echo $release->id ?>" id="release-<?php echo $release->id ?>">
  <header>
    <h3>
      <i class="fa fa-align-justify help-tip" style="cursor:move" data-toggle="tooltip" data-placement="right" title="" data-original-title="Press down to move."></i>

      <span class="release-name"> <?php echo $release->name ?></span> <a href="javascript:void(0);" data-release_id="<?php echo $release->id ?>" class="small pull-right edit_release"> Edit</a></h3>
    <small>
      <span class="release-total">
        <?php echo __('Total') ?> <span id="release-total-<?php echo $release->id ?>"><?php echo intval($release->ProjectReleaseAlternative->count()) ?></span> <?php echo __('Items') ?>.
      </span>
    </small>
  </header>
  <ul class="items" data-release_id="<?php echo $release->id ?>" data-criterion_id="<?php echo $release->criterion_id ?>">
    <?php if ($release->ProjectReleaseAlternative->count()) : ?>
      <?php foreach ($release->ProjectReleaseAlternative as $releaseAlternative) : ?>
        <li class="item" data-alternative_id="<?php echo $releaseAlternative->alternative_id ?>" data-value="<?php echo $releaseAlternative->value ?>">
          <i class="fa fa-align-justify help-tip" style="cursor:move" data-toggle="tooltip" data-placement="right" title="" data-original-title="Press down to move."></i>
          <a href="javascript:void(0);" class="small edit_row" data-alternative_id="<?php echo $releaseAlternative->alternative_id ?>"><span class="name"><?php echo $releaseAlternative->Alternative ?></span> <?php echo intval($releaseAlternative->value) ?></a>
        </li>
      <?php endforeach ?>
    <?php endif ?>
  </ul>
</div>
