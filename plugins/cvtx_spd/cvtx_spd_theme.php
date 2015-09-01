<?php
add_action('cvtx_spd_theme_recipient', 'cvtx_spd_recipient_action', 10, 1);
function cvtx_spd_recipient_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);
  
    if(is_object($post)) {
        if($post->post_type == 'cvtx_antrag') {
            $rep = get_post_meta($post->ID, 'cvtx_antrag_recipient', array());
            if($rep && is_array($rep) && !empty($rep)) {
                foreach($rep as $rep1) {
                    echo('<p><strong>Der '.$rep1.' möge beschließen:</strong></p>');
                }
            }
            elseif($rep) {
                echo('<p><strong>Der '.get_post_meta($post->ID, 'cvtx_antrag_recipient', true).' möge beschließen:</strong></p>');
            }
        }
        elseif($post->post_type == 'cvtx_aeantrag') {
            echo('<p><strong>Der '.get_post_meta($post->ID, 'cvtx_aeantrag_recipient', true).' möge beschließen:</strong></p>');
        }
    }
}

add_action('cvtx_spd_theme_beschluss', 'cvtx_spd_beschluss_action', 10, 1);
function cvtx_spd_beschluss_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);
  
    if(is_object($post)) {
        /**
         * Wenn eine manuel eingetragene Beschlussversion existiert, geht diese vor
         */
        $expl = get_post_meta($post->ID, 'cvtx_antrag_decision_expl', true);
        $beschl = get_post_meta($post->ID, 'cvtx_antrag_decision', true);
        if($beschl) {
            if (is_plugin_active('html-purified/html-purified.php')) {
                global $cvtx_purifier, $cvtx_purifier_config;
                $beschl = $cvtx_purifier->purify($beschl, $cvtx_purifier_config);
            }
            $beschl = trim($beschl);
            if (!empty($beschl)) {
                // Convert line breaks to paragraphs
                $beschl = '<p>'.preg_replace('/[\r\n]+/', '<br/>', $beschl).'</p>';
          
                printf('%1$s', $beschl);
            }
            return;
        }
        $poll = get_post_meta($post->ID, 'cvtx_antrag_poll', true);
        $konsens = get_post_meta($post->ID, 'cvtx_antrag_ak_konsens', true);
        $recom = get_post_meta($post->ID, 'cvtx_antrag_ak_recommendation', true);
        /**
         * Die Version der AK wurde angenommen
         */
        if(($recom == 'Annahme in der Fassung der Antragskommission' || $recom == 'Annahme in der Fassung des Landesvorstandes' || $recom == 'Annahme in der Fassung der AK') && $poll == 'Annahme') {
            $content = get_post_meta($post->ID, 'cvtx_antrag_version_ak', true);
            if (is_plugin_active('html-purified/html-purified.php')) {
                global $cvtx_purifier, $cvtx_purifier_config;
                $content = $cvtx_purifier->purify($content, $cvtx_purifier_config);
            }
            $content = trim($content);
            if (!empty($content)) {
                // Convert line breaks to paragraphs
                $content = '<p>'.preg_replace('/[\r\n]+/', '<br/>', $content).'</p>';

                printf('%1$s', $content);
            }
            return;
        }
        /**
         * Alle anderen Fälle: ursprünglicher Text wird angezeigt
         */
        $content = $post->post_content;
        if (is_plugin_active('html-purified/html-purified.php')) {
            global $cvtx_purifier, $cvtx_purifier_config;
            $content = $cvtx_purifier->purify($content, $cvtx_purifier_config);
        }
        $content = trim($content);
        if (!empty($content)) {
            // Convert line breaks to paragraphs
            $content = '<p>'.preg_replace('/[\r\n]+/', '<br/>', $content).'</p>';
        
            printf('%1$s', $content);
        }
    }
}

