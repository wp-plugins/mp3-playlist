<?php

define('MP3PLAYLIST_PLUGIN', 'mp3-playlist');
define('MP3PLAYLIST_PLUGIN_PATH', WP_PLUGIN_URL.'/'.MP3PLAYLIST_PLUGIN.'/');
define('MP3PLAYLIST_PLUGIN_NAME', 'MP3 Playlist Lite');

global $wpdb;
global $wp_playlist_table;
global $wp_playlist_data_table;

global $merlic_playlist_version;

$wp_playlist_table = $wpdb->prefix.'playlist';
$wp_playlist_data_table = $wpdb->prefix.'playlist_data';

$merlic_playlist_version = '1.0.0';

?>