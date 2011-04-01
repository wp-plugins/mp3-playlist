<?php 
class Merlic_Playlist {

	/**
	 * Executed at the beginning
	 * @return
	 */
	public function init() {
	
		//include default jQuery
		if (!wp_script_is('jquery', 'queue'))
			wp_enqueue_script("jquery");
		
		//default stylesheet
		$css_url = WP_PLUGIN_URL.'/'.MP3PLAYLIST_PLUGIN.'/style/default.css';
		$css_dir = WP_PLUGIN_DIR.'/'.MP3PLAYLIST_PLUGIN.'/style/default.css';
		self::include_css($css_url, $css_dir, 'merlic_playlist_css_default');

		//jwplayer
		$jwplayer_url = WP_PLUGIN_URL.'/'.MP3PLAYLIST_PLUGIN.'/player/swfobject.js';
		$jwplayer_dir = WP_PLUGIN_DIR.'/'.MP3PLAYLIST_PLUGIN.'/player/swfobject.js';
		self::include_js($jwplayer_url, $jwplayer_dir, 'merlic_playlist_jw_player');

		//default js
		$default_js_url = WP_PLUGIN_URL.'/'.MP3PLAYLIST_PLUGIN.'/js/default.js';
		$default_js_dir = WP_PLUGIN_DIR.'/'.MP3PLAYLIST_PLUGIN.'/js/default.js';
		self::include_js($default_js_url, $default_js_dir, 'merlic_playlist_js_default');

		//jquery ui
		$jquery_ui_url = WP_PLUGIN_URL.'/'.MP3PLAYLIST_PLUGIN.'/js/jquery-ui-1.8.10.custom.min.js';
		$jquery_ui_dir = WP_PLUGIN_DIR.'/'.MP3PLAYLIST_PLUGIN.'/js/jquery-ui-1.8.10.custom.min.js';
		self::include_js($jquery_ui_url, $jquery_ui_dir, 'merlic_playlist_jquery_ui');

	}
	
	/**
	 * Adds a link in the WordPress Settings box
	 * @return
	 */
	public function admin_menu() {
		add_options_page(MP3PLAYLIST_PLUGIN_NAME, MP3PLAYLIST_PLUGIN_NAME, 'manage_options', 'mp3_playlist_admin', array('Merlic_Playlist', "draw_admin_menu"));
	}
	
	/**
	 * Draws the content of the plugin settings page
	 * @return
	 */
	public function draw_admin_menu() {
		include 'admin/options-page.php';
	}
	
	/**
	 * Called just before the content of the page is displayed
	 * @param object $content
	 * @return mixed The content of the page
	 */
	public function the_content( $content ) {
		return $content;
	}
	
	/**
	 *
	 * @param object $atts
	 * @return
	 */
	public function shortcode( $atts ) {
		$player = self::create_playlist('content');
		return $player;
	}
	
	/**
	 * Register widget for use in sidebars
	 * @return
	 */
	public function register_widget() {
		wp_register_sidebar_widget('merlic_playlist_widget', 'Playlist', array('Merlic_Playlist', 'draw_widget'));
		wp_register_widget_control('merlic_playlist_widget', 'Short desc', array('Merlic_Playlist', 'widget_title'), null, 75, 'merlic_playlist_widget');
	}
	
	/**
	 *
	 * @return
	 */
	public function widget_title() {
		if (isset($_POST['merlic_playlist_widgettitle_submit'])) {
			update_option('merlic_playlist_widget_title', $_POST['merlic_playlist_widget_title']);
		}
		
		echo '
			<p>
				<label for="merlic_playlist_widget_title">'.__('Title:').'</label><br />
				<input name="merlic_playlist_widget_title" type="text" value="'.get_option('merlic_playlist_widget_title').'" />
			</p>
			<input type="hidden" id="merlic_playlist_widgettitle_submit" name="merlic_playlist_widgettitle_submit" value="1" />
		';
	}
	
	/**
	 * Builds the full widget structure with title
	 * @param object $args
	 * @return
	 */
	public function draw_widget( $args ) {
		echo $args['before_widget'];
		echo $args['before_title'];
		echo get_option('merlic_playlist_widget_title');
		echo $args['after_title'];
		echo self::draw_widget_content();
		echo $args['after_widget'];
	}
	
	public function plugin_action_links( $links, $file ) {
		if ($file == plugin_basename(dirname(__FILE__).'/'.MP3PLAYLIST_PLUGIN.'.php')) {
			$links[] = '<a href="options-general.php?page=mp3_playlist_admin">'.__('Settings').'</a>';
		}
		
		return $links;
	}

	
	//=========================================================================================================================//
	
	/**
	 * Draws the actual content of the widget
	 * @param object $field
	 * @return
	 */
	private function draw_widget_content() {
		$player = self::create_playlist('widget');
		
		echo $player;
	}
	
	/**
	 * Includes a custom css file
	 * @param object $url
	 * @param object $dir
	 * @param object $handle
	 * @return
	 */
	private function include_css( $url, $dir, $handle ) {
		if (file_exists($dir)) {
			wp_register_style($handle, $url);
			wp_enqueue_style($handle);
		}
		else
			print($dir.' not found');
	}
	
	/**
	 * Includes a custom js file
	 * @param object $url
	 * @param object $dir
	 * @param object $handle
	 * @return
	 */
	private function include_js( $url, $dir, $handle ) {
		if (file_exists($dir)) {
			wp_register_script($handle, $url);
			wp_enqueue_script($handle);
		}
		else
			print($dir.' not found');
	}
	
	/**
	 * Prints a line to the screen
	 * @param object $text String or array to print
	 * @return
	 */
	private function println( $text ) {
		if (is_array($text) or is_object($text)) {
			echo '<pre>';
			print_r($text);
			echo '</pre>';
		}
		else {
			echo '<pre>';
			echo $text;
			echo '</pre>';
		}
		
		echo '<br />'."\n";
	}
	
	private function create_playlist($position) {
		
		$playlist = get_playlist();
			
		if($position == 'content')
			$width = $playlist->width;
		else
			$width = '100%';
		
		$playlist = '
				<div id="mediaplayer_'.$position.'"></div>
				
				<script type="text/javascript">
				  var so = new SWFObject("'.MP3PLAYLIST_PLUGIN_PATH.'player/player.swf","ply","'.$width.'","'.$playlist->height.'","9","#");
				  so.addParam("allowfullscreen","true");
				  so.addParam("allowscriptaccess","always");
				  so.addParam("wmode","opaque");
				  so.addVariable("file","'.MP3PLAYLIST_PLUGIN_PATH.'player/playlist.xml");
				  so.addVariable("playlist","bottom");
				  so.addVariable("skin","'.MP3PLAYLIST_PLUGIN_PATH.'player/simple.zip");
				  so.addVariable("backcolor","ffffff");
				  so.addVariable("frontcolor","000000");
				  so.addVariable("lightcolor","0099cc");
				  so.write("mediaplayer_'.$position.'");
				</script>
			';
		
		return $playlist;
	}
}
?>
