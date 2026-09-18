<?php
/**
 * Backend Class for use in all amr plugins
 * Version 0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // no direct access
}

//------------------------------------------------------------------------------------------------------------------
if (!class_exists('amr_saw_plugin_admin')) {
	class amr_saw_plugin_admin {
		var $hook 		= 'amr_saw';
		var $longname	= 'Shortcode any widget - insert widgets or widget areas into a page.';
		var $shortname	= 'Shortcode any widget';
		var $accesslvl	= 'manage_options';

		function __construct() {
			add_action('admin_menu', array($this, 'register_settings_page') );
		}

		function register_settings_page() {
			add_options_page( $this->longname, $this->shortname, $this->accesslvl, $this->hook, array($this,'config_page'));
		}

		function plugin_options_url() {
			return admin_url( 'options-general.php?page='.$this->hook );
		}

		function admin_heading($title)  {
			echo '<div class="wrap">
			<h2>'.esc_html($title).'</h2>';
		}

		function admin_subheading($title)  {
			echo '<h2>'.esc_html($title).'</h2>';
		}

		function config_page() {
			if (!current_user_can($this->accesslvl))
				wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'amr-shortcode-any-widget'));

			$this->admin_heading($this->longname);
			$this->where_shortcode();

			echo '<h2>Help:</h2>';
			echo '<h3>More detailed instructions at the wordpress plugin <a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/amr-shortcode-any-widget/#installation">installation and faq pages.</a></h3>';
			echo '<ol>';
			echo '<li>';
			esc_html_e('Test your widget in a normal sidebar first.', 'amr-shortcode-any-widget');
			echo ' <a title="Go to widget area" href="'.esc_url(admin_url('widgets.php')).'">';
			esc_html_e('Go to widgets', 'amr-shortcode-any-widget');
			echo '</a></li>';
			echo '<li>';
			esc_html_e('Drag the widgets you want to use to the shortcodes sidebar.', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			esc_html_e('Add a do_widget or do_widget_area shortcode to a page.', 'amr-shortcode-any-widget');
			echo ' <a title="Create a page" href="'
			.esc_url(admin_url('post-new.php?post_type=page&content=%5Bdo_widget%20Archives%5D'))
			.'">';
			esc_html_e('Create a page with example do_widget shortcode', 'amr-shortcode-any-widget');
			echo '</a>';
			echo '</li>';
			echo '</ol>';

			echo '<h2>';
			esc_html_e('To add a single widget to a page', 'amr-shortcode-any-widget');
			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			esc_html_e('Add the shortcode [do_widget widgetname] to a page:', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			echo '[do_widget categories] or [do_widget name=categories] ';
			echo '</li>';
			echo '<li>';
			esc_html_e('[do_widget "tag cloud"] or [do_widget id=widgetid]', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '<li>';
			echo 'To see a list of your widgets in their sidebars, add <b>?do_widget_debug</b> to the url of page with the do_widget shortcode.';
			echo '</li>';
			echo '</ul>';

			echo '<br />';
			echo '<h2>';
			esc_html_e('More advanced options:','amr-shortcode-any-widget');
			echo '</h2>';
			echo '<ul><li>';
			echo 'Use title=false to hide a widget title. ';
			echo '</li>';
			echo '<li>';
			echo '[do_widget pages title=false]  will hide the widget title';
			echo '</li></ul>';
			echo '<h3>';
			esc_html_e('To change the style, change the html:','amr-shortcode-any-widget');
			echo '</h3>';
			echo '<ul>';

			echo '<li>';
			echo 'Use title=somehtmltag and wrap=somehtmltag  to change the html used.  This may change how your theme\'s css affects the widget when it is in  page.  It all depends what what html selectors your theme uses.';
			echo '</li>';
			echo '<li>';
			echo ' Use class=yourclassname to add a class - maybe to override your themes widget styling?  Obviously you must have css that applies to that class.';
			echo '</li>';

			echo '<li>';
			echo '[do_widget pages title=h3]  give the title a heading 3 html tag.';
			echo '</li>';
			echo '<li>';
			echo '[do_widget "tag cloud" wrap=aside]   will wrap the widget in an "aside" html tag.';
			echo '</li>';

			echo '</ul>';

			echo '<h4>';
			echo "Valid title html tags are : </h4><ul>";
			echo '<li>h1</li>';
			echo '<li>h2</li>';
			echo '<li>h3</li>';
			echo '<li>h4</li>';
			echo '<li>h5</li>';
			echo '<li>header</li>';
			echo '<li>strong</li>';
			echo '<li>em</li>';
			echo '</ul>';

			echo "<h4>Valid html wrap tags are :</h4><ul>";
			echo '<li>div</li>';
			echo '<li>p</li>';
			echo '<li>main</li>';
			echo '<li>aside</li>';
			echo '<li>section</li>';
			echo '</ul>';

			echo '<h2>';
			esc_html_e('To add multiple instances of the same widget:', 'amr-shortcode-any-widget');
			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			echo '[do_widget id=widgetid1] [do_widget id=widgetid2]';
			echo '</li>';
			echo '</ul>';

			echo '<h2>';
			esc_html_e('To add a widget area - all widgets in the widget area:', 'amr-shortcode-any-widget');
			echo '</h2>';
			echo '<ul>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.esc_url(admin_url('post-new.php?post_type=page&content=%5Bdo_widget_area%5D'))
			.'"> ';
			esc_html_e('Create a page with do_widget_area shortcode', 'amr-shortcode-any-widget');
			echo '</a> Hoping to use theme styling.';
			echo '</li>';
			echo '<li>';
			echo '<a title="Create a page" href="'
			.esc_url(admin_url('post-new.php?post_type=page&content=%5Bdo_widget_area%20widget_area_class%3Dnone%5D'))
			.'"> ';
			esc_html_e('Create a page with do_widget_area shortcode  without the widget_area class', 'amr-shortcode-any-widget');
			echo '</a> Hoping to avoid theme sidebar styling.';
			echo '</li>';
			echo '<li>';
			esc_html_e('NB: Using something like the twenty-fourteen theme? you might end up with white text on a white background.  Tweak the widget classes or the html of the wrap or title. If that fails, adjust your css.', 'amr-shortcode-any-widget');
			echo '</li>';
			echo '</ul>';
			echo '</div>'; // .wrap
		}

		function text_limit( $text, $limit, $finish = ' [&hellip;]') {
			if( strlen( $text ) > $limit ) {
		    	$text = substr( $text, 0, $limit );
				$text = substr( $text, 0, - ( strlen( strrchr( $text,' ') ) ) );
				$text .= $finish;
			}
			return $text;
		}

		function where_shortcode() {
			global $wpdb;

			echo '<h2>'.esc_html__('This site is using do_widget shortcodes in the following:','amr-shortcode-any-widget').'</h2>';
			$like    = '%' . $wpdb->esc_like('[do_widget') . '%';
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT ID, post_title, post_date, post_status, post_content FROM {$wpdb->posts} WHERE post_status IN ( 'publish', 'future' ) AND post_content LIKE %s ORDER BY post_date DESC",
				$like
			));

			echo '<table class="widefat wp-list-table striped"><thead><tr><th>';
			esc_html_e('Post');
			echo '</th><th>';
			esc_html_e('Published');
			echo '</th><th>';
			esc_html_e('Shortcodes');
			echo '</th></tr></thead><tbody>';
			foreach((array) $results as $result) {
				echo '<tr><td>';
				edit_post_link(esc_html($result->post_title).' ',' ',' ',$result->ID);
				echo '</td><td>'.esc_html(substr($result->post_date,0,11));
				if ($result->post_status !== 'publish') echo esc_html($result->post_status);
				echo '</td><td>';

				preg_match_all('/\[do_widget[^\]]*\]/', $result->post_content, $matches);

				foreach ($matches[0] as $m) {
					echo esc_html($m);
					echo '<br />';
				}
				echo '</td></tr>';
			}
			echo '</tbody></table>';
		}
	}
}
