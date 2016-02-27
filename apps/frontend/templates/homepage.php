<?php
/**
 * @var sfGuardUser $sf_user
 */

use_helper('sfCombine');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <title>Sensorsix</title>
  <link rel="shortcut icon" href="/favicon.ico"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
  <?php include_combined_stylesheets() ?>
  <?php include_combined_javascripts() ?>

  <!--[if IE]>
  <script type="text/javascript" src="/libs/jquery-placeholder/jquery.placeholder.js?v=2.0.8"></script>
  <script type="text/javascript">
    jQuery(function($){ $('input[placeholder], textarea[placeholder]').placeholder(); })
  </script>
  <![endif]-->
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->frontend_top; ?>
</head>
<body>
<!--[if lt IE 9]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
<div class="wrapper">
<div class="">
<header class="site-header homepage">
  <div class="entry-header width-locker">
    <a href="<?php echo url_for('homepage') ?>" class="logo">
      <img src="<?php echo image_path('logo.png'); ?>" alt=""/>
    </a>
    <ul class="main-menu">
      <li><a href="<?php echo url_for('@about') ?>">About</a></li>
      <li><a href="/#pricing">Pricing</a></li>
      <li><a href="<?php echo url_for('@products') ?>">Product tour</a></li>
      <li><a href="<?php echo url_for('@customers') ?>">Customers</a></li>
      <li><a href="<?php echo url_for('@support') ?>">Help</a></li>
    </ul>
    <div class="right-nav">
      <?php if ($sf_user->isAuthenticated()): ?>
        <?php include_partial('global/user_panel'); ?>
      <?php else: ?>
        <a href="<?php echo url_for('sf_guard_signin') ?>" class="btn btn-gray btn-login">Login</a>
      <?php endif; ?>
      <ul class="page-link">
        <li><a href="/blog">Blog</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
    </div>
  </div>
  <div class="search-header width-locker">
    <h2 class="title-header">Prioritize your backlog</h2>
<center>
    <span class="sub-title">Plan releases and collect input.</span>
<br><br><br>
    <a class="btn btn-lg btn-success" href="<?php echo url_for('sf_guard_signin') ?>" data-track="Sign Up"> Sign Up – It’s Free. </a>
</center>
    <?php /*  if ($sf_user->isAnonymous()) : ?>
      <?php include_component('page', 'scheduleDemo') ?>
    <?php endif */  ?>

    <div id="search-header-image-wrapper">
      <img src="<?php echo image_path('home-big-img.png'); ?>" class="wow fadeIn" alt=""/>
      <a class="youtube" href="https://www.youtube.com/embed/mZPLJRKviE8?rel=0&amp;wmode=transparent"><span class="play-button"></span></a>
    </div>
  </div>
</header>

<div class="main-content">
  <section class="decisions-blocks">
    <a name="about"></a>

    <div class="inner-decisions width-locker">
      <div class="title-decisions">
        <span class="big block">Create a clear overview of the most valued features</span>
        <span
          class="small block">Use our powerful online tool to base your product development on facts and not assumptions</span>
      </div>
      <div class="decisions-group">
        <div class="decision-box structure  wow fadeIn" data-wow-delay="1s">
          <div class="title">Collect</div>
          <p class="text">Record all your ideas, bugs, issues etc. when you encounter them</p>
        </div>
        <div class="decision-box collaborate  wow fadeIn" data-wow-delay="1.2s">
          <div class="title">Collaborate</div>
          <p class="text">Engage relevant stakeholder groups and estimate cost, risk & value</p>
        </div>
        <div class="decision-box analyze  wow fadeIn" data-wow-delay="1.2s">
          <div class="title">Analyze</div>
          <p class="text">Explore which features matter the most and optimize your resources</p>
        </div>
        <div class="decision-box plan  wow fadeIn" data-wow-delay="1s">
          <div class="title">Plan</div>
          <p class="text">Plan your road map and share it with stakeholders</p>
        </div>
      </div>
    </div>
  </section>

  <section id="solutions">
    <a name="solutions"></a>

    <div class="width-locker">
      <h4>explore our solutions</h4>
      <h2>Choose the right solution <br/>to fit your needs</h2>
      <article class="aligned-left wow fadeIn">
        <div class="image-holder">
          <img src="<?php echo image_path('idea-management.png'); ?>" alt=""/>
        </div>
        <div class="definition">
          <h4>Idea management</h4>
          <h2>One place for all product ideas</h2>
          <div class="content">
            <p>Your ideas are the raw material for all product innovation. Be sure to capture, document them wherever they arise and gradually detail and improve them. Break big ideas down to smaller components that can be developed.</p>
          </div>
        </div>
      </article>

      <article class="aligned-right wow fadeIn">
        <div class="image-holder">
          <img src="<?php echo image_path('screenshot-analyze.png'); ?>" alt=""/>
        </div>
        <div class="definition">
          <h4>Stakeholder engagement</h4>
          <h2>Get structured input from stakeholders</h2>
          <div class="content">
            <p>Get estimates from engineers, ratings from customers and strategic priorities from senior management, all input will be aggregated to give you the full view of what you should develop</p>
          </div>
        </div>
      </article>

      <article class="aligned-left wow fadeIn">
        <div class="image-holder">
          <img src="<?php echo image_path('screenshot-planner.png'); ?>" alt=""/>
        </div>
        <div class="definition">
          <h4>Roadmap planning</h4>
          <h2>Build the perfect roadmap</h2>
          <div class="content">
            <p>Ensure an efficient creation of a roadmap that can be shared with engineers, salespeople, management and customers to keep everybody on the same page about what is coming when. Quickly spot the implications when one project is delayed</p>
          </div>
        </div>
      </article>
    </div>
  </section>

  <section id="image-cite">
    <a name="customers"></a>
    <div class="width-locker">
      <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
          <q>By using Sensor Six we get a larger certainty that we are developing the right things.</q>
          <cite>Henning Andersen, Product Strategy Manager, Bluegarden
          </cite>
        </div>
        <div class="col-md-2"></div>
      </div>
      <hr>
      <div class="row" style="margin-left:100px">
        <div class="col-md-4"><a href="/customers#coop"><img src="images/logo-coop.png" alt="Coop" /></a></div>
        <div class="col-md-4"><a href="/customers#falconsocial"><img src="images/logo-falconsocial.png" alt="Falcon Social" /></a></div>
        <div class="col-md-4"><a href="/customers#bluegarden"><img src="images/logo-bluegarden.png" alt="Bluegarden" /></a></div>
      </div>
    </div>
  </section>

  <section id="pricing">
    <a name="pricing"></a>
    <div class="width-locker">
      <table class="plans">
        <col width="300"/>
        <col width="70"/>

        <col width="70"/>
        <col width="300"/>
        <thead>
        <tr class="plan-definition">
          <th><h4>Ideal for startups<br/>and to get started</h4></th>
          <td rowspan="2"></td>

          <td rowspan="2"></td>
          <th><h4>For decision making in teams</h4></th>
        </tr>
        <tr>
          <th class="dark-gray"><h2>Free</h2></th>

          <th class="dark-gray"><h2>Enterprise</h2></th>
        </tr>
        </thead>
        <tbody>
        <tr class="price">
          <td class="dark-gray">
           <div style="height:80px;">
           <span class="currency"></span>
            <span class="value">Free </span>
            <br><span class="period">editor/month</span>
            </div>
            <a href="<?php echo url_for('sf_guard_signin') ?>" class="btn btn-lg btn-success">Sign Up</a>
          </td>
          <td rowspan="4"></td>

          <td rowspan="4"></td>
          <td class="dark-gray">
          <div style="height:80px;">
            <span class="period">Call for price</span>
            </div>
            <a href="mailto:<?php echo sfConfig::get('app_info_email')?>" class="btn btn-lg btn-success">Contact</a>
          </td>
        </tr>
        <tr>
          <td class="dark-gray">Unlimited Users</td>

          <td class="dark-gray">Unlimited Users and Teams</td>
        </tr>
        <tr>
          <td class="dark-gray small">Cloud</td>

          <td class="dark-gray small">Cloud or On-premises</td>
        </tr>
        </tbody>
        <!--
        <tfoot>
        <tr>
          <th><a href="<?php echo url_for('@pricing') ?>">View all</a></th>
          <td></td>

          <td></td>
          <th><a href="<?php echo url_for('@pricing') ?>">View all</a></th>
        </tr>
        </tfoot>
      -->
      </table>
    </div>
  </section>
