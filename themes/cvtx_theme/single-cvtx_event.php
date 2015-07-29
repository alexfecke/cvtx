<?php
/**
 * Template für einzelne Veranstaltungen
 *
 * @package WordPress
 * @subpackage cvtx_theme
 */
?>
<?php get_header(); ?>
	<div class="inner">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php global $post; ?>
		<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
			<h2><?php echo $post->post_title; ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx_theme') . '</p>'); ?>
			</div>
		</div>
	<?php endwhile; else: ?>
	    <p><?php _e('There are no posts matching your search criteria. Sorry!', 'cvtx_theme'); ?></p>
	<?php endif; ?>
    <?php
    $event_id = $post->ID;
    $post_bak = $post;
    // Get all TOPs
    global $wpdb;
    $tids = $wpdb->get_col($wpdb->prepare("
      SELECT DISTINCT p.ID
      FROM $wpdb->posts p
      INNER JOIN $wpdb->postmeta p2 ON
        (p2.post_id = p.ID and p2.meta_key = 'cvtx_top_event')
      LEFT JOIN $wpdb->postmeta cvtxsort ON
        (cvtxsort.post_id = p.ID and cvtxsort.meta_key = 'cvtx_sort')
      WHERE p2.meta_value IN (%d, '-1')
      ORDER BY cvtxsort.meta_value ASC
    ", $event_id));
    if (!empty($tids)): ?>
      <ul id="antraege">
        <li class="top overview"><h3><?php print __('Overview','cvtx_theme'); ?></h3>
          <ul>
            <?php foreach($tids as $tid):?>
              <li class="antrag">
                <a href="#<?php print get_post_meta($tid, 'cvtx_top_short', true);?>"><?php echo get_the_title($tid); ?></a>
              </li>
            <?php endforeach; ?>
          </ul>
        </li><div class="tester"></div>
        
    <?php
    if(is_array($tids)) {
      $tids_arr = implode(',', $tids);
    }
    else {
      $tids_arr = $tids;
    }
    $aids = $wpdb->get_results($wpdb->prepare("
      SELECT DISTINCT p.ID, pm1.meta_value AS top, p.post_title
      FROM $wpdb->posts AS p
      INNER JOIN $wpdb->postmeta AS pm1 ON (pm1.post_id = p.ID and pm1.meta_key = 'cvtx_antrag_top')
      INNER JOIN $wpdb->postmeta AS pm2 ON (pm2.post_id = p.ID and pm2.meta_key = 'cvtx_antrag_event')
      LEFT JOIN $wpdb->postmeta AS cvtxsort ON (cvtxsort.post_id = p.ID and cvtxsort.meta_key = 'cvtx_sort')
      WHERE p.post_type = 'cvtx_antrag'  AND p.post_status = 'publish'
        AND pm1.meta_value IN ( {$tids_arr} )
        AND pm2.meta_value = %d
      ORDER BY cvtxsort.meta_value ASC
    ", array($event_id)));
    
    $new_aids = array();
    foreach($aids as $aid) {
      $new_aids[$aid->top][] = $aid->ID;
    }
    
    if(is_array($aids)) {
      $a_ids = array();
      foreach($aids as $aid) {
        $a_ids[] = $aid->ID;
      }
      $aids_arr = implode(',', $a_ids);
    }
    else {
      $aids_arr = $aids;
    }
    
    $aeids = $wpdb->get_results("
      SELECT DISTINCT p.ID, pm1.meta_value AS antrag
      FROM $wpdb->posts AS p
      INNER JOIN $wpdb->postmeta AS pm1 ON (pm1.post_id = p.ID and pm1.meta_key = 'cvtx_aeantrag_antrag')
      LEFT JOIN $wpdb->postmeta AS cvtxsort
        ON cvtxsort.post_id = p.ID
      WHERE p.post_type = 'cvtx_aeantrag' AND p.post_status = 'publish'
        AND pm1.meta_value IN ( {$aids_arr} )
      ORDER BY cvtxsort.meta_value ASC
    ");
    
    $new_aeaids = array();
    foreach($aeids as $aeid) {
      $new_aeaids[$aeid->antrag][] = $aeid->ID;
    }
    
    // show all tops
    foreach($tids as $tid): $top_id = $tid;
    ?>
      <li class="top" id="<?php print get_post_meta($tid,'cvtx_top_short',true);?>"><h3><?php echo get_the_title($tid); ?></h3>
      <div class="top_info" id="<?php print get_post_meta($tid,'cvtx_top_short',true);?>_info">
       <?php get_the_content($tid); ?>
      </div>
      <?php
      if(isset($new_aids[$top_id]) && !empty($new_aids[$top_id])):
        print '<ul>';
        foreach ($new_aids[$top_id] as $aid): $post = get_post($aid); setup_postdata($post); ?>
          <li class="antrag"><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
          <span class="steller">
            <strong><?php print __('Author(s)', 'cvtx_theme'); ?>:</strong>
            <?php print get_post_meta($post->ID,'cvtx_antrag_steller_short',true);?>
          </span>
          <ul class="options">
            <li><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) echo('<a href="'.$file.'">Download (pdf)</a>'); else echo('Kein PDF erstellt.'); ?></li>
            <?php $antrag_id = $post->ID; ?>
            <?php if(!empty($new_aeaids[$antrag_id])): ?>
              <li><a href="<?php echo get_the_permalink($antrag_id); ?>" rel="extern" class="ae_antraege_overview" meta-id="<?php print $post->ID; ?>"><?php print __('Amendment overview', 'cvtx_theme'); ?></a></li>
            <?php endif;?>
          </ul><div id="result-<?php print $post->ID; ?>" class="ae_antraege_result"></div>
          <?php if(!empty($new_aeaids[$antrag_id])): ?>
          <ul class="ae_antraege">
            <h4><?php print __('Amendments', 'cvtx_theme'); ?></h4>
          <?php
            foreach($new_aeaids[$antrag_id] as $aeid): ?>
              <li><span><?php echo get_the_title($aeid); ?></span> (<?php print __('Author(s)', 'cvtx_theme'); ?>: <em><?php print get_post_meta($aeid,'cvtx_aeantrag_steller_short',true);?></em>)</li>
            <?php endforeach;?>
          </ul>
          <?php endif;?>
          <div class="clear-block"></div></li>
        <?php endforeach;?>
        <?php print '</ul>';?>
      <?php endif; ?>

      <?php
      // query top-content
      $loop4 = new WP_Query(array('post_type'  => 'cvtx_application',
                                  'meta_key'   => 'cvtx_sort',
                                  'orderby'    => 'meta_value',
                                  'nopaging'   => true,
                                  'order'      => 'ASC',
                                  'meta_query' => array(array('key'     => 'cvtx_application_top',
                                                              'value'   => $top_id,
                                                              'compare' => '='))));
      if ($loop4->have_posts()):
      ?>
       <ul>
        <?php
        while ($loop4->have_posts()): $loop4->the_post(); ?>
         <li class="application">
          <!--<h4><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) the_title('<a href="'.$file.'">', ' (pdf)</a>'); else the_title(); ?></h4>-->
          <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
            <ul class="options">
              <li><?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) echo('<a href="'.$file.'">Download (pdf)</a>'); else echo('Kein PDF erstellt.'); ?></li>
            </ul>
          <div class="clear-block"></div>
         </li>
        <?php endwhile;?>
       </ul>
      <?php endif; ?>
      
      </li>
    <?php endforeach;?>
    </ul>
    <?php endif; ?>
    <?php wp_reset_postdata(); $post = $post_bak; ?>
		</div>
	</div>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>