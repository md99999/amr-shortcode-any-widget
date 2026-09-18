<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

function amr_show_shortcode_widget_possibilities () {
global $_wp_sidebars_widgets;

	if (!is_array($_wp_sidebars_widgets))
		return '';
	$sidebars_widgets = $_wp_sidebars_widgets;
	ksort ($sidebars_widgets);  // push inactive down the bottom of the list
	$text= '<ul>';
	foreach ($sidebars_widgets as $sidebarid => $sidebar) {

		if (is_array($sidebar)) {
			$text .= '<li><em>[do_widget_area '.esc_html($sidebarid).']</em><ul>';
			foreach ($sidebar as $i=> $w) {
				$text .=  '<li>';
				$text .=  '[do_widget id="'.esc_html($w).'"]';
				$text .= '</li>';
			}
			$text .=   '</ul></li>';
		}
	}
	$text .=  '</ul>';
	return ($text);
}

function amr_get_widgets_sidebar($wid) {
/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.
take the first one that matches */
global $_wp_sidebars_widgets;

	if (!is_array($_wp_sidebars_widgets))
		return false;
	foreach ($_wp_sidebars_widgets as $sidebarid => $sidebar) {
		if (is_array($sidebar) ) { // ignore the 'array version' sidebarid that isnt actually a sidebar
			foreach ($sidebar as $i=> $w) {
				if ($w === $wid) {
					return 	$sidebarid;
				}
			}
		}
	}
	return (false); // widget id not in any sidebar
}

function amr_get_sidebar_id ($name) {
/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.
take the first one that matches */
global $wp_registered_sidebars;

	foreach ((array) $wp_registered_sidebars as $i => $a) {
		if ((isset ($a['name'])) and ( $a['name'] === $name))
		return ($i);
	}
	return (false);
}

function amr_get_sidebar_name ($id) { /* dont need anymore ? or at least temporarily */
/* walk through the registered sidebars with a name and find the id - will be something like sidebar-integer.  take the first one */
global $wp_registered_sidebars;
	foreach ((array) $wp_registered_sidebars as $i => $a) {
		if ((isset ($a['id'])) and ( $a['id'] === $id)) {
			if (isset($a['name'])) return ($a['name']);
			else return ($id);
		}
	}
	return (false);
}

function amr_check_if_widget_debug() {
static $said = false;
	// only do these debug if we are logged in and are the administrator

	if (is_admin()) return false;   // if running in backend, then do not do debug.  20151217

	if (!current_user_can('manage_options'))
		return false;

	if (!isset($_GET['do_widget_debug']))
		return false;

	if ($said)
		return true;
	$said = true;

	$url_without_debug_query = esc_url(remove_query_arg( 'do_widget_debug'));
	echo '<br/>Note: Debug help is only shown to a logged-in Administrator.'
	.'<a href="'.$url_without_debug_query.'">Remove debug</a>'
	.'<br />';
	echo amr_show_shortcode_widget_possibilities (); // already escaped
	return true;
}

function amr_show_widget_debug($type, $name, $id, $sidebar) {
// only show debug info to users who can manage widgets - never to visitors

	$debug = amr_check_if_widget_debug();

	if (!current_user_can('edit_theme_options'))
		return '';

	$text =	amr_show_shortcode_widget_possibilities () ;

	if ($type=='empty') {
		$text = '<p>Problem with do_widget shortcode?  Try one of the following:</p>'.$text;
	}
	elseif (($type=='which one') and ($debug)) {
		$text = '<p>Debug help is on: Is your widget in the widgets_for_shortcodes sidebar?</p>'
		.$text;
	}

	return ($text);
}

function amr_save_shortcodes_sidebar() {  // when switching a theme, save the widgets we use for the shortcodes as they are getting overwritten
	$sidebars_widgets = wp_get_sidebars_widgets();
	if (!empty($sidebars_widgets['widgets_for_shortcodes']))
		update_option('sidebars_widgets_for_shortcodes_saved',$sidebars_widgets['widgets_for_shortcodes']);
}

function amr_restore_shortcodes_sidebar() {  // when switching a theme, restore the widgets we use for the shortcodes as they are getting overwritten

	$sidebars_widgets = wp_get_sidebars_widgets();
	$saved = get_option('sidebars_widgets_for_shortcodes_saved');
	if (empty($sidebars_widgets['widgets_for_shortcodes']) and is_array($saved) and !empty($saved)) {
		$sidebars_widgets['widgets_for_shortcodes'] = array_values(array_filter(array_map('strval', $saved)));
		wp_set_sidebars_widgets($sidebars_widgets);
	}
}

function amr_upgrade_sidebar() { // added in 2014 February for compatibility.. keep for how long. till no sites running older versions.?
	$sidebars_widgets = wp_get_sidebars_widgets();
	if (!empty($sidebars_widgets['Shortcodes']) and empty($sidebars_widgets['widgets_for_shortcodes'])) {  // we need to upgrade
		$sidebars_widgets['widgets_for_shortcodes'] = $sidebars_widgets['Shortcodes'];
		unset ($sidebars_widgets['Shortcodes']);
		wp_set_sidebars_widgets($sidebars_widgets);
		add_action( 'admin_notices', 'widgets_shortcode_admin_notice' );
	}
}

function widgets_shortcode_admin_notice() {
	if (!current_user_can('edit_theme_options'))
		return;
    ?>
    <div class="updated">
        <p><?php esc_html_e('Please go to widgets page and check your "widgets for shortcodes" sidebar.  It will hopefully have been corrected upgraded with your widgets and all should be fine.', 'amr-shortcode-any-widget'); ?></p>
    </div>
    <?php
}