</div>

<footer>
  <div class="content width-locker">
    <div class="connect-wrapper">
      <?php if ($sf_user->isAnonymous()) : ?>
        <div class="login-register pull-left">
          <a class="btn btn-gray btn-lg" href="<?php echo url_for('sf_guard_signin') ?>">Login</a>
        </div>
      <?php endif ?>
      <div class="socials-wrapper">
        <small><a href="<?php echo url_for('@terms'); ?>">Terms</a></small> |
        <h6>Connect with us</h6>
        <ul class="socials">
          <li><a target="_blank" class="twitter" href="https://twitter.com/Sensorsixhq"></a></li>
          <li><a target="_blank" class="linkedin" href="http://www.linkedin.com/company/sensor-six"></a></li>
          <li><a target="_blank" class="googleplus" href="https://plus.google.com/+Sensorsix2020/posts"></a></li>
        </ul>
      </div>
    </div>

    <div class="menu-wrapper">
      <ul class="column menu">
        <li><a href="<?php echo url_for('@about') ?>">About</a></li>
        <li><a href="/#pricing">Pricing</a></li>
        <li><a href="<?php echo url_for('@products') ?>">Product tour</a></li>
        <li><a href="<?php echo url_for('@customers') ?>">Customers</a></li>
      </ul>
      <ul class="column menu">
        <li><a href="/blog">Blog</a></li>
        <li><a href="/contact">Contact</a></li>
      </ul>
      <div class="column address">

        <small>Address: Hejrevej 30, 2400 Copenhagen, Denmark</small>
        <small>Phone: +45 28 55 87 99<br/>
          <a href="mailto:<?php echo sfConfig::get('app_info_email')?>"><?php echo sfConfig::get('app_info_email')?></a></small>
      </div>
      <div class="column subscribe-wrapper">
        <form id="subscribe-newsletters">
          <div class="input-group">
            <input id="subscriber-email" type="email" class="form-control input-lg" name="email" placeholder="Email">
              <span class="input-group-btn">
                <button id="subscribe-button" class="btn btn-success btn-lg" type="button">Get News</button>
              </span>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    setTimeout(function () {
      var a = document.createElement("script");
      var b = document.getElementsByTagName("script")[0];
      a.src = document.location.protocol + "//dnn506yrbagrg.cloudfront.net/pages/scripts/0020/3732.js?" + Math.floor(new Date().getTime() / 3600000);
      a.async = true;
      a.type = "text/javascript";
      b.parentNode.insertBefore(a, b)
    }, 1);
    $(function () {
      $('.youtube').colorbox({iframe:true, innerWidth:640, innerHeight:390});
      new WOW().init();

    });
  </script>
  <?php echo Doctrine::getTable("Scripts")->getSingleton()->frontend_bottom; ?>
</footer>
</div>
</div>
</body>
</html>
