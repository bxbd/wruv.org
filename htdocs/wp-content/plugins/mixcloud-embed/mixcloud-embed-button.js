(function() {
    tinymce.create('tinymce.plugins.MixcloudEmbedButton', {
        
        init : function(ed, url) {
        
        	var popUpURL = url + '/mixcloud-embed-tinymce.html';
        
			ed.addCommand('MixcloudEmbedPopup', function() {
				ed.windowManager.open({
					url : popUpURL,
					width : 600,
					height : 500, 
					height : 500,
					inline : 1
				});
			});

			ed.addButton('MixcloudEmbedButton', {
				title : 'Mixcloud Embed',
				image : url + '/mixcloud-embed-button.png',
				cmd : 'MixcloudEmbedPopup'
			});
		}
    });
    tinymce.PluginManager.add('MixcloudEmbedButton', tinymce.plugins.MixcloudEmbedButton);
}());