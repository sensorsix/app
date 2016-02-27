<?php

class HtmlToImageService
{
  protected
    $url = '',
    $path = '',
    $data = '',
    $options = array();

  public function run()
  {
    if(file_exists($this->path))
    {
      unlink($this->path);
    }

    if(shell_exec($this->getCommand()))
    {
      throw new sfException('Failed to run: '.$this->getCommand());
    }
  }

  public function setData($data)
  {
    $this->data = escapeshellarg(http_build_query($data));
  }

  public function setOptions(array $options = array())
  {
    $allowed = array('cookies-file', 'disk-cache', 'load-images', 'local-to-remote-url-access', 'script-encoding', 'ignore-ssl-errors');
    foreach ($options as $option => $value)
    {
      if(in_array($option,$allowed))
      {
        $this->options[$option] = $value;
      }
      else
      {
        throw new sfException('The option ' . $option . ' is not supported');
      }
    }
  }

  public function setUrl($url)
  {
    $this->url = escapeshellarg($url);
  }

  public function setImagePath($path)
  {
    $this->path = $path;
  }

  protected function getCommand()
  {
    return dirname(__FILE__) . '/phantomjs' . $this->getOptionsString() . ' ' . dirname(__FILE__) . '/rasterize.js ' . $this->url . ' ' . $this->data . ' ' . $this->path;
  }

  protected function getOptionsString()
  {
    $options_string = ' ';
    foreach ($this->options as $option => $value)
    {
      $options_string .= ' --' . $option;
      if (!empty($value))
      {
        $options_string .= '=' . $value;
      }
    }
    return $options_string;
  }
}
