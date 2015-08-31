<?php
/**
 * Sidebar
 *
 * @package WordPress
 * @subpackage cvtx_theme_spd
 */
?>

<div id="sidebar">
	<ul class="side">
		<?php if(function_exists('dynamic_sidebar')) {
			dynamic_sidebar('cvtx');
		}?>
	</ul>
</div>