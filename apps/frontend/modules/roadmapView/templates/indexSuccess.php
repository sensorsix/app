<?php
/**
 * @var Roadmap $roadmap
 * @var array $alternative_relations
 * @var array $related_decisions
 * @var array $timeline_data
 * @var sfOutputEscaperArrayDecorator $sf_data
 */
include_component('roadmapView', 'logo');
?>

<section class="content-header">
  <div class="row">
    <div class="col-md-6">
      <h1> <?php echo $roadmap->getName();?></h1>
    </div>
    <div class="col-md-6">
    </div>
    <div class="row">
</section>
<hr>

<?php
if ($roadmap->getWorkspaceMode() == 'list') {
  include_partial('listView', array(
    'roadmap'               => $roadmap,
    'related_decisions'     => $sf_data->getRaw('alternative_relations'),
    'alternative_relations' => $sf_data->getRaw('related_decisions'),
  ));
}else{
  include_partial('timelineView', array(
    'roadmap'               => $roadmap,
    'timeline_data'         => $timeline_data,
  ));
}
?>

