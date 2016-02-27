<?php
$routes = $sf_context->getRouting()->getRoutes();
?>
<div class="row">
  <div class="col-md-12">
    <a class="auth-link google_oauth" href="<?php echo url_for('sf_guard_auth', array('auth_method' => 'google')); ?>">
      <span class="auth-icon"><i></i></span>
      <span class="auth-title">Sign Up With Google</span>
    </a>
  </div>
  <div class="col-md-12">
    <?php if (isset($routes['sf_guard_register'])): ?>
      <a class="auth-link sensorsix_oauth" href="<?php echo url_for('@sf_guard_register'); ?>">
        <span class="auth-icon"><i></i></span>
        <span class="auth-title">Sign Up With SensorSix</span>
      </a>
    <?php endif; ?>
  </div>
  <div class="col-md-12 sign-up-disclaimer text-left">
    <?php if (isset($routes['sf_guard_register'])): ?>
      <a href="<?php echo url_for('@sf_guard_register') ?>">Sign Up With Email</a>. By signing up you indicate that you have read and agree to the Terms of Service.
    <?php endif; ?>
  </div>
</div>
<!--    <li class="auth-service linkedin"><a class="auth-link linkedin" href="/auth/linkedin"><span class="auth-icon linkedin"><i></i></span><span class="auth-title">LinkedIn</span></a></li>-->
<!--    <li class="auth-service facebook"><a class="auth-link facebook" href="/auth/facebook"><span class="auth-icon facebook"><i></i></span><span class="auth-title">Facebook</span></a></li>-->

<script>
  jQuery(function ($) {
    var popup;

    $.fn.eauth = function (options) {
      options = $.extend({
        id: '',
        popup: {
          width: 450,
          height: 380
        }
      }, options);

      return this.each(function () {
        var el = $(this);
        el.click(function () {
          if (popup !== undefined)
            popup.close();

          var redirect_uri,
            url = redirect_uri = this.href;

          url += url.indexOf('?') >= 0 ? '&' : '?';
          if (url.indexOf('redirect_uri=') === -1)
            url += 'redirect_uri=' + encodeURIComponent(redirect_uri) + '&';
          url += 'js';

          var centerWidth = (window.screen.width - options.popup.width) / 2,
            centerHeight = (window.screen.height - options.popup.height) / 2;

          popup = window.open(url, "sensorsix_eauth_popup", "width=" + options.popup.width + ",height=" + options.popup.height + ",left=" + centerWidth + ",top=" + centerHeight + ",resizable=yes,scrollbars=no,toolbar=no,menubar=no,location=no,directories=no,status=yes");
          popup.focus();

          return false;
        });
      });
    };
  });
</script>

<script type="text/javascript">
  jQuery(function($) {
    $("a.google_oauth").eauth({"popup": {"width": 500, "height": 450}, "id": "google_oauth"});
//    $(".auth-service.linkedin a").eauth({"popup": {"width": 900, "height": 550}, "id": "linkedin"});
//    $(".auth-service.facebook a").eauth({"popup": {"width": 585, "height": 290}, "id": "facebook"});
  });
</script>