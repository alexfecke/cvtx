<?php
if (is_admin()) add_action('add_meta_boxes', 'cvtx_spd_add_meta_boxes');
function cvtx_spd_add_meta_boxes() {
    // Reader
    add_meta_box('cvtx_reader_type', __('Type', 'cvtx'),
                 'cvtx_spd_reader_type', 'cvtx_reader', 'side', 'high');
                 
    // Proposals
    add_meta_box('cvtx_antrag_recipient', __('Adressat', 'cvtx'),
                 'cvtx_spd_recipient', 'cvtx_antrag', 'normal', 'high');
    add_meta_box('cvtx_antrag_ak', __('Antragskommission', 'cvtx'),
                 'cvtx_spd_metabox_ak', 'cvtx_antrag', 'normal', 'low');
    add_meta_box('cvtx_antrag_decision', __('Beschlussversion', 'cvtx'),
                 'cvtx_spd_antrag_decision', 'cvtx_antrag', 'normal', 'low');
    add_meta_box('cvtx_antrag_answer', __('Stellungnahme', 'cvtx'),
                 'cvtx_spd_answer_box', 'cvtx_antrag', 'normal', 'low');

    // Amendments
    add_meta_box('cvtx_aeantrag_verfahren', __('Procedure', 'cvtx'),
                 'cvtx_aeantrag_verfahren', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_ak', __('Antragskommission', 'cvtx'),
                 'cvtx_metabox_ak', 'cvtx_aeantrag', 'normal', 'default');
    add_meta_box('cvtx_aeantrag_recipient', __('Adressat', 'cvtx'),
                 'cvtx_recipient', 'cvtx_aeantrag', 'normal', 'high');
    add_meta_box('cvtx_aeantrag_decision', __('Beschluss', 'cvtx'),
                 'cvtx_aeantrag_decision', 'cvtx_aeantrag', 'normal', 'default');
    add_meta_box('cvtx_aeantrag_answer', __('Stellungnahme', 'cvtx'),
                 'cvtx_answer_box', 'cvtx_aeantrag', 'normal', 'default');
}

function cvtx_spd_reader_type() {
    global $post;
    echo('<select name="'.$post->post_type.'_type" id="'.$post->post_type.'_type">');
    $options = array(__('Antragsbuch', 'cvtx'), __('Beschlussprotokoll', 'cvtx'), __('Beschlussbuch', 'cvtx'), __('Erledigungsbroschüre', 'cvtx'));
    foreach ($options as $option) {
        echo('<option'.($option == get_post_meta($post->ID, $post->post_type.'_type', true) ? ' selected="selected"' : '').'>'.$option.'</option>');
    }
    echo('</select> ');
}

function cvtx_spd_metabox_ak() {
    global $post;
    echo('<p><label for="'.$post->post_type.'_ak_recommendation">'.__('Empfehlung Antragskommission', 'cvtx').':</label><br/>');
    echo('<input type="text" size="60" id="'.$post->post_type.'_ak_recommendation" name="'.$post->post_type.'_ak_recommendation" value="'.get_post_meta($post->ID, $post->post_type.'_ak_recommendation', true).'" /> ');
    echo('<select name="'.$post->post_type.'_ak_konsens" id="'.$post->post_type.'_ak_konsens"><option></option>');
    $options = array(__('Konsens', 'cvtx'), __('Kein Konsens', 'cvtx'));
    foreach ($options as $option) {
        echo('<option'.($option == get_post_meta($post->ID, $post->post_type.'_ak_konsens', true) ? ' selected="selected"' : '').'>'.$option.'</option>');
    }
    echo('</select> ');
    echo('</p>');
    if (is_plugin_active('html-purified/html-purified.php')) {
      wp_editor(get_post_meta($post->ID, $post->post_type.'_version_ak', true), $post->post_type.'_version_ak_admin', 
        array('media_buttons' => false,
              'textarea_name' => $post->post_type.'_version_ak',
              'tinymce'       => cvtx_tinymce_settings(),
              'quicktags'     => false,
              'teeny'         => false));
    } else {
        echo('<textarea style="width: 100%" for="'.$post->post_type.'_version_ak" name="'.$post->post_type.'_version_ak">'.get_post_meta($post->ID, $post->post_type.'_version_ak', true).'</textarea>');
    }
}

