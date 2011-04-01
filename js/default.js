var playlist_scr = document.getElementsByTagName('script');
var playlist_src = playlist_scr[playlist_scr.length - 1].getAttribute("src");


jQuery(document).ready(function($){
    var playlist_plugin_path = playlist_src.substr(0, playlist_src.lastIndexOf("/"));
    playlist_plugin_path = playlist_plugin_path.substr(0, playlist_plugin_path.lastIndexOf("/") + 1);
    var playlist_request_path = playlist_plugin_path + 'requests/';

    $('#merlic_playlist_form .plus_btn').live('click', function(){
        var this_row = $(this).parent().parent();
        
        $.ajax({
            type: "POST",
            url: playlist_request_path + "add_row.request.php",
            data: "",
            success: function(retval){
                this_row.after(retval);
            }
        });
        
    });
    
    $('#merlic_playlist_form .minus_btn').live('click', function(){
        var last_col = 0;
        
        $(this).parent().parent().remove();
    });
    
    $('#merlic_playlist tbody').sortable({
        opacity: 0.6,
        cursor: 'move',
        update: function(ev){
        }
    });
    
    $('a.merlic_playlist_playlist').click(function(){
        if ($(this).text() == 'Show playlist') 
            $(this).text('Hide playlist');
        else 
            $(this).text('Show playlist');
        
        $(this).next().toggle();
    });
    
    
});
