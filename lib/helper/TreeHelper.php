<?php


/**
 * @param array $nodes
 * @param string $field
 * @return string
 */
function tree_render($nodes, $field = 'name')
{
  $result = array();

  foreach ($nodes as $node)
  {
    // Has children
    if (isset($node['children']))
    {
      $result[] = "<li id=\"node-{$node['data']->id}\"><a href=\"#\">{$node['data']->$field}</a>" . tree_render($node['children'], $field) . '</li>';
    }
    else
    {
      $result[] = "<li id=\"node-{$node['data']->id}\"><a href=\"#\">{$node['data']->$field}</a></li>";
    }
  }

  return '<ul>' . implode("\n", $result) . '</ul>';
}