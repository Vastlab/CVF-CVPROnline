jQuery(document).ready(function(){
	jQuery('a#Btwcapt').on('click', function(e){
		e.preventDefault();
		var $this		=	jQuery( this );		
		var post_id		=	jQuery( $this ).data('wp_clone_pty_id');		
		var copies		=	jQuery( $this ).parent('.clone').find( '#wp_clone_any_item_no'+post_id ).val();
		console.log(copies);
		if(copies < 1){
			alert("Please enter valid entry");
			return false;
		}
		WPCAPTY_add_ajax_for_clone( post_id, copies );
		
	});

	function WPCAPTY_add_ajax_for_clone( id, carbon ){
		jQuery.ajax({
			url:wpclone_ajax_object.ajaxurl,
			type:'post',
			data:{
				action:'wcapt_wapty',
				postid: id,
				copies: carbon
			},
		}).success(function( response ){	
			var isEditPage	=	(window.location.href).search('edit.php');	
			if( isEditPage >0 ){
				var urlp = '';
				if (window.location.href.indexOf("?") > -1) {
			      	urlp = '&orderby=ID&order=asc';
			    }
			    else{
			    	urlp = '?orderby=ID&order=asc';
			    }
				window.location.href =window.location.href+urlp;
				//document.location.reload();
			}
		});
	}
	/* display bulk clone message*/
	jQuery('select#bulk-action-selector-top, select#bulk-action-selector-bottom').on('change', function() {
		jQuery('p.blk-clon-msg').remove();  
		if(this.value === 'clone'){
			jQuery(this).parents('.tablenav').after('<p class="blk-clon-msg"><strong>Note:</strong> Bulk clone action can create a single duplicate of each item.</p>');
		}
	});

});

