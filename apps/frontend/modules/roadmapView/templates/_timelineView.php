<div id="timeline-embed"></div>

<script type="text/javascript">
  $(function(){
    createStoryJS({
      type:       'timeline',
      width:      '100%',
      height:     '600',
      source:     <?php echo json_encode($sf_data->getRaw('timeline_data')) ?>,
      embed_id:   'timeline-embed'
    });
  });
</script>

<style>
  .flag-content > h3 {
    overflow-x: hidden;
    overflow-y: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: white!important;
  }

  .content-container > .text > .container > h3 {
    color: #999;
  }

  .media-container > .plain-text > .container > h2 {
    color: #999;
  }

  .content-container > .text > .container > h3 > span > a {
    color: #08c;;
  }

  .flag-content {
    overflow-x: hidden;
    overflow-y: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: white!important;
  }

  .flag-content:hover{
    width: auto!important;
  }

  .flag:hover {
    width: auto!important;
  }

  .marker .flag-content {
    opacity: 0.5;
  }

  .marker.active .flag-content {
    opacity: 1;
  }
</style>