add_action('cvtx_spd_theme_antrag_expl', 'cvtx_spd_antrag_expl_action', 10, 1);
function cvtx_spd_antrag_expl_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);

    if($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag') {
        $expl = get_post_meta($post->ID, $post->post_type.'_decision_expl', true);
        if($expl) {
            echo('<p><strong>'.$expl.'</strong></p>');
            return;
        }
        $konsens = get_post_meta($post->ID, $post->post_type.'_ak_konsens', true);
        $recom = get_post_meta($post->ID, $post->post_type.'_ak_recommendation', true);
        /**
         * Die urspruengliche Version wurde im Konsens angenommen
         */
        if($recom == 'Annahme' && $konsens == 'Konsens') {
            echo('<p><strong>'.$recom.'</strong></p>');
            return;
        }
        /**
         * Alle anderen Fälle: die Empfehlung der AK und die Version der AK werden ausgegeben
         */
        echo('<p><strong>'.$recom.'</strong></p>');
    }
}

add_action('cvtx_spd_theme_answer', 'cvtx_spd_answer_action', 10, 1);
function cvtx_spd_answer_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);
  
    if(is_object($post)) {
        $answer = get_post_meta($post->ID, $post->post_type.'_answer', true);
        if (is_plugin_active('html-purified/html-purified.php')) {
            global $cvtx_purifier, $cvtx_purifier_config;
            $answer = $cvtx_purifier->purify($answer, $cvtx_purifier_config);
        }
        $answer = trim($answer);
        $reps = wp_get_post_terms($post->ID, 'cvtx_tax_assign_to');
        if(!empty($reps) && $answer) {
            $recipients = '';
            for($i = 0; $i < count($reps); $i++) {
                $recipients .= $reps[$i]->name;
                if($i != count($reps)-1) $recipients .= ', ';
            }
            if (!empty($answer)) {
                // Convert line breaks to paragraphs
                $answer = '<p>'.preg_replace('/[\r\n]+/', '<br/>', $answer).'</p>';
          
                printf('%1$s', $answer);
            }
        }
    }
}

add_action('cvtx_spd_theme_assign_to', 'cvtx_spd_assign_to_action', 10, 1);
function cvtx_spd_assign_to_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);
  
    if(is_object($post)) {
        $reps = wp_get_post_terms($post->ID, 'cvtx_tax_assign_to');
        if(!empty($reps)) {
            $recipients = '';
            for($i = 0; $i < count($reps); $i++) {
                $recipients .= $reps[$i]->name;
                if($i != count($reps)-1) $recipients .= ', ';
            }
            echo('<h3>Überweisen an:</h3> <p><strong>'.$recipients.'</strong></p>');
        }
    }
}

add_action('cvtx_spd_theme_version_ak', 'cvtx_spd_version_ak_action', 10, 1);
function cvtx_spd_version_ak_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);
  
    if(is_object($post)) {
        $version_ak = get_post_meta($post->ID, $post->post_type.'_version_ak', true);
        if (is_plugin_active('html-purified/html-purified.php')) {
            global $cvtx_purifier, $cvtx_purifier_config;
            $version_ak = $cvtx_purifier->purify($version_ak, $cvtx_purifier_config);
        }
        $version_ak = trim($version_ak);
        // Anything left to print?
        if (!empty($version_ak)) {
            // Convert line breaks to paragraphs
            $version_ak = '<p>'.preg_replace('/[\r\n]+/', '<br/>', $version_ak).'</p>';
        
            printf('%1$s', $version_ak);
        }
    }
}

add_action('cvtx_spd_theme_ak_konsens', 'cvtx_spd_ak_konsens_action', 10, 1);
function cvtx_spd_ak_konsens_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);
  
    if(is_object($post)) {
        $ak_recommendation = get_post_meta($post->ID, $post->post_type.'_ak_recommendation', true);
        $ak_konsens = get_post_meta($post->ID, $post->post_type.'_ak_konsens', true);
        echo('<p><strong class="title-ak">Empfehlung der Antragskommission: </strong>'.$ak_recommendation.' '.spd_short_konsens($ak_konsens).'</p>');
    }
}

