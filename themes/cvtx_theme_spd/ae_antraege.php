<?php
/**
 * Template Name: Änderungsantragsübersicht
 *
 * Dieses Template stellt eine Übersicht über alle bereits
 * eingerichteten Anträge dar.
 *
 * @package WordPress
 * @subpackage cvtx_theme_spd
 */
?>

<!DOCTYPE html>
<html lang="<?php bloginfo('language'); ?>">

  <head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <title><?php wp_title(); ?> <?php bloginfo('name'); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/ae_antraege.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/ae_antraege_print.css" type="text/css" media="print" />
    <?php wp_head(); ?>
  </head>

  <body>

    <div id="header">
      <div id="verlauf">
        <p><a href="<?php bloginfo('url'); ?>"><< <?php echo __('Back to the page', 'cvtx_theme'); ?></a> <span class="right"><?php bloginfo('name'); ?></span></p>
      </div>
    </div>
    
    <?php

    $antraege       = (isset($_POST['antraege']) ? $_POST['antraege'] : false);
    $show_empty     = (isset($_POST['show_empty'])     && $_POST['show_empty']     ? true : false);
    $show_verfahren = (isset($_POST['show_verfahren']) && $_POST['show_verfahren'] ? true : false);
    $show_steller   = (isset($_POST['show_steller'])   && $_POST['show_steller']   ? true : false);
    
    if (is_array($antraege) || $show_empty || $show_verfahren || $show_steller) $hide = true;
    else $hide = false;

    // TOP-Query
    $loop = new WP_Query(array('post_type' => 'cvtx_top',
                               'orderby'   => 'meta_value',
                               'meta_key'  => 'cvtx_sort',
                               'order'     => 'ASC',
                               'nopaging'  => true,
                               'meta_query'=> array(array('key'   => 'cvtx_top_antraege',
                                                          'value' => 'off',
                                                          'compare' => '!='))));
    if($loop->have_posts()):?>
      <div id="liste">
        <div class="toggler"><a href="#"><?php if($hide) print __('Show filters','cvtx_theme'); else print __('Hide filters', 'cvtx_theme'); ?></a></div>
        <form method="post" id="filter" <?php if($hide) print 'style="display:none"'; ?> >
          <label for="tops"><?php print __('Agenda points and amendments', 'cvtx_theme'); ?></label>
          <select id="tops" style="width: 100%" multiple="multiple" size="20" name="antraege[]">
          <?php
          while ($loop->have_posts()):$loop->the_post(); $top_id = $post->ID;?>
            <optgroup label="<?php the_title(); ?>">
            <?php
            $loop2 = new WP_Query(array('post_type'  => 'cvtx_antrag',
                                        'orderby'    => 'meta_value',
                                        'meta_key'   => 'cvtx_sort',
                                        'order'      => 'ASC',
                                        'nopaging'   => true,
                                        'meta_query' => array(array('key'     => 'cvtx_antrag_top',
                                                                    'value'   => $top_id,
                                                                    'compare' => '='))));
            while ($loop2->have_posts()): $loop2->the_post(); ?>
              <option value="<?php print $post->ID; ?>" label="<?php the_title(); ?>" <?php if(isset($_POST['antraege']) && in_array($post->ID, $_POST['antraege'])) print 'selected="true"'; ?>>
                <?php the_title(); ?>
              </option>
            <?php endwhile;?>
            </optgroup>
          <?php endwhile;?>
          </select>
          <p>
            <input id="show_empty" name="show_empty" type="checkbox" <?php if($show_empty) print 'checked="true"'; ?> />
            <label for="show_empty"><?php print __('Show only resolutions with amendments', 'cvtx_theme'); ?></label>
            <input id="show_verfahren" name="show_verfahren" type="checkbox" <?php if($show_verfahren) print 'checked="true"'; ?> />
            <label for="show_verfahren"><?php print __('Show procedure', 'cvtx_theme'); ?></label>
            <input id="show_steller" name="show_steller" type="checkbox" <?php if($show_steller) print 'checked="true"'; ?> />
            <label for="show_steller"><?php print __('Show full authors field','cvtx_theme'); ?></label>
          </p>
          <input type="submit" value="Liste anzeigen" />
        </form>
      </div>
    <?php endif; ?>

    <?php if($antraege): ?>
      <div id="result">
      <?php foreach($antraege as $antrag_id):?>
        <?php $loop3 = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                                          'orderby'    => 'meta_value',
                                          'meta_key'   => 'cvtx_sort',
                                          'nopaging'   => true,
                                          'order'      => 'ASC',
                                          'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                                      'value'   => $antrag_id,
                                                                      'compare' => '=')))); ?>
        <?php if (!$show_empty || $loop3->have_posts()): ?>
          <?php $post = get_post($antrag_id); ?>
          <h3><?php the_title(); ?></h3>
          <?php if ($loop3->have_posts()): ?>
            <table>
              <tr>
                <th><?php print __('Title'); ?></th>
                <th><?php print __('Author(s)', 'cvtx_theme'); ?></th>
                <th><?php print __('Resolution', 'cvtx_theme'); ?></th>
                <?php if ($show_verfahren): ?><th><?php print __('Modifications', 'cvtx_theme'); ?></th><th><?php print __('Procedure', 'cvtx_theme'); ?></th><?php endif; ?>
              </tr>
              <tbody>
              <?php while ($loop3->have_posts()): $loop3->the_post();?>
                <tr>
                  <td><p><?php print the_title(); ?></p></td>
                  <td>
                    <p>
                      <?php if ($show_steller): print get_post_meta($post->ID, 'cvtx_aeantrag_steller', true); ?>
                      <?php else: print get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true); ?>
                      <?php endif; ?>
                    </p>
                  </td>
                  <td><?php the_content(); ?></td>
                  <?php if($show_verfahren):?>
                    <td>
                      <p>
                        <?php print get_post_meta($post->ID, 'cvtx_aeantrag_detail', true); ?>
                      </p>
                    </td>
                    <td class="<?php print get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true); ?>">
                      <p class="verfahren"><?php print get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true); ?></p>
                    </td>
                  <?php endif; ?>
                </tr>
              <?php endwhile; ?>
              </tbody>
            </table>
          <?php endif;?>
        <?php endif; ?>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/ae_antraege.js"></script>
    <?php wp_footer(); ?>
  
  </body>

</html>
