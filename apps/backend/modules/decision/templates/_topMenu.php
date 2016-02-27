<?php foreach($decisions as $decision): ?>
  <li class="product-list-preview">
    <a href="<?php echo url_for('@dashboard?decision_id=' . $decision->getId()); ?>">
      <span class="name"><?php echo $decision->getName();?></span>
      <span class="description">
      <?php
        if ($decision->getObjective()){
          $match = array();
          $objective = html_entity_decode($decision->getObjective());
          $r = preg_match("/^[^\s]+([\s]+[^\s]+){0,5}/", $objective, $match);
          echo strip_tags($match[0]);
          if ($match[0] != $decision->getObjective()){
            echo ' ...';
          }
        }
        ?>
        </span>
      </a>
  </li>
  <li class="divider"></li>
<?php endforeach ?>