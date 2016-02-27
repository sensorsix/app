<ul id="menu" class="nav nav-tabs">
  <li<?php echo has_slot('menu_users_active') ? ' class="active"' : '' ?>><?php echo link_to('Users', '@homepage') ?></li>
  <li<?php echo has_slot('menu_black_list_active') ? ' class="active"' : '' ?>><?php echo link_to('Black list', '@domain') ?></li>
  <li<?php echo has_slot('menu_scripts_active') ? ' class="active"' : '' ?>><?php echo link_to('Scripts', '@scripts_edit') ?></li>
  <li<?php echo has_slot('menu_logs_active') ? ' class="active"' : '' ?>><?php echo link_to('Logs', '@logs') ?></li>
  <li<?php echo has_slot('menu_promo_active') ? ' class="active"' : '' ?>><?php echo link_to('Promo Code', '@promo') ?></li>
  <li<?php echo has_slot('menu_type_template_active') ? ' class="active"' : '' ?>><?php echo link_to('Type Template', '@type_template') ?></li>
</ul>