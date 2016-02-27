<?php
echo stylesheet_tag('/libs/bootstrap/css/bootstrap.min.css?v=3.3.2');
?>

<div id="overall-wrapper">
  <div id="page">
    <div id="page-content">

    <?php foreach($releases as $release) : ?>
      <div style="margin-bottom: 15px">
        <div class="row">
          <div class="col-md-6">
            <h4 style="margin-left: 20px"><?php echo $release->name ?></h4>
            <table class="table" style="width: 600px">
              <?php if ($release->ProjectReleaseAlternative->count()) : ?>
                <?php foreach ($release->ProjectReleaseAlternative as $releaseAlternative) : ?>
                  <tr>
                    <td style="width: 400px; padding-left: 20px"><?php echo $releaseAlternative->Alternative ?></td>
                    <td><?php echo $releaseAlternative->value ?></td>
                  </tr>
                <?php endforeach ?>
              <?php endif ?>
              <tr>
                <td style="padding-left: 20px"><strong>Total</strong></td>
                <td><strong><?php echo $release->value ?></strong></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    <?php endforeach ?>
    </div>
  </div>
</div>
