<?php
$basePath  = '';
$_title    = isset($pageTitle)    ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8')    : 'Mithooos — Authentic Sindhi Ajrak, Topi &amp; Cultural Products';
$_desc     = isset($pageDesc)     ? htmlspecialchars($pageDesc,  ENT_QUOTES, 'UTF-8')    : 'Shop authentic Sindhi Ajrak, Topi, Kajoor dates, Achaar &amp; home textiles at Mithooos. Handcrafted cultural products delivered worldwide from Sindh, Pakistan.';
$_canonical = isset($pageCanonical) ? htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8') : 'https://mithooos.com/';
$_ogType   = isset($pageOgType)   ? htmlspecialchars($pageOgType, ENT_QUOTES, 'UTF-8')  : 'website';
$_ogImage  = isset($pageOgImage)  ? htmlspecialchars($pageOgImage, ENT_QUOTES, 'UTF-8') : 'https://mithooos.com/images/hero1.png';
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/svg+xml" href="<?= $basePath ?>/favicon.png">
  <link rel="icon" type="image/x-icon" href="<?= $basePath ?>/favicon.ico">
  <link rel="apple-touch-icon" href="<?= $basePath ?>/apple-touch-icon.png">
  <title><?= $_title ?></title>
  <meta name="description" content="<?= $_desc ?>">
  <meta name="robots" content="index, follow">

  <!-- Open Graph / Twitter Card -->
  <meta property="og:type" content="<?= $_ogType ?>">
  <meta property="og:title" content="<?= $_title ?>">
  <meta property="og:description" content="<?= $_desc ?>">
  <meta property="og:image" content="<?= $_ogImage ?>">
  <meta property="og:url" content="<?= $_canonical ?>">
  <meta property="og:site_name" content="Mithooos">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $_title ?>">
  <meta name="twitter:description" content="<?= $_desc ?>">
  <meta name="twitter:image" content="<?= $_ogImage ?>">
  <link rel="canonical" href="<?= $_canonical ?>">

  <!-- Global Schema: Organization + WebSite -->
  <script type="application/ld+json">
  {"@context":"https://schema.org","@graph":[
    {"@type":"Organization","@id":"https://mithooos.com/#org","name":"Mithooos","url":"https://mithooos.com/","logo":{"@type":"ImageObject","url":"https://mithooos.com/images/logo.png"},"sameAs":[],"contactPoint":{"@type":"ContactPoint","contactType":"customer service","availableLanguage":["English","Urdu"]}},
    {"@type":"WebSite","@id":"https://mithooos.com/#website","url":"https://mithooos.com/","name":"Mithooos","publisher":{"@id":"https://mithooos.com/#org"},"potentialAction":{"@type":"SearchAction","target":{"@type":"EntryPoint","urlTemplate":"https://mithooos.com/shop?q={search_term_string}"},"query-input":"required name=search_term_string"}}
  ]}
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $basePath ?>/css/variables.css?v=2.1">
  <link rel="stylesheet" href="<?= $basePath ?>/css/main.css?v=2.1">
  <link rel="stylesheet" href="<?= $basePath ?>/css/components.css?v=2.1">
  <link rel="stylesheet" href="<?= $basePath ?>/css/pages.css?v=2.1">

  <style> [data-reveal]{opacity:0;transform:translateY(28px);transition:opacity .7s ease,transform .7s ease} [data-reveal].visible{opacity:1;transform:translateY(0)} </style>
  <link rel="stylesheet" href="<?= $basePath ?>/css/skeleton.css">
  <link rel="stylesheet" href="<?= $basePath ?>/css/responsive.css?v=2.2">
  <script>window.__APP_CONFIG__ = { apiBase: '/backend' };</script>
  <?php if (!empty($headExtras)) echo $headExtras; ?>
</head>
