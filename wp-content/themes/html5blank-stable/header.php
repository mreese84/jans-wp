<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
  <head>
    <meta charset="UTF-8">
    <!--Load FontAwesome-->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

    <link href="//www.google-analytics.com" rel="dns-prefetch">
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/favicon.ico" rel="shortcut icon">
        <link href="<?php echo get_template_directory_uri(); ?>/img/icons/touch.png" rel="apple-touch-icon-precomposed">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php bloginfo('description'); ?>">

    <?php wp_head(); ?>
    <script>
        // conditionizr.com
        // configure environment tests
        conditionizr.config({
            assets: '<?php echo get_template_directory_uri(); ?>',
            tests: {}
        });
        </script>
  </head>
  <body <?php body_class(); ?>>
    <!-- wrapper -->
    <div class="wrapper">
      <!-- header -->
      <header class="navigation" role="banner">
          <div class="navigation-wrapper">
            <!-- logo -->
            <a href="<?php echo home_url(); ?>" class="logo">
              <img src="<?php echo get_template_directory_uri(); ?>/img/logo-web.png" alt="Logo Image">
            </a>
            <!-- Mobile menu -->
            <a href="javascript:void(0)" class="navigation-menu-button" id="js-mobile-menu"><i class="fa fa-navicon"></i></a>
            <!-- nav -->
            <nav class="nav" role="navigation">
              <?php html5blank_nav(); ?>
            </nav>
          </div>
      </header>
      <!-- /header -->
