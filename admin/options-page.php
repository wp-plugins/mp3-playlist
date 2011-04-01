<?php 
global $wpdb;
global $wp_playlist_table;
global $wp_playlist_data_table;

if ($_POST['mp3_playlist_submit']) {
	
	$query = "TRUNCATE TABLE `$wp_playlist_table`";
	$wpdb->query($query);
	
	$query = "
		 INSERT INTO `$wp_playlist_table`
		 VALUES (
		 NULL,
		 '".$_POST['merlic_playlist_name']."',
		 '".$_POST['merlic_playlist_width']."',
		 '".$_POST['merlic_playlist_height']."',
		 ''
		 )
	 ";
	$wpdb->query($query);
	
	$playlist_id = $wpdb->insert_id;
	
	$query = "TRUNCATE TABLE `$wp_playlist_data_table`";
	$wpdb->query($query);

	foreach ($_POST['media_id'] as $index=>$title) {
		$query = "
			INSERT INTO `$wp_playlist_data_table` 
			VALUES (
				NULL, 
				'".$playlist_id."', 
				'".$_POST['media_id'][$index]."', 
				'".$_POST['artist'][$index]."', 
				'".$_POST['duration'][$index]."', 
				'".$_POST['status'][$index]."',
				'$index'
			)
		";
		$wpdb->query($query);
	}
	
	create_xml();
	
	$save_result = __('Your playlist has been saved.');
}

$playlist = get_playlist();
$playlist_songs = get_playlist_songs();


//get all attachments (media library)
/*
 $args = array('orderby'=>'post_title', 'order'=>'DESC', 'post_type'=>'attachment');
 $attachments = get_posts($args);
 */
$query = "SELECT * FROM ".$wpdb->posts." WHERE post_type = 'attachment' ORDER BY post_title ASC";
$attachments = $wpdb->get_results($query);


if (count($attachments) > 0) {
	foreach ($attachments as $attachment) {
		$mime_type_parts = explode('/', $attachment->post_mime_type);
		//keep only audio
		if ($attachment->post_mime_type == 'audio/mpeg') {
			$audio_options .= '<option value="'.$attachment->ID.'">'.$attachment->post_title.'</option>';
		}
		
		if ($mime_type_parts[0] == 'image') {
			$image_options .= '<option value="'.$attachment->ID.'">'.$attachment->post_title.'</option>';
		}
	}
}


