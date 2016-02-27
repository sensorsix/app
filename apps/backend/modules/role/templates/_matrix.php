<?php
/**
 * @var Criterion[]|Doctrine_Collection $criteria
 * @var Alternative[]|Doctrine_Collection $alternatives
 * @var Role $role
 */
?>

<?php if ($criteria->count() && $alternatives->count()) : ?>
  <table cellpadding="0" cellspacing="0" border="0" id="planned-measurement" class="table table-hover table-condensed table-matrix">
    <thead>
      <tr>
        <th><?php echo __('Name') ?><br/><br/></th>
        <?php foreach ($criteria as $criterion) : ?>
          <th style="text-align: center;">
            <span title="<?php echo $criterion->name ?>"><?php echo Utility::teaser($criterion->name, 15) ?></span><br/>
            <input id="all-criterion-<?php echo $criterion->id ?>" type="checkbox" />
          </th>
        <?php endforeach ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($alternatives as $alternative) : ?>
        <tr>
          <td class="active">
            <label title="<?php echo $alternative->name ?>">
              <input id="all-alternative-<?php echo $alternative->id ?>" type="checkbox" /> <?php echo Utility::teaser($alternative->name, 30) ?>
            </label>

          </td>
          <?php foreach ($criteria as $criterion) : ?>
            <td style="text-align: center;">
              <input data-criterion="<?php echo $criterion->id ?>" data-alternative="<?php echo $alternative->id ?>" class="measure criterion-<?php echo $criterion->id ?> alternative-<?php echo $alternative->id ?>" type="checkbox" <?php echo (!$role || $role->getRawValue()->hasPlannedMeasurement($criterion->id, $alternative->id)) ? 'checked="checked"' : '' ?> />
            </td>
          <?php endforeach ?>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <script type="text/javascript">
    $(document).ready(function(){
      oTable = $('#planned-measurement').dataTable( {
        "sScrollY"        : "296px",
        "sScrollX"        : "100%",
        "sScrollXInner"   : "<?php echo $criteria->count() * 170  ?>px",
        "bScrollCollapse" : true,
        "bSort"           : false,
        "bFilter"         : false,
        "bInfo"           : false,
        "bPaginate"       : false,
        "oLanguage": {
          "sInfo"         : "",
          "sInfoEmpty"    : "",
          "sInfoFiltered" : ""
        }
      } );

      new $.fn.dataTable.FixedColumns( oTable, {
        "iLeftColumns": 1,
        "iLeftWidth"  : 250
      } );

      oTable.fnAdjustColumnSizing();
    });
  </script>
<?php endif ?>