function spd_short_konsens($konsens) {
    $map = array('Konsens' => '<strong>(K)</strong>',
               'Kein Konsens' => '<strong>(Kein Konsens)</strong>');
    if(isset($map[$konsens]))
        return $map[$konsens];
    else
        return '';
}

add_action('cvtx_spd_theme_title_wo_short', 'cvtx_spd_title_wo_short_action', 10, 1);
function cvtx_spd_title_wo_short_action($post_id = false) {
    if(!(isset($post_id) || !$post_id)) global $post;
    else $post = get_post($post_id);
  
    if(is_object($post)) {
        $title = $post->post_title;
    
        echo('<h3>'.$title.'</h3>');
    }
}

add_action('cvtx_spd_theme_aeantrag_action', 'cvtx_spd_aeantrag_action', 10, 1);
function cvtx_spd_aeantrag_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    if (is_object($post) && $post->post_type == 'cvtx_aeantrag') {
        $page = get_post_meta($post->ID, 'cvtx_aeantrag_seite', true);
        $zeile = get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true);
        $action = get_post_meta($post->ID, 'cvtx_aeantrag_action', true);
        echo('<strong>Seite '.$page.', Zeile '.$zeile.($action ? ', '.$action : '').'</strong>');
    }
}

add_action('cvtx_spd_theme_aenderungsantraege_list', 'cvtx_spd_aenderungsantraege_list_action', 10, 1);
function cvtx_spd_aenderungsantraege_list_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    // specify wp_query for all aenderungsantraege to given ID
    global $wpdb;
    $ids = $wpdb->get_col($wpdb->prepare("
      SELECT DISTINCT p.ID
      FROM $wpdb->posts p
      INNER JOIN $wpdb->postmeta p2 ON
        (p2.post_id = p.ID and p2.meta_key = 'cvtx_aeantrag_antrag')
      LEFT JOIN $wpdb->postmeta cvtxsort ON
        (cvtxsort.post_id = p.ID and cvtxsort.meta_key = 'cvtx_sort')
      WHERE p2.meta_value = %d AND p.post_type = 'cvtx_aeantrag'
      ORDER BY cvtxsort.meta_value
    ", $post->ID));
    if (!empty($ids)): ?>
        <div id="ae_antraege" class="entry-content">
            <h3 class="top"><?php _e('Amendments', 'cvtx'); ?><?php if (isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) _e(' to ', 'cvtx').cvtx_get_short($post); ?></h3>
            <ul>
                <?php foreach($ids as $id): $post = get_post($id); ?>
                  <?php $poll = get_post_meta($post->ID, 'cvtx_aeantrag_poll', true); ?>
                  <?php if($poll_class = cvtx_map_poll($poll)) $class = ' class="'.$poll_class.'"'; else $class = false; ?>
                  <li<?php if($class) print $class; ?>>
                    <h4 class="title top"><?php the_title(); ?></h4>
                    <?php do_action('cvtx_theme_antragsteller',array('short' => false, 'post_id' => $id)); ?>
                    <?php do_action('cvtx_theme_recipient', $id); ?>
                    <?php do_action('cvtx_theme_aeantrag_action', $id); ?>
                    <?php
                    $content = $post->post_content;
                    $content = apply_filters('the_content', $content);
                    $content = str_replace(']]>', ']]&gt;', $content);
                    echo $content;
                    ?>
                    <?php do_action('cvtx_theme_grund', $id); ?>
        				    <?php do_action('cvtx_theme_ak_konsens', $id); ?>
          					<?php do_action('cvtx_theme_version_ak', $id); ?>
          					<?php do_action('cvtx_theme_poll', $id); ?>
                  </li>
                <?php endforeach; ?>
            </ul>
        </div>
   <?php endif; wp_reset_postdata();
}

remove_action('cvtx_theme_poll', 'cvtx_theme_poll_action', 10, 1);
add_action('cvtx_theme_poll', 'cvtx_poll_action', 10, 2);
function cvtx_poll_action($post_id = false, $naked = false) {
  if(!(isset($post_id) || !$post_id)) global $post;
  else $post = get_post($post_id);
  
  if(is_object($post)) {
    $poll = get_post_meta($post->ID, $post->post_type.'_poll', true);
    if($post->post_type == 'cvtx_antrag' && !$naked) echo('<h3>Beschluss</h3>');
    elseif($post->post_type == 'cvtx_antrag' && $naked) echo('<p><strong>Beschluss:</strong>');
    else if($post->post_type == 'cvtx_aeantrag') echo('<h4 class="title poll">Beschluss:</h4>');
    echo($poll);
  }
}

remove_action('cvtx_theme_aenderungsantraege', 'cvtx_aenderungsantraege_action', 10, 1);
add_action('cvtx_theme_aenderungsantraege', 'cvtx_spd_aenderungsantraege_action', 10, 1);
/**
 * themed output of all aenderungsantraege to given post or post_id
 * 
 * @param post_id Do you want a specific posts aenderungsantraege?
 *
 */
function cvtx_spd_aenderungsantraege_action($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    // specify wp_query for all aenderungsantraege to given ID
    $loop = new WP_Query(array('post_type'  => 'cvtx_aeantrag',
                               'meta_key'   => 'cvtx_sort',
                               'orderby'    => 'meta_value',
                               'order'      => 'ASC',
                               'nopaging'   => true,
                               'meta_query' => array(array('key'     => 'cvtx_aeantrag_antrag',
                                                           'value'   => $post->ID,
                                                           'compare' => '='))));
    if ($loop->have_posts()): ?>
        <div id="ae_antraege" class="entry-content">
            <h3><?php _e('Amendments', 'cvtx'); ?><?php if (isset($_GET['ae_antraege']) && $_GET['ae_antraege'] == 1) _e(' to ', 'cvtx').cvtx_get_short($post); ?></h3>
            <table cellpadding="3" cellspacing="0" valign="top" class="ae_antraege_table">
                <tr>
                    <th><strong><?php _e('Page', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Line', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Author(s)', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Text', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Explanation', 'cvtx'); ?></strong></th>
                    <th><strong><?php _e('Version AK', 'cvtx'); ?></strong></th>
                </tr>
                <?php 
                while ($loop->have_posts()): $loop->the_post(); ?>
                    <tr <?php if(cvtx_map_procedure(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true)) === 'd') echo('class="withdrawn"'); ?>>
                        <td class="seite"><strong><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_seite', true)); ?></strong></td>
                        <td class="zeile"><strong><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true)); ?></strong></td>
                        <td class="steller"><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_steller_short', true));?></td>
                        <td class="text"><?php the_content(); ?></td>
                        <td class="grund"><?php echo(get_post_meta($post->ID, 'cvtx_aeantrag_grund', true)); ?></td>
                        <td class="verfahren"><span class="flag <?php echo(cvtx_map_procedure(get_post_meta($post->ID, 'cvtx_aeantrag_verfahren', true))); ?>"></span><span class="procedure"><span class="arrow"></span><?php echo(get_post_meta($post->ID, $post->post_type.'_ak_recommendation', true).' '.short_konsens(get_post_meta($post->ID, $post->post_type.'_ak_konsens', true))); ?><?php $version_ak = get_post_meta($post->ID, $post->post_type.'_version_ak', true); if (is_plugin_active('html-purified/html-purified.php')) { global $cvtx_purifier, $cvtx_purifier_config; $version_ak = $cvtx_purifier->purify($version_ak, $cvtx_purifier_config); } $version_ak = trim($version_ak); ?><?php if($version_ak): echo('<p/>'.$version_ak); endif; ?></span></td>
                    </tr>
                <?php endwhile;?>
            </table>
        </div>
   <?php endif; wp_reset_postdata();
}

?>