// Beschlussversion
function cvtx_spd_antrag_decision() {
    global $post;
    echo('<p><label for="'.$post->post_type.'_decision_expl">'.__('Beschluss', 'cvtx').':</label><br/>');
    echo('<input type="text" size="60" id="'.$post->post_type.'_decision_expl" name="'.$post->post_type.'_decision_expl" value="'.get_post_meta($post->ID, $post->post_type.'_decision_expl', true).'" /></p>');
    echo('<label for="'.$post->post_type.'_decision">'.__('Text des Beschlusses', 'cvtx').':</label><br/>');
    if (is_plugin_active('html-purified/html-purified.php')) {
      wp_editor(get_post_meta($post->ID, 'cvtx_antrag_decision', true), 'cvtx_antrag_decision_admin', 
        array('media_buttons' => false,
              'textarea_name' => 'cvtx_antrag_decision',
              'tinymce'       => cvtx_tinymce_settings(),
              'quicktags'     => false,
              'teeny'         => false));
    } else {
        echo('<textarea style="width: 100%" for="cvtx_antrag_decision" name="cvtx_antrag_decision">'.get_post_meta($post->ID, 'cvtx_antrag_decision', true).'</textarea>');
    }
}

function cvtx_spd_aeantrag_decision() {
    global $post;
    echo('<p><label for="'.$post->post_type.'_decision_expl">'.__('Beschluss', 'cvtx').':</label><br/>');
    echo('<input type="text" size="60" id="'.$post->post_type.'_decision_expl" name="'.$post->post_type.'_decision_expl" value="'.get_post_meta($post->ID, $post->post_type.'_decision_expl', true).'" /></p>');
}

function cvtx_spd_antrag_is_decided($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    
    if($post->post_type != 'cvtx_antrag')
      return false;
      
    $decision = get_post_meta($post->ID, 'cvtx_antrag_decision', true);
    $poll = trim(get_post_meta($post->ID, 'cvtx_antrag_poll', true));
    if($decision || ($poll && $poll != 'Nicht abgestimmt')) {
      return true;
    }
    return false;
}

function cvtx_spd_antrag_is_finished($post_id = false) {
    if (!isset($post_id) || !$post_id) global $post;
    else $post = get_post($post_id);
    
    if($post->post_type != 'cvtx_antrag')
      return false;
      
    $answer = get_post_meta($post->ID, 'cvtx_antrag_answer', true);
    if($answer) {
      return true;
    }
    return false;
}

// Stellungnahme
function cvtx_spd_answer_box() {
    global $post;
    if (is_plugin_active('html-purified/html-purified.php')) {
      wp_editor(get_post_meta($post->ID, $post->post_type.'_answer', true), $post->post_type.'_answer_admin', 
        array('media_buttons' => false,
              'textarea_name' => $post->post_type.'_answer',
              'tinymce'       => cvtx_tinymce_settings(),
              'quicktags'     => false,
              'teeny'         => false));
    } else {
        echo('<textarea style="width: 100%" for="'.$post->post_type.'_answer" name="'.$post->post_type.'_answer">'.get_post_meta($post->ID, $post->post_type.'_answer', true).'</textarea>');
    }
}

