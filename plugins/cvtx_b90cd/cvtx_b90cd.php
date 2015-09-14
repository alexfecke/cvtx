<?php
/**
 * @package cvtx_b90cd
 * @version 0.1
 */

/*
Plugin Name: cvtx-Erweiterung: LaTeX-Templates im B90/Die Grünen CD
Plugin URI: http://cvtx-project.org
Description: Dieses Plugin erweitert das Antragstool "cvtx" um LaTeX-Templates im neuen Corporate Design von Bündnis90/Die Grünen.
Author: Alexander Fecke & Max Löffler
Version: 0.1
Author URI: http://alexander-fecke.de
License: GPLv2 or later
*/
define('CVTX_B90CD_PLUGIN_DIR', plugin_dir_path(__FILE__));

// set path to latex directory of this plugin
$options = get_option('cvtx_options');
$options['cvtx_latex_tpl-plugin-dir'] = CVTX_B90CD_PLUGIN_DIR.'/latex/';
update_option('cvtx_options', $options);
?>