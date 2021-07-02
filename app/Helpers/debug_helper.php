<?php

function debug()
{
  # code...
}

if (! function_exists('print_pre'))
{
  function print_pre($vars,$border=0)
  {
    $style = '';
    if ($border) {
      $style = ' style="border:1px solid #dddddd;padding:2px" ';
    }
    echo '<pre'.$style.'>';
    print_r($vars);
    echo '</pre>';
  }
}