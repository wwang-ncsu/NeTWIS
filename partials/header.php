<?php require_once __DIR__ . '/../config.php'; ?>
<?php
  
  $page_title = $page_title ?? $SITE_NAME;
  $page_desc  = $page_desc  ?? "The Networking of Wireless Information Systems, NetWIS, laboratory led by Dr. Wenye Wang...";
  $active     = $active     ?? '';
?>
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
  
  <link rel="stylesheet" type="text/css" href="<?= asset('2207NetWisLabfiles/application.css') ?>">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="<?= asset('2207NetWisLabfiles/nanogallery.min.css') ?>">
</head>
<body class="<?= $active ? 'page-' . htmlspecialchars($active) : '' ?>">

<!-- Header -->
<div class="header"> 
  <!-- Professor photos and contact infos -->
  <div class="header__item header__item--professor">
    <div id="photo-header">
      <a href="<?= $BASE_URL ?>/index.php" class="photo-header--figure">
        <img src="<?= asset('2207NetWisLabfiles/wenye1996.jpg') ?>">
        <p>1996 - Beijing</p>
      </a>
      <a href="<?= $BASE_URL ?>/index.php" class="photo-header--figure">
        <img src="<?= asset('2207NetWisLabfiles/wenye2016.jpg') ?>">
        <p>2016 - Raleigh</p>
      </a>
    </div>
    <div>
      <p> 
        <span id="name-prof">Wenye Wang (王文野)</span><br>
        <span id="prof">Professor, IEEE Fellow </span><br>
           Electrical and Computer Engineering<br>
           NC State University
      </p>
      <div class="contact-faculty">
        <ul>
          <li><a><span data-icon=""></span> 919-513-2549</a></li>
          <li><a href="mailto:wwang@ncsu.edu" target="top"><span data-icon=""></span> wwang@ncsu.edu</a></li>
          <li><a href="https://www.google.com/maps/place/Department+of+Electrical+and+Computer+Engineering/@35.772063,-78.674191,17z/data=!3m1!4b1!4m2!3m1!1s0x89acf59e741b1d09:0x3dff08db302f5df4" target="_blank"><span data-icon=""></span> 3056 Engineering Building II, 890 Oval Drive</a></li>
          <li><a><span data-icon=""></span> schedule by emails. </a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Netwis logo, navigation -->
  <div class="header__item header__item--nav">
    <ul>
      <li>
        <a href="<?= $BASE_URL ?>/index.php" id="logo">
          <img src="<?= asset('2207NetWisLabfiles/netwis2.png') ?>" width="100">
        </a>
      </li>
      <li class="<?= is_active('people',$active) ?>"><a href="<?= $BASE_URL ?>/people.php">People</a></li>
      <li class="<?= is_active('gallery',$active) ?>"><a href="<?= $BASE_URL ?>/gallery.php">Gallery</a></li>
      <li class="<?= is_active('faqs',$active) ?>"><a href="<?= $BASE_URL ?>/faqs.php">FAQs</a></li>
    </ul>
  </div>
</div>

<div class="wrapper site-wrapper"> <!-- container for main content and aside -->
  <div class="main"> <!-- container for main content -->
