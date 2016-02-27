<?php
/** @var CostAnalyze $analyze */
$analyze = $analyze->getRawValue();
$red_line = $analyze->getRedLine();

echo stylesheet_tag('/libs/bootstrap/css/bootstrap.min.css?v=3.3.2');
?>
<style>
  .red-line-bottom td {
    border-bottom: 1px solid red!important;
  }

  .red-line-top td {
    border-top: 1px solid red!important;
  }
</style>

<div id="overall-wrapper">
  <div id="page" style="width: 600px">
    <div id="page-content">
      <table style="width: 300px; margin: 20px">
        <tbody>
          <tr>
            <td><strong>Cost variable:</strong></td>
            <td><?php echo $analyze->getCriterionName(); ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><strong>Pool</strong></td>
            <td><strong>Unallocated</strong></td>
          </tr>
          <tr>
            <td><?php echo $analyze->getPool() ?></td>
            <td><?php echo $analyze->getUnallocated() ?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><strong>Total benefit:</strong></td>
            <td><?php echo $analyze->getBScore() ?></td>
          </tr>
        </tbody>
      </table>

    <div class="row">
      <div class="col-md-6">
        <div id="cost-estimate-wrap">
          <table id="cost-estimate-table" class="table">
            <?php foreach ($analyze->getOrder() as $item) :
              $class = $item['forced'] != 1 ? ($item['forced'] == 1 ? 'forced-out' : 'forced-in') : '';
              $class .= $item['id'] == $red_line['alternative_id'] ? $red_line['class'] : '';
            ?>
            <tr class="<?php echo $class ?> cost-alternative" id="cost-alternative-<?php echo $item['id'] ?>">
              <td>
                <a <?php echo $item['forced'] == 2 ? 'class="force-in force-info"' : 'style="display: none"' ?> href="javascript:void(0)"></a>
                <a <?php echo $item['forced'] == 0 ? 'class="force-out force-info"' : 'style="display: none"' ?> href="javascript:void(0)"></a>
              </td>
              <td><?php echo $item['name'] ?></td>
              <td><?php echo $item['price'] ?></td>
            </tr>
            <?php endforeach ?>
          </table>
        </div>
      </div>
    </div>
    </div>
  </div>
</div>