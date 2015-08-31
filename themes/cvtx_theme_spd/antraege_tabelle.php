<?php
/**
 * Template Name: Antragsübersichtstabelle
 *
 * Dieses Template stellt eine tabellarische Übersicht aller
 * eingerichteten Anträge, Änderungsanträge und TOPs dar.
 *
 * @package WordPress
 * @subpackage cvtx_theme_spd
 */
?>

<?php get_header(); ?>
  <div class="inner">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
      <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
        <h2><?php the_title(); ?></h2>
        <div class="entry">
          <?php the_content('<p class="serif">' . __('Read the rest of this entry &raquo;', 'cvtx_theme') . '</p>'); ?>
        </div>
      </div>
    <?php endwhile; endif; ?>
  
    <?php
    $post_bak = $post;
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $curr_evid = (get_query_var('cvtx_top_event')) ? get_query_var('cvtx_top_event') : -1;
    $posts_page = (get_query_var('posts_page')) ? get_query_var('posts_page') : 10;
    $sort_by = (get_query_var('sort_by')) ? get_query_var('sort_by') : 'cvtx_sort';
    $sort_by_dir = (get_query_var('sort_by_dir')) ? get_query_var('sort_by_dir') : 'DESC';
    $assigned_to = (get_query_var('assigned_to')) ? get_query_var('assigned_to') : false;
    $status = (get_query_var('status')) ? get_query_var('status') : false;
    $steller = (get_query_var('steller')) ? get_query_var('steller') : false;
    $loop = new Custom_WP_Query(array('post_type' => array('cvtx_antrag'),
                               'meta_key'  => $sort_by,
                               'orderby'   => 'meta_value',
                               'posts_per_page' => $posts_page,
                               'meta_type' => ($sort_by == 'cvtx_antrag_ord' || $sort_by == 'cvtx_antrag_event' ? 'NUMERIC' : 'CHAR'),
                               'meta_query' => array(($curr_evid != -1 ?
                                 array(
                                   'key' => 'cvtx_antrag_event',
                                   'value' => $curr_evid,
                                   'compare' => '=',
                                 ) : false),
                                 ($status ? array(
                                   'key' => 'cvtx_antrag_poll',
                                   'value' => $status,
                                   'compare' => '=',
                                 ) : false),
                                 ($steller ? array(
                                   'key' => 'cvtx_antrag_steller',
                                   'value' => $steller,
                                   'compare' => 'LIKE',
                                 ) : false),
                               ),
                               'tax_query' => ($assigned_to ? array(
                                 array(
                                   'taxonomy' => 'cvtx_tax_assign_to',
                                   'field' => 'slug',
                                   'terms' => $assigned_to
                                 )
                               ) : false),
                               'paged' => $paged,
                               'order' => $sort_by_dir));
    global $wp_query;
    // Put default query object in a temp variable
    $tmp_query = $wp_query;
    // Now wipe it out completely
    $wp_query = null;
    // Re-populate the global with our custom query
    $wp_query = $loop;
    ?>    
    <form>
      <div id="filter" class="filter">
        <h3>Filter</h3>
          <div class="form-item">
            <label for="status">Status</label>
            <select name="status" id="status">
              <?php $beschluss = array(__('Annahme', 'cvtx'), __('Ablehnung', 'cvtx'), __('Erledigt', 'cvtx'), __('Überweisung', 'cvtx')); ?>
              <option value="0">- Keine Auswahl -</option>
              <?php foreach($beschluss as $beschl): ?>
                <option value="<?php echo $beschl; ?>"<?php print ($status == $beschl ? ' selected="true"' : ''); ?>><?php echo $beschl; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-item">
            <label for="assigned_to">Überwiesen an</label>
            <select name="assigned_to" id="assigned_to">
              <?php $terms = get_terms( 'cvtx_tax_assign_to', $args ); ?>
              <option value="0">- Keine Auswahl -</option>
              <?php foreach ($terms as $term) : ?>
                <option value="<?php echo $term->slug; ?>"<?php print ($assigned_to == $term->slug ? ' selected="true"' : ''); ?>><?php echo $term->name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-item">
            <label for="steller">Antragstellerin/Antragsteller</label>
            <input name="steller" id="steller" type="text" value="<?php print $steller; ?>" />
          </div>
          <div class="form-item">
            <label for="cvtx_antrag_event">Veranstaltung</label>
            <?php echo cvtx_dropdown_events($selected = $curr_evid, $type = 'cvtx_top', $message = '', $multiple = false); ?>
          </div>
      </div>
      <div id="sort" class="filter">
        <?php
          $sortings = array('cvtx_sort' => '- Standard -', 'cvtx_antrag_ord' => 'Antragsnummer', 'cvtx_antrag_poll' => 'Status', 'cvtx_antrag_event' => 'Veranstaltung');  
        ?>
        <h3>Sortieren</h3>
          <div class="form-item">
            <label for="sort_by">Sortieren nach</label>
            <select name="sort_by" id="sort_by">
              <?php foreach($sortings as $sort_key => $sort_name): ?>
                <option value="<?php echo $sort_key; ?>"<?php echo ($sort_by == $sort_key ? ' selected="true"' : ''); ?>><?php echo $sort_name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-item">
            <label for="sort_by">Richtung</label>
            <?php $sorting_dirs = array('ASC' => 'Aufsteigend', 'DESC' => 'Absteigend'); ?>
            <select name="sort_by_dir" id="sort_by_dir">
              <?php foreach($sorting_dirs as $sort_key => $sort_name): ?>
                <option value="<?php echo $sort_key; ?>"<?php echo ($sort_by_dir == $sort_key ? ' selected="true"' : ''); ?>><?php echo $sort_name; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-item">
            <label for="posts_page">Beiträge pro Seite</label>
            <input type="number" id="posts_page" name="posts_page" value="<?php echo $posts_page; ?>"/>
          </div>
      </div>
      <div class="clear-block"></div>
      <div class="form-item submit">
        <input type="submit" value="Filtern &raquo;" />
      </div>
    </form>
    <div id="tabelle">
      <h3>Ergebnisse</h3>
      <?php if($loop->have_posts()): ?>
      <table id="antraege">
        <thead>
          <tr>
            <td class="kuerzel">Kürzel</td>
            <td class="status">Status</td>
            <td class="steller">Antragsteller</td>
            <td class="assigned">Überweisung</td>
            <td class="kategorie">Antragsbereich</td>
            <td class="titel">Titel</td>
            <td class="parteitag">Veranstaltung</td>
          </tr>
        </thead>
        <tbody>
        <?php while ($loop->have_posts()): $loop->the_post();?>
          <?php $event_id = get_post_meta(($post->post_type == 'cvtx_aeantrag' ? get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true): $post->ID), ($post->post_type == 'cvtx_aeantrag' ? 'cvtx_antrag' : $post->post_type).'_event', true); ?>
          <tr>
            <td><a href="<?php echo get_permalink($post->ID); ?>"><?php print cvtx_get_short($post); ?></a></td>
            <td><?php print get_post_meta($post->ID, $post->post_type.'_poll', true); ?></td>
            <td><?php print get_post_meta($post->ID, 'cvtx_antrag_steller', true); ?></td>
            <td><?php $reps = wp_get_post_terms($post->ID, 'cvtx_tax_assign_to');
                      if(!empty($reps)) {
                      $recipients = '';
                      for($i = 0; $i < count($reps); $i++) {
                        $recipients .= $reps[$i]->name;
                        if($i != count($reps)-1) $recipients .= ', ';
                      }
                      echo $recipients;
                      }
                ?>
            <td><?php print ($post->post_type == 'cvtx_antrag' ? get_the_title(get_post_meta($post->ID, 'cvtx_antrag_top', true)) : ''); ?></td>
            <td>
              <strong><?php print ($post->post_type != 'cvtx_aeantrag' ? $post->post_title : ''); ?></strong><br/>
              <?php if (function_exists('cvtx_get_file') && $file = cvtx_get_file($post, 'pdf')) echo('<a href="'.$file.'">Download (pdf)</a>'); ?>
            </td>
            <td><a href="<?php echo get_permalink($event_id); ?>"><?php print get_the_title($event_id); ?></a></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
      <?php else: ?>
      <p>Keine Ergebnisse gefunden.</p>
      <?php endif; ?>
    </div>

    <?php wp_reset_postdata(); $post = $post_bak; ?>

    <?php if($loop->max_num_pages > 1): ?>
    <div id="tabelle-nav">
      <?php echo paginate_links($args = array(
      	'format'             => '/page/%#%',
      	'total'              => $loop->max_num_pages,
      	'current'            => $paged,
      	'show_all'           => True,
      	'end_size'           => 1,
      	'mid_size'           => 2,
      	'prev_next'          => True,
      	'prev_text'          => __('<'),
      	'next_text'          => __('>'),
      	'type'               => 'list',
      	'add_args'           => array(
        	 'cvtx_top_event' => $curr_evid,
        	 'posts_page'     => $posts_page,
        	 'sort_by'        => $sort_by,
        	 'sort_by_dir'    => $sort_by_dir,
        	 'assigned_to'    => $assigned_to,
        	 'status'         => $status,
        	 'steller'        => $steller,
      	),
      	'add_fragment'       => '',
      	'before_page_number' => '',
      	'after_page_number'  => ''
      )); ?>
    </div>
    <?php endif; ?>
  
    <?php // Restore original query object
    $wp_query = null;
    $wp_query = $tmp_query;
    ?>
  </div>
  </div>
<?php get_footer(); ?>