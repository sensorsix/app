<?php
/**
 * @var $roadmaps Roadmap[]
 */
?>

<?php foreach($roadmaps as $roadmap): ?>
  <li class="product-list-preview">
    <a href="<?php echo url_for('@roadmap\dashboard?id=' . $roadmap->getId()); ?>">
      <span class="name"><?php echo $roadmap->getName();?></span>
      <span class="description">
      <?php
        if ($roadmap->getDescription()){
          $match = array();
          $objective = html_entity_decode($roadmap->getDescription());
          $r = preg_match("/^[^\s]+([\s]+[^\s]+){0,5}/", $objective, $match);
          echo strip_tags($match[0]);
          if ($match[0] != $roadmap->getDescription()){
            echo ' ...';
          }
        }
        ?>
        </span>
      </a>
  </li>
  <li class="divider"></li>
<?php endforeach ?>