<?php

include_once '../../../../wp-config.php';
include '../define.php';

global $wpdb;

//get all attachments (media library)
/*
$args = array('post_type'=>'attachment');
$attachments = get_posts($args);
*/
$query = "SELECT * FROM ".$wpdb->posts." WHERE post_type = 'attachment' ORDER BY post_title ASC";
$attachments = $wpdb->get_results($query);


if(count($attachments)>0) {
	foreach ($attachments as $attachment) {
		$mime_type_parts = explode('/', $attachment->post_mime_type); 
		//keep only audio
		if($mime_type_parts[0] == 'audio'){
			$audio_options .= '<option value="'.$attachment->ID.'">'.$attachment->post_title.'</option>';
		}
	}
}

$row = '
<tr>
	<td>
		<select name="status[]">
			<option value="publish" style="color: #008000;">Publish</option>
			<option value="pending" style="color: #F00000;">Pending</option>
		</select>
	</td>
	<td><select name="media_id[]">'.$audio_options.'</select></td>
	<td><input type="text" name="artist[]" value=""></td>
	<td><input type="text" name="duration[]" value=""></td>
	<td>
		<img class="minus_btn" src="'.MP3PLAYLIST_PLUGIN_PATH.'style/minus.png" alt="" />
		<img class="plus_btn" src="'.MP3PLAYLIST_PLUGIN_PATH.'style/plus.png" alt="" />
		<img class="drag" src="'.MP3PLAYLIST_PLUGIN_PATH.'style/drag.png" alt="" />
	</td>
</tr>
';

echo $row;
?>