?>
<div class="wrap merlic_test">
    <h2><?php _e('Playlist Settings'); ?></h2>
    <br/>
    <form id="merlic_playlist_form" method="POST" accept-charset="utf-8" target="_self" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <h3><?php _e('Audio Player'); ?></h3>
        <table class="form-table">
            <!--
            <tr valign="top">
            <th scope="row">
            <label for="merlic_playlist_player_title">
            <?php _e('Player Title'); ?>
            </label>
            </th>
            <td>
            <input type="text" name="merlic_playlist_player_title" value="<?php echo get_option('merlic_playlist_player_title'); ?>">
            <span class="description"><?php _e("Not in use yet"); ?></span>
            </td>
            </tr>
            -->
            <tr valign="top">
                <th scope="row">
                    <label for="merlic_playlist_width">
                        <?php _e('Player Width'); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="merlic_playlist_width" value="<?php echo $playlist->width > 0 ? $playlist->width : '400'; ?>"><span class="description"><?php _e("Width in px"); ?></span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="merlic_playlist_height">
                        <?php _e('Player Height'); ?>
                    </label>
                </th>
                <td>
                    <input type="text" name="merlic_playlist_height" value="<?php echo $playlist->height > 0 ? $playlist->height : '300'; ?>"><span class="description"><?php _e("Height in px"); ?></span>
                </td>
            </tr>
        </table>
        <br/>
	    <h4><?php _e('Shortcode'); ?></h4>
	    <table class="form-table">
	        <tr valign="top">
	            <th scope="row">
	                <label>
	                    [mp3playlist]
	                </label>
	            </th>
	            <td>
	                <span class="description"><?php _e("Paste this shortcode inside a page or a post"); ?></span>
	            </td>
	        </tr>
	    </table>
	    <br/>
        <h3><?php _e('MP3 List'); ?></h3>
        <span class="description">
            <p>
                <?php _e('You must add your MP3s in the <a href="media-new.php">Media panel</a>. Only <b>.mp3</b> extensions are allowed for this plugin.'); ?>
            </p>
            <p>
                <?php _e('<b>Please allow up to a few minutes for the player to update when you add remove songs (or empty the cash data of your browser).</b>'); ?>
            </p>
        </span>
        <table id="merlic_playlist">
            <thead>
                <tr>
                    <th>
                        <?php _e('Status'); ?>
                    </th>
                    <th>
                        <?php _e('Title'); ?>
                    </th>
                    <th>
                        <?php _e('Artist'); ?>
                    </th>
                    <th>
                        <?php _e('Duration'); ?>
                    </th>
                    <th>
                        <?php _e('Actions'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (count($playlist_songs) > 0) {
                	foreach ($playlist_songs as $i=>$mp3) {
                		
                ?>
                <tr>
                    <td>
                        <select name="status[]">
                            <option value="publish" style="color: #008000;"<?php echo($mp3->status == 'publish' ? 'selected="selected"' : ''); ?>>Publish</option>
                            <option value="pending" style="color: #F00000;"<?php echo($mp3->status == 'pending' ? 'selected="selected"' : ''); ?>>Pending</option>
                        </select>
                    </td>
                    <td>
                        <select name="media_id[]">
                            <?php 
                            if (count($attachments) > 0) {
                            	foreach ($attachments as $attachment) {
                            		$mime_type_parts = explode('/', $attachment->post_mime_type);
                            		//keep only audio
                            		if ($attachment->post_mime_type == 'audio/mpeg') {
                            			echo '<option value="'.$attachment->ID.'" '.($mp3->media_id == $attachment->ID ? 'selected="selected"' : '').'>'.$attachment->post_title.'</option>';
                            		}
                            	}
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="artist[]" value="<?php echo $mp3->artist; ?>">
                    </td>
                    <td>
                        <input type="text" name="duration[]" value="<?php echo $mp3->duration; ?>">
                    </td>
                    <td>
                        <img class="minus_btn" src="<?php echo MP3PLAYLIST_PLUGIN_PATH.'style/minus.png'; ?>" alt="" />
						<img class="plus_btn" src="<?php echo MP3PLAYLIST_PLUGIN_PATH.'style/plus.png'; ?>" alt="" />
						<img class="drag" src="<?php echo MP3PLAYLIST_PLUGIN_PATH.'style/drag.png'; ?>" alt="" />
                    </td>
                </tr>
                <?php 
                }
                }
                else {
                	
                ?>
                <tr>
                    <td>
                        <select name="status[]">
                            <option value="publish" style="color: #008000;">Publish</option>
                            <option value="pending" style="color: #F00000;">Pending</option>
                        </select>
                    </td>
                    <td>
                        <select name="media_id[]">
                            <?php echo $audio_options; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="artist[]" value="">
                    </td>
                    <td>
                        <input type="text" name="duration[]" value="">
                    </td>
                    <td>
                        <img class="minus_btn" src="<?php echo MP3PLAYLIST_PLUGIN_PATH.'style/minus.png'; ?>" alt="" />
						<img class="plus_btn" src="<?php echo MP3PLAYLIST_PLUGIN_PATH.'style/plus.png'; ?>" alt="" />
						<img class="drag" src="<?php echo MP3PLAYLIST_PLUGIN_PATH.'style/drag.png'; ?>" alt="" />
                    </td>
                </tr>
                <?php 
                }
                ?>
            </tbody>
        </table>
        <br/>
        <br/>
        <input class="button-primary" type="submit" name="mp3_playlist_submit" value="<?php _e('Save Options'); ?>" />
        <br/>
		<p><?php echo $save_result; ?></p>
        <br/>
		<h3>Upgrade</h3>
        <p>
            If you want to be able to create multiple playlists you need to upgrade to the <a href="http://wordpress.phpanswer.com/playlist/">FULL version</a>.
        </p>
    </form>
</div>
<?php 
function create_xml() {
	$myFile = ABSPATH.'wp-content/plugins/'.MP3PLAYLIST_PLUGIN.'/player/playlist.xml';
	$fh = fopen($myFile, 'w') or die("can't open file");
	
	//----------------------------------------------//
	$upload_dir = wp_upload_dir();

	$playlist = get_playlist_songs();
	
	if (count($playlist) > 0) {
		foreach ($playlist as $i=>$mp3) {
			if($mp3->status == "publish"){
				$media = get_post($mp3->media_id);
				
				$file_url = $upload_dir['baseurl'].'/'.get_post_meta($mp3->media_id, '_wp_attached_file', true);
				
				$items .= '
				<track>
					<title>'.$media->post_title.'</title>
					<creator>'.$mp3->artist.'</creator>
					<duration>'.$mp3->duration.'</duration>
					<location>'.$file_url.'</location>
				</track>
				';
			}
		}
	}
	
	$xml = '
<?xml version="1.0" encoding="UTF-8" ?>

<playlist version="1" xmlns="http://xspf.org/ns/0/">
	<title>'.get_option('merlic_playlist_player_title').'</title>
	<info>'.home_url().'</info>
	
	<trackList>	
	'.$items.'
	</trackList>
</playlist>
';

	//----------------------------------------------//
	fwrite($fh, $xml);
	fclose($fh);
}

?>
