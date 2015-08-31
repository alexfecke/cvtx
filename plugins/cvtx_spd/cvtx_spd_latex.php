<?php
function cvtx_spd_aeantrag_titel($post) {
    $page = get_post_meta($post->ID, 'cvtx_aeantrag_seite', true);
    $zeile = get_post_meta($post->ID, 'cvtx_aeantrag_zeile', true);
    $action = get_post_meta($post->ID, 'cvtx_aeantrag_action', true);
    echo(cvtx_get_latex('Seite '.$page.', Zeile '.$zeile.($action ? ', '.$action : '')));
}

function cvtx_spd_version_ak($post, $strip_nl = false) {
    if ($post->post_type == 'cvtx_antrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_version_ak', true), $strip_nl));
    } else if ($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_aeantrag_version_ak', true), $strip_nl));
    }
}

function cvtx_spd_tex_recipient($post, $strip_nl = false) {
    if($post->post_type == 'cvtx_antrag') {
        $rep = get_post_meta($post->ID, 'cvtx_antrag_recipient', array());
        if($rep && is_array($rep) && !empty($rep)) {
            foreach($rep as $rep1) {
                echo('\textbf{'.cvtx_get_latex('Der '.$rep1.' möge beschließen:').'}\newline');
            }
        }
        elseif($rep) {
            echo(cvtx_get_latex('Der '.get_post_meta($post->ID, 'cvtx_antrag_recipient', true).' möge beschließen:'));
        }
    }
    elseif($post->post_type == 'cvtx_aeantrag') {
        echo(cvtx_get_latex('Der '.get_post_meta($post->ID, 'cvtx_aeantrag_recipient', true).' möge beschließen:'));
    }
}

function cvtx_spd_ak_recommendation($post) {
    $ak_recommendation = get_post_meta($post->ID, $post->post_type.'_ak_recommendation', true);
    $ak_konsens = get_post_meta($post->ID, $post->post_type.'_ak_konsens', true);
    if(!cvtx_antrag_is_decided($post->ID)) {
        echo(cvtx_get_latex($ak_recommendation.' '.short_konsens($ak_konsens)));  
    } else {
        echo(cvtx_get_latex($ak_recommendation));
    }
}

function cvtx_spd_antrag_expl($post) {
    if($post->post_type == 'cvtx_antrag') {
        $expl = get_post_meta($post->ID, 'cvtx_antrag_decision_expl', true);
        if($expl) {
            echo(cvtx_get_latex($expl, $strip_nl = false));
            return;
        }
        $konsens = get_post_meta($post->ID, 'cvtx_antrag_ak_konsens', true);
        $recom = get_post_meta($post->ID, 'cvtx_antrag_ak_recommendation', true);
        /**
         * Die urspruengliche Version wurde im Konsens angenommen
         */
        /*
        if($recom == 'Annahme' && $konsens == 'Konsens') {
          echo('\textbf{'.$recom.'}');
          return;
        }
        */
        /**
         * Alle anderen Fälle: die Empfehlung der AK und die Version der AK werden ausgegeben
         */
        echo(cvtx_get_latex($recom, $strip_nl = false));
    }
}

function cvtx_spd_has_ak_recommendation($post) {
    $ak_recommendation = get_post_meta($post->ID, $post->post_type.'_ak_recommendation', true);
    $ak_konsens = get_post_meta($post->ID, $post->post_type.'_ak_konsens', true);
    if($ak_recommendation || $ak_konsens)
        return true;
    return false;
}

function cvtx_spd_result($post) {
    $decision_expl = get_post_meta($post->ID, $post->post_type.'_decision_expl', true);
    if($decision_expl) {
        echo(cvtx_get_latex($decision_expl));
        return;
    }
    $reps = wp_get_post_terms($post->ID, 'cvtx_tax_assign_to');
    if(!empty($reps)) {
        $recipients = '';
        for($i = 0; $i < count($reps); $i++) {
            $recipients .= $reps[$i]->name;
            if($i != count($reps)-1) $recipients .= ', ';
        }
        echo(cvtx_get_latex('Überweisung an '.$recipients));
        return;
    }
    $poll = get_post_meta($post->ID, $post->post_type.'_poll', true);
    if($poll) {
        echo $poll;
    }
}

function cvtx_spd_answer($post, $strip_nl = true) {
    if($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag')
        echo(cvtx_get_latex(get_post_meta($post->ID, $post->post_type.'_answer', true), $strip_nl));
}

function cvtx_spd_answer_rep($post) {
    $reps = wp_get_post_terms($post->ID, 'cvtx_tax_assign_to');
    if(!empty($reps)) {
        $recipients = '';
        for($i = 0; $i < count($reps); $i++) {
            $recipients .= $reps[$i]->name;
            if($i != count($reps)-1) $recipients .= ', ';
        }
        echo(cvtx_get_latex('Stellungnahme der '.$recipients.':'));
        return;
    }
}

function cvtx_spd_part_of_bbuch($post) {
    $poll = get_post_meta($post->ID, $post->post_type.'_poll', true);
    if($poll && ($poll == 'Annahme' || $poll == 'Überweisung')) {
      return true;
    }
    return false;
}

function cvtx_spd_part_of_ebuch($post) {
    $reps = wp_get_post_terms($post->ID, 'cvtx_tax_assign_to');
    if(!empty($reps)) {
        return true;
    }
    return false;
}

function cvtx_spd_tex_beschluss($post, $strip_nl = true) {
    if($post->post_type == 'cvtx_antrag') {
        $beschl = get_post_meta($post->ID, 'cvtx_antrag_decision', true);
        if($beschl) {
            echo(cvtx_get_latex($beschl, $strip_nl));
            return;
        }
    }
    $decision_expl = get_post_meta($post->ID, $post->post_type.'_decision_expl', true);
    if($decision_expl && ($decision_expl == 'Annahme in der Fassung der Antragskommission' ||
                          $decision_expl == 'Annahme in der Fassung des Landesvorstandes')) {
        $version_ak = get_post_meta($post->ID, $post->post_type.'_version_ak', true);
        echo(cvtx_get_latex($version_ak, $strip_nl));
        return;
    }
    cvtx_antragstext($post, $strip_nl);
}

function cvtx_spd_tex_beschluss1($post, $strip_nl = false) {
    /**
     * Wenn eine manuel eingetragene Beschlussversion existiert, geht diese vor
     */
    if($post->post_type == 'cvtx_antrag') {
        $expl = get_post_meta($post->ID, 'cvtx_antrag_decision_expl', true);
        $beschl = get_post_meta($post->ID, 'cvtx_antrag_decision', true);
        if($beschl) {
            if($expl) {
                /*
                echo('\textbf{'.cvtx_get_latex($expl, $strip_nl).'}');
                echo('\newline\newline ');
                */
            }
            echo(cvtx_get_latex($beschl, $strip_nl));
            return;
        }
        $poll = get_post_meta($post->ID, 'cvtx_antrag_poll', true);
        $konsens = get_post_meta($post->ID, 'cvtx_antrag_ak_konsens', true);
        $recom = get_post_meta($post->ID, 'cvtx_antrag_ak_recommendation', true);
        /**
         * Fassung der AK
         */
        if($recom && $poll == 'Annahme' && ($recom == 'Annahme in der Fassung der Antragskommission' ||
                                          $recom == 'Annahme in der Fassung des Landesvorstandes')) {
            echo(cvtx_get_latex(get_post_meta($post->ID, 'cvtx_antrag_version_ak', true), $strip_nl));
            return;
        }
        echo(cvtx_get_latex($post->post_content, $strip_nl));
    }
}
?>