// Metainformationen (Ä-Antragsnummer / Zeile, Antrag)
function cvtx_spd_aeantrag_meta() {
    global $post;
    $antrag_id = get_post_meta($post->ID, 'cvtx_aeantrag_antrag', true);

    echo('<label for="cvtx_aeantrag_antrag_select">'.__('Resolution', 'cvtx').':</label><br />');
    echo(cvtx_dropdown_antraege($antrag_id, __('No agenda created', 'cvtx').'.'));
    echo('<br />');
    echo('<label for="cvtx_aeantrag_ord">'.__('Nr', 'cvtx').':</label><br />');
    echo('<input name="cvtx_aeantrag_ord" id="cvtx_aeantrag_ord_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_ord', true).'" />');
    echo('<br />');
    echo('<label for="cvtx_aeantrag_action_field">'.__('Action', 'cvtx').':</label><br />');
    echo('<select name="cvtx_aeantrag_action" id="cvtx_aeantrag_action"><option>Ändern</option>');
    $options = array(__('Einfügen', 'cvtx'), __('Ergänzen', 'cvtx'), __('Streichen', 'cvtx'));
    foreach ($options as $option) {
        echo('<option'.($option == get_post_meta($post->ID, 'cvtx_aeantrag_action', true) ? ' selected="selected"' : '').'>'.$option.'</option>');
    }
    echo('</select> ');
    echo('<br />');
    echo('<label for="cvtx_aeantrag_zeile_field">'.__('Line', 'cvtx').':</label><br />');
    echo('<input name="cvtx_aeantrag_zeile" id="cvtx_aeantrag_zeile_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true).'" />');
    echo('<br />');
    echo('<label for="cvtx_aeantrag_seite_field">'.__('Page', 'cvtx').':</label><br />');
    echo('<input name="cvtx_aeantrag_seite" id="cvtx_aeantrag_seite_field" type="text" value="'.get_post_meta($post->ID, 'cvtx_aeantrag_seite', true).'" />');
    echo('<p id="admin_message" class="error">');
    echo('<span id="unique_error_cvtx_aeantrag_ord" class="cvtx_unique_error">'.__('There is another amendment with this number.', 'cvtx').'</span> ');
    echo('<span id="unique_error_cvtx_aeantrag_zeile" class="cvtx_unique_error">'.__('There is another amendment concering this line/page.', 'cvtx').'</span> ');
    echo('<span id="empty_error_cvtx_aeantrag_zeile" class="cvtx_empty_error">'.__('Please insert line.', 'cvtx').'</span> ');
    echo('</p>');
}

// Adressat
function cvtx_spd_recipient() {
    global $post;
    echo('<select name="'.$post->post_type.'_recipient[]" id="'.$post->post_type.'_recipient" multiple>');
    $cvtx_options = get_option('cvtx_spd_options');
    if(isset($cvtx_options['cvtx_antrag_recipients_setting']) && is_array($cvtx_options['cvtx_antrag_recipients_setting'])) {
      $options = $cvtx_options['cvtx_antrag_recipients_setting'];
    } else {
      $options = array(__('Landesparteitag', 'cvtx'), __('Bundesparteitag', 'cvtx'), __('Parteikonvent', 'cvtx'));
    }
    foreach ($options as $option) {
        echo('<option'.(in_array(trim($option), get_post_meta($post->ID, $post->post_type.'_recipient', array())) ? ' selected="selected"' : '').'>'.$option.'</option>');
    }
    echo('</select> ');
}

