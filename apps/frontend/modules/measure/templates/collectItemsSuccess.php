<?php
/**
 * @var Alternative $alternatives
 * @var sfResponse $sf_response
 * @var sfGuardUser $sf_user
 * @var Decision $decision
 * @var Role $role
 */
$alternatives = $alternatives->getRawValue();
$sf_response->setTitle('Collect input');
$voted_items_ids = $sf_user->getRawValue()->getAttribute('voted_items_ids', array());
include_component('measure', 'logo');
?>
<h1 class="title text-center"><?php echo $decision->name ?></h1>

<form class="form-horizontal col-md-6 col-md-offset-3" action="<?php echo url_for('@measure\itemSuggestionSave') ?>" method="post">
  <div class="form-group">
    <div class="col-md-12">
      <?php echo $form['name']->render(array('placeholder'=>'Title')) ?>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-12">
      <?php echo $form['additional_info']->render(array('class'=>'form-control','placeholder'=>'Description')) ?>
    </div>
  </div>
  <?php echo $form->renderHiddenFields() ?>
  <button class="btn btn-success pull-right" type="submit"><?php echo __('Submit') ?></button>
  <div class="clear"></div>
</form>

<?php if ($role->display_items) : ?>
  <ul class="collect-input col-md-6 col-md-offset-3">
    <?php foreach ($alternatives as $alternative) : ?>
      <li>
        <div class="title">
          <?php echo $alternative->name ?>
        </div>
        <div class="vote-box">
          <?php if ($role->allow_voting && !in_array($alternative->id, $voted_items_ids)) : ?>
            <a class="vote glyphicon glyphicon-thumbs-up" href="<?php echo url_for('@measure\alternativeVote?id=' . $alternative->id) ?>"></a>
          <?php endif ?>
          <span class="score"><?php echo $alternative->score ?></span>
        </div>
        <div class="clear"></div>
        <div class="description">
          <?php echo $alternative->additional_info ?>
        </div>
      </li>
    <?php endforeach ?>
  </ul>
<?php endif ?>

<div class="form-group clear">
  <div class="col-md-6 col-md-offset-3">
    <a class="btn btn-primary pull-right" href="<?php echo url_for('@measure\measure') ?>">Next</a>
  </div>
</div>

<script>
  $(function () {
    $('.vote').on('click', function (e) {
      var self = this;
      e.preventDefault();
      $.get(this.href, function (response) {
        if (response.length) {
          $(self).closest('.vote-box').find('.score').text(response);
        }
      });
    });
  });
</script>