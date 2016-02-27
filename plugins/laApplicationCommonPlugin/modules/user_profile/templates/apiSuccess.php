<?php
/**
 * @var sfWebResponse $sf_response
 * @var sfGuardSecurityUser $sf_user
 */

$sf_response->setTitle('API');
$sf_response->setSlot('disable_menu', true);
?>
<div class="row">

  <div class="col-md-3">
    <?php include_partial("tabs"); ?>
  </div>

  <div class="col-md-9">
    <h1 class="title"><?php echo __('API') ?></h1>
    <h2>Access token</h2>
    <div class="form-group">
      <label for="access-token" class="col-xs-4 control-label">Access token</label>
      <div class="col-xs-5">
        <input id="access-token" style="cursor: default;" onclick="this.select();" class="form-control" type="text" readonly value="<?php echo $sf_user->getGuardUser()->getLastToken() ?>"/>
      </div>
      <div class="col-xs-3">
        <a id="generate-token-button" class="btn btn-primary" href="javascript:void(0)">Generate new token</a>
      </div>
    </div>
    <br clear="all">
    <div id="api-access-token"></div>

    <hr>
    <h2>API calls </h2>
    <table class="table table-striped table-hover">
      <thead>
      <th>URL</th><th>Method</th>
      </thead>
      <tbody>
      <tr><td>/api/account/user</td><td>GET</td></tr>
      <tr><td>/api/project/list</td><td>GET</td></tr>
      <tr><td>/api/project/:id/details</td><td>GET</td></tr>
      <tr><td>/api/project/create</td><td>POST</td></tr>
      <tr><td>/api/project/update</td><td>PUT</td></tr>
      <tr><td>/api/:project_id/item/list</td><td>GET</td></tr>
      <tr><td>/api/item/:id/details</td><td>GET</td></tr>
      <tr><td>/api/item/create</td><td>POST</td></tr>
      <tr><td>/api/item/update</td><td>PUT</td></tr>
      <tr><td>/api/item/delete</td><td>DELETE</td></tr>
      <tr><td>/api/:project_id/criteria/list</td><td>GET</td></tr>
      <tr><td>/api/criterion/:id/details</td><td>GET</td></tr>
      <tr><td>/api/criterion/create</td><td>POST</td></tr>
      <tr><td>/api/:project_id/role/list</td><td>GET</td></tr>
      <tr><td>/api/role/:id/details</td><td>GET</td></tr>
      <tr><td>/api/role/create</td><td>POST</td></tr>
      <tr><td>/api/:project_id/response/list</td><td>GET</td></tr>
      <tr><td>/api/response/:id/details</td><td>GET</td></tr>
      <tr><td>/api/:role_id/criterion-prioritization/list</td><td>GET</td></tr>
      <tr><td>/api/criterion-prioritization/:id/details</td><td>GET</td></tr>
      <tr><td>/api/criterion-prioritization/create</td><td>POST</td></tr>
      <tr><td>/api/criterion-prioritization/delete</td><td>DELETE</td></tr>
      <tr><td>/api/:role_id/alternative-measurement/list</td><td>GET</td></tr>
      <tr><td>/api/alternative-measurement/:id/details</td><td>GET</td></tr>
      <tr><td>/api/alternative-measurement/create</td><td>POST</td></tr>
      <tr><td>/api/alternative-measurement/delete</td><td>DELETE</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script type="text/javascript">
  $('#generate-token-button').on('click', function () {
    $.get('<?php echo url_for('@user_profile\generateToken') ?>', function(response) {
      $('#access-token').val(response);
    });
  });
</script>
