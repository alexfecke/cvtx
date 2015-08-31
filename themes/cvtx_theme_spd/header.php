<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php wp_title(); ?> <?php bloginfo( 'name' ); ?></title>
<link rel="icon" href="favicon.ico" type="image/x-icon" /> 

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" href="<?php bloginfo( 'stylesheet_url' ); ?>" type="text/css" media="screen" />
<?php if(is_page_template('antraege_tabelle.php')) :?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_directory'); ?>/antraege_tabelle.css" media="screen" />
<?php endif;?>
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php if ( is_singular() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
<noscript>
<style type="text/css">
nav ul li:hover ul {
	display: block;
}
</style>
</noscript>
</head>


<body>
<div id="overlay"></div>
<header>
	<div class="wrapper">
		<div id="headerimg"><h1><?php bloginfo('name'); ?></h1>
	    	<div class="description">
    			<?php bloginfo('description'); ?>
     		</div>
     	</div>
		<a href="<?php echo get_option('home'); ?>"><div id="spd"></div></a>
    <nav><?php wp_nav_menu(array('theme_location' => 'header-menu', 'walker' => (has_nav_menu('header-menu') ? new cvtx_walker() : ''))); ?></nav>
	</div>
</header>
<div id="c_wrap" class="wrapper">
	<div id="content">