<?php
/**
 * Template für einzelne Anträge
 *
 * @package WordPress
 * @subpackage cvtx_theme_spd
 */
?>

<?php get_header(); ?>
	<div class="inner">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	    <?php $decided = cvtx_spd_antrag_is_decided($post->ID); ?>
        <?php $finished = cvtx_spd_antrag_is_finished($post->ID); ?>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><?php the_title(); ?></h2>
			<div class="entry">
			    <div class="antrag spd-cvtx-entry">
					<?php do_action('cvtx_theme_antragsteller',array('short' => false)); ?>
					<?php do_action('cvtx_spd_theme_recipient'); ?>
					<?php do_action('cvtx_spd_theme_title_wo_short'); ?>
					<?php if(!$decided) the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx_theme') . '</p>'); ?>
					<?php if(!$decided) do_action('cvtx_theme_grund'); ?>
					<?php if($decided) do_action('cvtx_spd_theme_beschluss'); ?>
			    </div>
                <?php if(!$decided): ?>
                    <div class="ak-version spd-cvtx-entry">
                        <?php do_action('cvtx_spd_theme_ak_konsens'); ?>
                        <?php do_action('cvtx_spd_theme_version_ak'); ?>
			        </div>
                <?php endif; ?>
			    <div class="allgemein spd-cvtx-entry">
				    <?php if($decided): ?>
					    <?php do_action('cvtx_theme_poll'); ?>
					<?php endif; ?>
					<?php do_action('cvtx_theme_assign_to'); ?>
			    </div>
                <?php if($finished === true): ?>
                    <div class="ak-version spd-cvtx-entry">
                        <?php do_action('cvtx_spd_theme_answer'); ?>
			        </div>
                <?php endif; ?>
				<?php do_action('cvtx_theme_pdf'); ?>
			</div>
			</p>
			<?php do_action('cvtx_spd_theme_aenderungsantraege_list'); ?>
		</div>
	<?php endwhile; else: ?>
	    <p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
	<?php endif; ?>
	</div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>