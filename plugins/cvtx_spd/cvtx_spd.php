<?php
/**
 * @package cvtx_spd
 * @version 0.1
 */

/*
Plugin Name: cvtx Agenda Plugin - SPD
Plugin URI: http://cvtx-project.org
Description: Erweiterung des Antragssystem-Plugins "cvtx" durch Funktionalitäten die auf die Anwendung durch die SPD zugeschnitten sind.
Author: Alexander Fecke & Max Löffler
Version: 0.1
Author URI: http://alexander-fecke.de
License: GPLv2 or later
*/

define('CVTX_SPD_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(CVTX_SPD_PLUGIN_DIR.'/cvtx_spd_admin.php');
require_once(CVTX_SPD_PLUGIN_DIR.'/cvtx_spd_latex.php');
require_once(CVTX_SPD_PLUGIN_DIR.'/cvtx_spd_widgets.php');
require_once(CVTX_SPD_PLUGIN_DIR.'/cvtx_spd_theme.php');

/**
  * Define additional fields
  */
$cvtx_spd_types = array(
    'cvtx_reader' => array(
        'cvtx_reader_type'
    ),
    'cvtx_antrag' => array(
        'cvtx_antrag_version_ak',
        'cvtx_antrag_ak_konsens',
        'cvtx_antrag_ak_recommendation',
        'cvtx_antrag_recipient',
        'cvtx_antrag_answer',
    ),
    'cvtx_aeantrag' => array(
        'cvtx_aeantrag_seite',
        'cvtx_aeantrag_version_ak',
        'cvtx_aeantrag_ak_konsens',
        'cvtx_aeantrag_ak_recommendation',
        'cvtx_aeantrag_ord',
        'cvtx_aeantrag_action',
        'cvtx_aeantrag_recipient',
        'cvtx_aeantrag_decision_expl',
        'cvtx_aeantrag_answer',
    ),
);

add_action('wp_insert_post', 'cvtx_spd_insert_post', 12, 2);
/**
 * Action inserts/updates posts
 */
function cvtx_spd_insert_post($post_id, $post = null) {
    global $cvtx_spd_types;

    if (in_array($post->post_type, array_keys($cvtx_spd_types)) && !($post != null && $post->pinged == '12345')) {
        if ($post->post_type == 'cvtx_aeantrag' && isset($_POST['cvtx_aeantrag_antrag']) && isset($_POST['cvtx_aeantrag_zeile'])) {
            // get top and antrag and validate data
            $top_id     = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_top', true);
            $top_ord    = get_post_meta($top_id, 'cvtx_top_ord', true);
            $antrag_ord = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_ord', true);
            $event_id   = get_post_meta($_POST['cvtx_aeantrag_antrag'], 'cvtx_antrag_event', false);
            $event_id   = (is_array($event_id) ? array_pop($event_id) : $event_id);
            $event_year = get_post_meta($event_id, 'cvtx_event_year', true);
            $event_nr   = get_post_meta($event_id, 'cvtx_event_ord', true);
            
            // get page nr
            if(isset($_POST['cvtx_aeantrag_seite'])) {
                $aeantrag_page = $_POST['cvtx_aeantrag_seite'];
            } else {
                $aeantrag_page = false;
            }

            // fetch main info on line (first few numbers)
            if (preg_match('/^([0-9]+)/', $_POST['cvtx_aeantrag_zeile'], $match1)) {
                $aeantrag_zeile = $match1[1];
            } else $aeantrag_zeile = 0;
            // look for vari-ending (some kind of -2 at the end of the line field)
            if (preg_match('/([0-9]+)$/', $_POST['cvtx_aeantrag_zeile'], $match2)
             && strlen($match2[1]) < strlen($_POST['cvtx_aeantrag_zeile'])) {
                $aeantrag_vari = $match2[1];
            } else $aeantrag_vari = false;
            
            // get globally sortable string
            $_POST['cvtx_sort'] = cvtx_get_sort('cvtx_aeantrag', $data = array('top' => $top_ord, 'subject' => $antrag_ord, 'page' => $aeantrag_page, 'zeile' => $aeantrag_zeile, 'vari' => $aeantrag_vari, 'year' => $event_year, 'event' => $event_nr);
        }
        foreach ($cvtx_spd_types[$post->post_type] as $key) {
            if (isset($_POST[$key])) {
                // save data
                $value = @$_POST[$key];
                if (empty($value)) {
                    delete_post_meta($post_id, $key);
                    continue;
                }
    
                // If value is a string it should be unique
                if (!is_array($value)) {
                    // Update meta
                    if (!update_post_meta($post_id, $key, $value)) {
                        // Or add the meta data
                        add_post_meta($post_id, $key, $value);
                    }
                } else {
                    // If passed along is an array, we should remove all previous data
                    delete_post_meta($post_id, $key);
                    
                    // Loop through the array adding new values to the post meta as different entries with the same name
                    foreach ($value as $entry)
                        add_post_meta($post_id, $key, $entry);
                }
            }
        }
    }
}

function cvtx_spd_map_reader_type($type) {
  $map = array(
    "Antragsmappe" => "amappe",
    "Beschlussprotokoll" => "bprotokoll",
    "Beschlussbuch" => "bbuch",
    "Erledigungsbroschüre" => "ebuch"
  );
  if(isset($map[$type])) {
    return $map[$type];
  }
  return "amappe";
}
?>