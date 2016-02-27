<ul class="nav nav-pills">
  <li<?php echo $active == 'home' ? ' class="active"' : '' ?>><?php echo link_to('Home', '@homepage') ?></li>
  <li<?php echo $active == 'products' ? ' class="active"' : '' ?>><?php echo link_to('Products', '@products') ?></li>
  <li<?php echo $active == 'pricing' ? ' class="active"' : '' ?>><?php echo link_to('Pricing', '@pricing') ?></li>
  <li<?php echo $active == 'about' ? ' class="active"' : '' ?>><?php echo link_to('About', '@about') ?></li>
  <li><a href="/blog"><?php echo __('Blog') ?></a></li>
</ul>

<div class="control-group text-right" style="margin-top: -50px">
  <a class="btn btn-primary" href="<?php echo url_for('/project') ?>"><?php echo __('Launch') ?></a>
</div>



