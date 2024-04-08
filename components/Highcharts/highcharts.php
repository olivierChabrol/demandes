<?php

class Highcharts
{
  private $_code;

  public function highcharts($name, $options, $engine='jquery')
  {
    $this->_code = $this->build_code($name, $options, $engine);
  }

  private function build_code($name, $options, $engine='jquery')
  {
    $code = '<script type="text/javascript">';
    if ($engine == 'mootools')
      $code .= 'window.addEvent(\'domready\', function() {';
    else
      $code .= '$(document).ready(function() {';
    $code .= 'var ' . $name . ' = new Highcharts.Chart({';
    $code .= $this->build_options($options);
    $code .= '});});</script>';
    return $code;
  }

  private function build_options($options)
  {
    $code = array();
    foreach ($options as $key => $option)
      $code []= $this->build_option($key, $option);
    return implode(',', $code);
  }

  private function build_option($key, $options)
  {
    $code = $key . ': ';
    if (!is_array(reset($options)))
      $code .= $this->build_properties($options);
    else
      {
        $code .= '[';
        $opts = array();
        foreach ($options as $option)
          {
            $opts []= $this->build_properties($option);
          }
        $code .= implode(',', $opts);
        $code .= ']';
      }
    return $code;
  }

  private function build_properties($options)
  {
    $code = array();
    foreach ($options as $key => $value)
      $code []= $this->build_property($key, $value);
    return '{' . implode(',', $code) . '}';
  }

  private function build_property($key, $value)
  {
    $code = $key . ': ';
    if ($value instanceof HighchartsArray)
      $code .= $value->get();
    else
      $code .= '\'' . $value . '\'';
    return $code;
  }

  public function getCode()
  {
    return $this->_code;
  }

}

class HighchartsArray
{
  private $_array;
  public function HighchartsArray($array)
  {
    $new_array = array();
    foreach ($array as $elem)
      if (is_string($elem))
        $new_array []= '\'' . $elem . '\'';
      else
        $new_array []= $elem;
    $this->_array = $new_array;
  }
  public function get()
  {
    $js_array = '[';
    $js_array .= implode(',', $this->_array);
    $js_array .= ']';
    return $js_array;
  }
}