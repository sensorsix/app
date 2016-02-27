<?php /** @var ExpertPanel $panel */ ?>
<div id="expert-panel-container" class="form-horizontal specific-form">
  <div class="form-group">
    <label class="col-xs-2 control-label">Number of products:</label>
    <div class="col-xs-10">
      <span id="expert-panel-products" class="help-block">
        <?php echo $panel->getProductsNumber() ?>
      </span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-xs-2 control-label">Number of criteria:</label>
    <div class="col-xs-10">
      <span id="expert-panel-criteria" class="help-block">
        <?php echo $panel->getCriteriaNumber() ?>
      </span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-xs-2 control-label">Price:</label>
    <div class="col-xs-10">
      <span id="expert-panel-price" class="help-block">
        $<?php echo $panel->getPrice() ?>
      </span>
    </div>
  </div>
  <div class="form-group">
    <label class="col-xs-2">Approximate number of days:</label>
    <div class="col-xs-10">
      <span id="expert-panel-days" class="help-block">
        <?php echo $panel->getDaysNumber() ?>
      </span>
    </div>
  </div>
  <div class="form-group">
    <div class="col-xs-10">
      <a id="hire-experts" class="btn btn-primary" href="javascript:void(0)">Hire experts</a>
    </div>
  </div>
</div>
<script type="text/javascript">
$(function () {
  var $container = $('#expert-panel-container'),
      $criteria_number = $('#expert-panel-criteria', $container),
      $product_number = $('#expert-panel-products'),
      $price = $('#expert-panel-price', $container),
      $days_number = $('#expert-panel-days', $container),
      ratio = <?php echo ExpertPanel::PRICE_RATIO ?>;

  $('#criteria-list li').on('click', function () {
    var criteria_number = $criteria_number.text(),
        price;

    $(this).hasClass('active') ? criteria_number++ : criteria_number--;
    $criteria_number.text(criteria_number);
    price = criteria_number * $product_number.text() * ratio;
    $price.text(price);
    if (price <= 500) {
      $days_number.text(7);
    } else if (price > 500 && price <= 2000) {
      $days_number.text(14);
    } else {
      $days_number.text(21);
    }
  });

  $('#hire-experts', $container).on('click', function () {
    if (confirm('You are hiring experts to evaluate your selected products. It will cost $' + $price.text())) {
      $.post('<?php echo url_for('@role\hireExperts?id=' . $panel->getRoleId()) ?>', {}, function (response) {
        if (response.length) {
          $container.parent().html(response);
        }
      });
    }
  });
});
</script>