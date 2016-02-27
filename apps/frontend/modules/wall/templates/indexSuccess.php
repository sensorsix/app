<?php $sf_response->setTitle('Wall') ?>
<h1 class="title text-center"><?php echo $decision->name ?></h1>
<div class="control-group">
  <?php if (!$posts->count()) : ?>
    No posts
  <?php endif ?>
  <?php foreach ($posts as $post): ?>
    <div id="post-<?php echo $post->id ?>" class="wall-box">
      <div class="row-fluid text-center">
        <h5><?php echo $post->title ?></h5>
      </div>
      <div class="row-fluid"><?php echo $post->getRawValue()->content ?></div>
      <div class="row-fluid">
        <div class="description-text">
          <?php echo $post->comment ?>
        </div>
      </div>
    </div>
  <?php endforeach ?>
</div>