<?php

$q=$_GET['q'];
#                        daili/60.shtml
  preg_match("/daili\/([0-9]+)\.shtml/",$q,$a);

  $id=$a[1];
  $dir=intval($a[1]/10);

  include "./daili/{$dir}/{$id}.shtml";

?>
