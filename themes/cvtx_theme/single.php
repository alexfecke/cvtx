<?php
/**
 * Template für einzelne Beiträge
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */
?>

<?php get_header(); ?>
	<div class="inner">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx_theme') . '</p>'); ?>
			</div>
			<p class="postmetadata alt">
				<small>
				<?php printf(__('This %1$s was published on %2$s at %3$s.', 'cvtx_theme'),
                                get_post_type_object(get_post_type())->labels->singular_name,
                                get_the_time(__('l, j. F Y', 'cvtx_theme')),
                                get_the_time(),
                                get_the_category_list(', ')); ?>
				</small>
			</p>
					<?php comments_template( '', true ); ?>
		</div>
	<?php endwhile; else: ?>
	    <p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
	<?php endif; ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>