if (is_admin()) add_filter('manage_edit-cvtx_spd_reader_columns', 'cvtx_spd_reader_columns');
function cvtx_spd_reader_columns($columns) {
    $columns['cvtx_reader_type'] = __('Type', 'cvtx');
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_spd_antrag_columns', 'cvtx_spd_antrag_columns');
function cvtx_spd_antrag_columns($columns) {
    $columns['cvtx_antrag_ak_konsens'] = __('Konsens', 'cvtx');
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_spd_antrag_sortable_columns', 'cvtx_spd_register_sortable_antrag');
function cvtx_spd_register_sortable_antrag($columns) {
    $columns['cvtx_antrag_ak_konsens'] = 'cvtx_antrag_ak_konsens';
    return $columns;
}

if (is_admin()) add_filter('manage_edit-cvtx_spd_aeantrag_columns', 'cvtx_spd_aeantrag_columns');
function cvtx_spd_aeantrag_columns($columns) {
    $columns['cvtx_aeantrag_ak_konsens'] = __('Konsens', 'cvtx');
    $columns['cvtx_aeantrag_poll'] = __('Poll', 'cvtx');
    unset($columns['cvtx_aeantrag_verfahren']);
    return $columns;
}

// Register the column as sortable
if (is_admin()) add_filter('manage_edit-cvtx_spd_aeantrag_sortable_columns', 'cvtx_spd_register_sortable_aeantrag');
function cvtx_spd_register_sortable_aeantrag($columns) {
    $columns['cvtx_aeantrag_ak_konsens'] = 'cvtx_aeantrag_ak_konsens';
    return $columns;
}

if (is_admin()) add_action('manage_posts_custom_column', 'cvtx_spd_format_lists');
function cvtx_format_lists($column) {
    global $post;
    switch ($column) {
        case 'cvtx_reader_type':
            echo(get_post_meta($post->ID, 'cvtx_reader_type', true));
            break;
        case "cvtx_antrag_ak_konsens":
            echo(short_konsens(get_post_meta($post->ID, 'cvtx_antrag_ak_konsens', true)));
            break;
        case "cvtx_aeantrag_ak_konsens":
            echo(short_konsens(get_post_meta($post->ID, 'cvtx_aeantrag_ak_konsens', true)));
            break;
    }
}

/**
 * Add custom filter for Konsens-Verfahrensvorschläge to the resolution list 
 */
add_action('restrict_manage_posts', 'cvtx_spd_admin_posts_filter_restrict_manage_posts' );
/**
 * First create the dropdown
 * make sure to change POST_TYPE to the name of your custom post type
 * 
 * @author Ohad Raz
 * 
 * @return void
 */
function cvtx_spd_admin_posts_filter_restrict_manage_posts(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if ('cvtx_antrag' == $type || 'cvtx_aeantrag' == $type){
        //change this to the list of values you want to show
        //in 'label' => 'value' format
        $values = array(
            'Konsens' => 'Konsens', 
            'Kein Konsens' => 'Kein Konsens',
            'Vertagt' => 'Vertagt',
        );
        ?>
        <select name="cvtx_filter_antraege_by_konsens">
        <option value=""><?php _e('Filter by Konsens', 'cvtx'); ?></option>
        <?php
            $current_v = isset($_GET['cvtx_filter_antraege_by_konsens'])? $_GET['cvtx_filter_antraege_by_konsens']:'';
            foreach ($values as $label => $value) {
                printf(
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
    }
}

/**
 * Add custom filter for version of AK to the resolution list 
 */
add_action('restrict_manage_posts', 'cvtx_spd_admin_posts_filter_ak_recommendation' );
/**
 * First create the dropdown
 * make sure to change POST_TYPE to the name of your custom post type
 * 
 * @author Ohad Raz
 * 
 * @return void
 */
function cvtx_spd_admin_posts_filter_ak_recommendation(){
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if ('cvtx_antrag' == $type || 'cvtx_aeantrag' == $type){
        ?>
        <input type="text" size="20" id="cvtx_filter_ak_recommendation" name="cvtx_filter_ak_recommendation" placeholder="Empfehlung AK" value="<?php echo isset($_GET['cvtx_filter_ak_recommendation'])? $_GET['cvtx_filter_ak_recommendation']: ''; ?>" />
        <?php
    }
}

add_filter( 'parse_query', 'cvtx_spd_posts_filter' );
/**
 * if submitted filter by post meta
 * 
 * make sure to change META_KEY to the actual meta key
 * and POST_TYPE to the name of your custom post type
 * @author Ohad Raz
 * @param  (wp_query object) $query
 * 
 * @return Void
 */
function cvtx_spd_posts_filter( $query ){
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }
    if (('cvtx_antrag' == $type || 'cvtx_aeantrag' == $type) && is_admin() && $pagenow=='edit.php' && isset($_GET['cvtx_filter_antraege_by_konsens']) && $_GET['cvtx_filter_antraege_by_konsens'] != '') {
        $query->query_vars['meta_query'][] = array('key' => $type.'_ak_konsens',
                                                   'value' => $_GET['cvtx_filter_antraege_by_konsens'],
                                                   'compare' => '=');
    }
    if (('cvtx_antrag' == $type || 'cvtx_aeantrag' == $type) && is_admin() && $pagenow=='edit.php' && isset($_GET['cvtx_filter_ak_recommendation']) && $_GET['cvtx_filter_ak_recommendation'] != '') {
        $query->query_vars['meta_query'][] = array('key' => $type.'_ak_recommendation',
                                                   'value' => $_GET['cvtx_filter_ak_recommendation'],
                                                   'compare' => 'LIKE');
    }
}

add_action('admin_menu', 'cvtx_spd_admin_page');
function cvtx_spd_admin_page() {
    add_options_page('cvtx SPD Page', __('cvtx Agenda Plugin SPD', 'cvtx_spd'), 'manage_options', 'cvtx_spd_config', 'cvtx_spd_options_page');
}

function cvtx_options_page() {
    echo('<div>');
    echo('<h2>'.__('cvtx Agenda Plugin SPD','cvtx').'</h2>');
    echo('<form action="options.php" method="post">');
    settings_fields('cvtx_spd_options');
    do_settings_sections('cvtx_spd_config');
    print '<input name="Submit" type="submit" value="'.esc_attr(__('Save Changes')).'" />';
    print '</form></div>';
}

add_action('admin_init', 'cvtx_spd_admin_init');
function cvtx_spd_admin_init(){
    register_setting('cvtx_spd_options', 'cvtx_spd_options', 'cvtx_spd_options_validate');
    add_settings_field('cvtx_spd_pdf_columns', __('Zahl der Spalten im PDF', 'cvtx'), 'cvtx_spd_pdf_columns', 'cvtx_spd_config', 'cvtx_spd_main');
    add_settings_field('cvtx_spd_antrag_recipients_setting', __('Adressaten für Anträge', 'cvtx'), 'cvtx_spd_antrag_recipients_setting', 'cvtx_spd_config', 'cvtx_spd_main');
}

function cvtx_spd_options_validate($input) {
    $newinput = $input;
    $newinput['cvtx_antrag_recipients_setting'] = explode("\n", $input['cvtx_antrag_recipients_setting']);
    return $newinput;
}


function cvtx_spd_create_options() {
    $cvtx_spd_options = array(
        'cvtx_antrag_recipients_setting' => array(
          "Landesparteitag",
          "Bundesparteitag",
          "Parteikonvent"
        ),
    );
    if(!get_option('cvtx_spd_options')) {
        // check for older version of options
        foreach($cvtx_spd_options as $key => $value) {
            if($value = get_option($key)) {
                $cvtx_spd_options[$key] = $value;
                delete_option($key);
            }
        }
        add_option('cvtx_spd_options', $cvtx_spd_options);
    }
    else {
        $old_op = get_option('cvtx_spd_options');
        $cvtx_spd_options = wp_parse_args($old_op, $cvtx_spd_options);
        update_option('cvtx_spd_options', $cvtx_spd_options);
    }
}

function cvtx_spd_restore_options() {
    delete_option('cvtx_spd_options');
    cvtx_spd_create_options();
}

function cvtx_spd_pdf_columns() {
    $options = get_option('cvtx_spd_options');
    echo "<input id='cvtx_spd_pdf_columns' name='cvtx_spd_options[cvtx_spd_pdf_columns]' type='checkbox' ".(isset($options['cvtx_spd_pdf_columns']) ? "checked='checked'" : "")." /> ";
    echo('<span class="description">Einspaltige Darstellung</span>');
}

function cvtx_spd_antrag_recipients_setting() {
    $options = get_option('cvtx_spd_options');
    if (!isset($options['cvtx_antrag_recipients_setting']) || !is_array($options['cvtx_antrag_recipients_setting'])) $options['cvtx_antrag_recipients_setting'] = array();
    echo('<textarea id="cvtx_antrag_recipients_setting" name="cvtx_spd_options[cvtx_antrag_recipients_setting]" cols="60" rows="10">'.implode("\n", $options['cvtx_antrag_recipients_setting']).'</textarea> ');
    echo('<span class="description">(Adressaten durch Zeilenumbruch geteilt eingeben)</span>');
}
?>