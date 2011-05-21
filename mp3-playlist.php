<?php 
/*
 Plugin Name: MP3 Playlist Lite
 Plugin URI: http://wordpress.phpanswer.com/playlist/
 Description: Allows you to add a playlist to pages, posts and sidebar. Just create your playlist and paste the shortcode <strong>[mp3playlist]</strong> where you want to draw it. For sidebar, drag the widget to your sidebar. If you want to be able to create multiple playlists you need to upgrade to the <a href="http://wordpress.phpanswer.com/playlist/">FULL version</a>.
 Version: 1.0.0
 Author: Cristian Merli
 Author URI: http://wordpress.phpanswer.com
 */

/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; version 2 of the License.
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


include 'define.php';
include 'playlist.class.php';
include 'playlist-utility.class.php';



add_action('admin_menu', array('Merlic_Playlist', 'admin_menu'));
add_action('init', array('Merlic_Playlist', 'init'));
add_action('widgets_init', array('Merlic_Playlist', 'register_widget'));

add_filter("the_content", array('Merlic_Playlist', 'the_content'));


add_shortcode('mp3playlist', array('Merlic_Playlist', 'shortcode'));

add_filter( 'plugin_action_links', array('Merlic_Playlist', 'plugin_action_links'), 10, 2 );

register_activation_hook(__FILE__, 'merlic_playlist_activate');
register_deactivation_hook(__FILE__, 'merlic_playlist_deactivate');

//--------------------------------------------------------------------------------------------------//

function merlic_playlist_activate() {
	global $wpdb;
	global $merlic_playlist_version;
	global $wp_playlist_table;
	global $wp_playlist_data_table;

	if ($wpdb->get_var("show tables like '$wp_playlist_table'") != $wp_playlist_table) {
		require_once (ABSPATH.'wp-admin/includes/upgrade.php');
	
		$query = "
			CREATE TABLE IF NOT EXISTS `$wp_playlist_table` (
			  `ID` int(11) NOT NULL auto_increment,
			  `name` varchar(128) NOT NULL,
			  `width` int(11) NOT NULL,
			  `height` int(11) NOT NULL,
			  `skin` VARCHAR( 128 ) NOT NULL,
			  PRIMARY KEY  (`ID`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8
		";
		dbDelta($query);
		
		$query = "
			CREATE TABLE IF NOT EXISTS `$wp_playlist_data_table` (
				  `ID` int(11) NOT NULL auto_increment,
				  `playlist_id` int(11) NOT NULL,
				  `media_id` int(11) NOT NULL,
				  `artist` varchar(128) NOT NULL,
				  `duration` varchar(128) NOT NULL,
				  `status` enum('publish','pending') NOT NULL,
				  `position` int(11) NOT NULL,
				  PRIMARY KEY  (`ID`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8
		";
		dbDelta($query);
		
		update_option("mp3_playlist_version", $merlic_playlist_version);		
	}
	else {
		
		$installed_ver = get_option("merlic_playlist_version");
		
		if ($installed_ver != $merlic_playlist_version) {
			update_option("mp3_playlist_version", $merlic_playlist_version);
		}
		
	}
	
	//$playlist_helper = new Merlic_Playlist_Helper();
	//$playlist_helper->notification();
	
}

function merlic_playlist_deactivate() {

}

function get_playlist() {
	global $wpdb;
	global $wp_playlist_table;
	
	$query = "SELECT * FROM `$wp_playlist_table`";
	$playlist = $wpdb->get_results($query);
	$playlist = $playlist[0];
	
	return $playlist;
}

function get_playlist_songs() {
	global $wpdb;
	global $wp_playlist_data_table;
	
	$query = "SELECT * FROM `$wp_playlist_data_table`";
	$playlist = $wpdb->get_results($query);
	
	return $playlist;
}

?>
