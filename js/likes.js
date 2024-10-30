(function ($) {
    $(function () {
		
		$link_likes_post = $('.likes-post-link');
		$link_likes_comment = $('.likes-comment-link');
		if($link_likes_post.length > 0){
			// On each link
			$link_likes_post.each(function(){
				// Listen the click event
				$(this).click(function(){
					relLikeLink = $(this).attr('rel');
					var theLink = $(this);
					var postId = parseInt(relLikeLink.split('_')[1]);
					var nbId = $('#nbLikePost_' + postId);
					var actionLike = ( $(this).hasClass('dolike') ) ? 'doLike' : 'doUnLike'
					
					// Do the ajax request
					// Initialise the request to mark this particular post as read
					$.post(ajaxurl, {
						action:     'like_a_post',
						post_id:    postId,
						actionLike : actionLike

					}, function (response) {
						if (response.success) {
							if(theLink.hasClass('dolike')){
								theLink.removeClass('dolike');
								theLink.addClass('doUnlike');
								theLink.html(unLikePostLabel);
							}else{
								theLink.removeClass('doUnlike');
								theLink.addClass('dolike');
								theLink.html(likePostLabel);
							}
							nbId.html(response.nbLikeText);
						}
					});
					
					return false;
				})
			});
		}
		
		if($link_likes_comment.length > 0){
			// On each link
			$link_likes_comment.each(function(){
				// Listen the click event
				$(this).click(function(){
					relLikeLink = $(this).attr('rel');
					var theLink = $(this);
					var commentId = parseInt(relLikeLink.split('_')[1]);
					var nbId = $('#nbLikeComment_' + commentId);
					var actionLike = ( $(this).hasClass('dolike') ) ? 'doLike' : 'doUnLike'
					
					// Do the ajax request
					// Initialise the request to mark this particular post as read
					$.post(ajaxurl, {
						action:     'like_a_comment',
						comment_id:    commentId,
						actionLike : actionLike

					}, function (response) {
						if (response.success) {
							if(theLink.hasClass('dolike')){
								theLink.removeClass('dolike');
								theLink.addClass('doUnlike');
								theLink.html(unLikeCommentLabel);
							}else{
								theLink.removeClass('doUnlike');
								theLink.addClass('dolike');
								theLink.html(likeCommentLabel);
							}
							nbId.html(response.nbLikeText);
						}
					});
					
					return false;
				})
			});
		}
       
 
    });
}(jQuery));