<?php

  $info = 'Інформаційний сервіс «Каталог навчальних планів» дозволить Вам зручно створювати нові навчальні плани.';
  $icon = "service.png";
  $mask = 13;

  $mode = !empty($_GET['mode']) ? $_GET['mode'] : 0;

  if($_GET['key'] && $mode == 0) {
    header("Location: http://127.0.0.1:8000/?key=".$_GET['key']);
    exit();
  }

  switch($mode) {
    case 0:
      break;
    case 2:
      header('Content-Type: image/png');
      readfile($icon);
      exit;
    case 3:
      echo $info;
      exit;
    case 100;
      header('X-Cabinet-Support: ' . $mask);
    default:
      exit;
  }