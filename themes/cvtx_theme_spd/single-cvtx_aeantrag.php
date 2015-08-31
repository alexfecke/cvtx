<?php
/**
 * Template für einzelne Änderungsanträge
 *
 * @package WordPress
 * @subpackage cvtx_theme_spd
 */
?>

<?php get_header(); ?>
	<div class="inner">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
			<div class="entry">
                <?php do_action('cvtx_theme_antragsteller',array('short' => false)); ?>
                <?php do_action('cvtx_spd_theme_recipient'); ?>
                <?php do_action('cvtx_spd_theme_aeantrag_action'); ?>
                <?php the_content(); ?>
                <?php do_action('cvtx_theme_grund'); ?>
                <?php do_action('cvtx_spd_theme_ak_konsens'); ?>
				<?php do_action('cvtx_spd_theme_version_ak'); ?>
				<?php do_action('cvtx_theme_poll'); ?>
			</div>
		</div>
	<?php endwhile; else: ?>
	    <p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
	<?php endif; ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>