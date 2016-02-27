<div id="dashboard_roadmap_description" class="row">
  <div class="col-md-11 lead">
    <?php if ($roadmap->getShowDescription()): ?>
      <?php echo sfOutputEscaperGetterDecorator::unescape($roadmap->getDescription()); ?>
    <?php endif; ?>
  </div>
  <div class="col-md-1">
    <span class="roadmap-blue-label"><?php echo $roadmap->getStatus(); ?></span>
  </div>
</div>
<hr style="margin-bottom: 0;">

<?php foreach ($roadmap->getOrderedRoadmapDecision() as $roadmapDecision): ?>
  <div class="roadmap-decision-wrapper" style="border-left: 20px solid <?php echo $roadmapDecision->getDecision()->getColor() ?: 'transparent'; ?>; padding-left: 10px;">
    <div class="row">
      <div class="col-md-7"><h2 class="roadmap-decision-header"><?php echo $roadmapDecision->getDecision()->getName(); ?></h2></div>
      <div class="col-md-4">
        <?php
        if ($roadmapDecision->getDecision()->getStartDate()){
          echo '<h4 class="text-primary roadmap-decision-date">' . DateTime::createFromFormat('Y-m-d H:i:s', $roadmapDecision->getDecision()->getStartDate())->format('j M Y') . '</h4>';
        }else{
          echo "<i><small>not set</small></i>";
        }
        ?>
        -
        <?php
        if ($roadmapDecision->getDecision()->getEndDate()){
          echo '<h4 class="text-primary roadmap-decision-date">' . DateTime::createFromFormat('Y-m-d H:i:s', $roadmapDecision->getDecision()->getEndDate())->format('j M Y') . '</h4>';
        }else{
          echo "<i><small>not set</small></i>";
        }
        ?>
      </div>
      <div class="col-md-1">
        <span class="roadmap-blue-label" style="background-color: <?php echo $roadmapDecision->getDecision()->getStatusColor(); ?>;"><?php echo $roadmapDecision->getDecision()->getStatus(); ?></span>
      </div>
    </div>

    <div class="row" style="margin-top: 15px;">
      <div class="col-md-8"><?php echo sfOutputEscaperGetterDecorator::unescape($roadmapDecision->getDecision()->getObjective()); ?></div>
      <div class="col-md-4 pull-right">
        <?php foreach ($roadmapDecision->getDecision()->getTagDecision() as $tagDecision): ?>
          <span class="tag label label-info"><?php echo $tagDecision->getTag()->getName(); ?></span>
        <?php endforeach; ?>
      </div>
    </div>

    <?php if ($roadmap->getShowReleases() && count($roadmapDecision->getDecision()->getProjectRelease())): ?>
      <div class="row" style="margin-top: 15px;">
        <div class="col-md-12"><h3 class="roadmap-releases-header"><p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::RELEASE_TYPE, true) ?></p></h3></div>
      </div>

      <div class="roadmap-releases-list">
        <?php foreach ($roadmapDecision->getDecision()->getProjectRelease() as $project_release): ?>
          <div class="row">
            <div class="col-md-12"><h4 class="roadmap-releases-header"><?php echo $project_release->getName(); ?></h4></div>

            <?php if ($roadmap->getShowItems()): ?>
              <?php foreach ($project_release->getProjectReleaseAlternative() as $projectReleaseAlternative): ?>
                <div class="col-md-12">
                  <?php echo $projectReleaseAlternative->getAlternative()->getName(); ?>
                  <?php if (count($projectReleaseAlternative->getAlternative()->getAlternativeRelation())): ?>
                    <i class="fa fa-link"></i>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php if ($roadmap->getShowDependencies()): ?>
      <?php if (array_key_exists($roadmapDecision->getDecision()->getId(), $alternative_relations)): ?>
        <div class="row" style="margin-top: 15px;">
          <div class="col-md-12">
            <b>This project has dependency to project(s) <?php echo implode(', ', $related_decisions[$roadmapDecision->getDecision()->getId()]); ?></b>
            <ul>
              <?php foreach ($alternative_relations[$roadmapDecision->getDecision()->getId()] as $alternative_relation): ?>
                <?php foreach ($alternative_relation['relations'] as $relation): ?>
                  <li>
                    - <?php echo $alternative_relation['alternative']->getName(); ?> has dependency to

                    <?php foreach ($relation['linked_alternatives'] as $linked_alternative): ?>
                      <?php echo $linked_alternative->getName(); ?>
                    <?php endforeach; ?>

                    in project <?php echo $relation['decision']->getName(); ?>
                  </li>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <hr style="margin-bottom: 0; margin-top: 0;">
<?php endforeach; ?>