<?php
JHTML::_('stylesheet', 	'components/'.getBookingExtName().'/assets/js/commentingjs/css/commenting.css');

$hotel = $this->hotel;
$hotelUrl = JURI::current() . '?hotel_id=' . $this->hotel->hotel_id . '&tip_oper=-1&task=showTab';


if ($this->state->get("hotel.tabId") == 4) {
	ReviewsService::generateReviewsMetaData($this->hotel);
}
?>
<div class="hotel-box hotel-item">
    <form  action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotel'.JHotelUtil::getItemIdS()) ?>" method="post" name="reviewForm" id="reviewForm" >
    <h2><?php echo JText::_('LNG_REVIEWS_OF', true) . ' ' . $this->hotel->hotel_name ?></h2>

    <div class="hotel-rating-info row-fuild">
        <div class="rating-score span6">
            <h4><?php echo $hotel->reviewScoreTranslation; ?></h4>

            <div>
                <div class="rating_total_score_number">
                    <strong><?php echo JHotelUtil::fmt($hotel->hotel_rating_score, 1) ?></strong>
                </div>
            </div>
           	<?php if(isset($hotel->ratingScores) && count($hotel->ratingScores)>0){?>
	        <div class="hotel-rating clear">
		        <?php foreach($hotel->ratingScores as $ratingScore){ ?>
			        <div class="rating-classification">
				        <?php echo "<b>".$ratingScore->name."</b>";?>
			        </div>
		        <?php } ?>
	        </div>
	        <?php } ?>
            <div class="clear"> 
            	<?php echo JText::_('LNG_REVIEW_BASED_ON', true); ?> 
            	<a href="<?php echo JHotelUtil::getHotelLink($this->hotel) . '?' . strtolower(JText::_("LNG_REVIEWS")) ?>"><?php echo $hotel->reviews[0]->totalReviewsCount ?>&nbsp;<?php echo JText::_('LNG_REVIEW_NAMING', true); ?></a>
            </div>

            <p> <?php echo JText::_('LNG_REVIEWS_DESCRIPTION_TEXT_1', true); ?>
                <strong> <?php echo JText::_('LNG_REVIEWS_DESCRIPTION_TEXT_2', true); ?> </strong> <?php echo JText::_('LNG_REVIEWS_DESCRIPTION_TEXT_3', true); ?>
                <i><?php echo $this->hotel->hotel_name ?></i>.</p>

        </div>
        <div class="rating-criterias span6">
            <h4><strong> <?php echo JText::_('LNG_SCORE_BREAKDOWN', true) . ' ' . $this->hotel->hotel_name ?></strong> :
            </h4>
            <?php foreach ($hotel->reviewAnwersScore as $answer) { ?>
                <div class="rating-criteria">
                    <p class="rating-criteria-title">
                        <?php echo $answer->question ?>
                    </p>

                    <div class="rating-criteria-score">
                        <?php echo JHotelUtil::fmt($answer->average, 1) ?>
                    </div>
                    <div class="criteria-score">
                        <div style="width: <?php echo JHotelUtil::fmt($answer->average, 1) * 10 ?>%" class="rating-bar">
                            &nbsp;</div>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php } ?>
        </div>
        <div class="clear"></div>
    </div>
        <input type="hidden" name="task"  id="task" value=""/>
        <input type="hidden" name="hotelIdReview" id="hotelIdReview" value="<?php echo $this->hotel->hotel_id?>"/>
        <input type="hidden" name="clickedLink" id="clickedLink" value="<?php echo $this->reviewCommentId ?>" />
    </form>
</div>



<form  action="<?php echo JRoute::_('index.php?option=com_jhotelreservation&view=hotel'.JHotelUtil::getItemIdS()) ?>" method="post" name="adminForm" id="adminForm" >
        <input type="hidden" name="hotel_id" id="hotel_id" value="<?php echo $this->hotel->hotel_id?>"/>
        <input type="hidden" name="<?php  echo strtolower(JText::_("LNG_REVIEWS")) ?>" value="<?php  echo strtolower(JText::_("LNG_REVIEWS")) ?>"/>
       

<?php $reviews = JRequest::getVar(strtolower(JText::_("LNG_REVIEWS"))); ?>
<?php if (isset($reviews)) { ?>
    <div id="reviews-container">
        <div class="blue-box clear">
            <div class="result-counter"><?php echo $this->pagination->getResultsCounter()?></div>
        </div>
        <div class="hotel-reviews">
            <?php foreach ($hotel->reviews as $review) { ?>
                <div class="hotel-review">
                    <div class="rating_total_score_number">
                        <strong> <?php echo JHotelUtil::fmt($review->average, 1) ?></strong>
                    </div>
                    <div class="review-details">
                        <div class="reviewer-name">
                            <?php echo $review->last_name ?>
                        </div>
                        <div class="reviewer-type">
                            <?php echo $review->party_composition ?>
                        </div>
                        <div class="reviewer-location">
                            <span class="country"><?php echo $review->country ?></span>
                        </div>
                        <span
                            class="review-date"><?php echo strftime("%B %d, %Y", strtotime($review->review_date)) ?></span>
                    </div>
                    <div class="review-container">
                        <div class="review-comment group">
                            <div class="review-col review-info" >
                                <div class="review-tile review-text"><?php echo $review->review_short_description ?></div>
                                <div class="review-description review-text"> <?php echo $review->review_remarks ?></div>
                            </div>
                            <div class="review-col social-actions">
                                <div class="hotel-actions">
                                    <a class="ui-hotel-button small" onclick="displayModalShare(<?php echo $review->review_id ?>)" id="share">
                                        <?php echo JText::_('LNG_SHARE')?>
                                    </a>
					            </div>
                                <div class="leaveCommentLink" style="display:<?php echo isSuperUser()?"":"none"?>">
                                    <?php
                                    if($this->user->id == 0) { ?>
                                        <h4><a href="javascript:void(0)" class="link" onclick="loginUser(<?php echo $review->review_id;?>);"><?php echo JText::_('LNG_GUEST_USER_LOGIN_HERE_TO_COMMENT')?></a>
                                        </h4>
                                    <?php }elseif($this->user->id > 0){
                                        ?>
                                        <h4><a id="leaveaComment_<?php echo $review->review_id;?>" class="link" href="javascript:void(0)" onclick="showTheCommentForm(<?php echo $review->review_id ?>);"><?php echo JText::_('LNG_GUEST_USER_LOGIN_HERE_TO_COMMENT')?></a>
                                        </h4>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>

                    <div id="all_comments_<?php echo $review->review_id ?>" class="all_comments">
                        <?php foreach ($review->comments as $reviewComment) {
                            if (isset($reviewComment)) {
                                ?>
                                <div id="commentBlock_<?php echo $reviewComment->id ?>" class="commentBlock">
                                    <ul id="innerCommentBlock_<?php echo $reviewComment->id ?>"
                                        class="innerCommentBlock">
                                        <li>
                                            <header>
                                                <label class="author"> <?php echo $reviewComment->name ?> </label>
                                                <p> <?php echo $reviewComment->name ?> </p>
                                            </header>
                                            <article>
                                                <p class="comment"><?php echo $reviewComment->comment; ?></p>
                                                <?php
                                                if ($this->user->id > 0) {
                                                    if (JFactory::getUser()->id == $reviewComment->userId || $this->user->get('isRoot')) { ?>
                                                        <span class="editButtons">
                                                            <button
                                                                class="cancel"
                                                                onclick="deleteComment(<?php echo $review->review_id ?>,<?php echo $reviewComment->id ?>)">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                            <button
                                                                class="save"
                                                                onclick="editCommentHTML(<?php echo $review->review_id ?>,<?php echo $reviewComment->id ?>,'<?php echo (string)$reviewComment->comment; ?>','<?php echo (string)$reviewComment->name ?>')">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                        </span>
                                                    <?php }
                                                } ?>
                                                <div class="clear"></div>
                                            </article>
                                        </li>
                                    </ul>
                                </div>
                            <?php }
                        } ?>
                    </div>
                    <?php if ($this->user->id > 0) { ?>
                        <form style="display: none;" class="commentFormSingle" method='post' action=""
                              id="form_<?php echo $review->review_id ?>"
                              onsubmit="return postComment('<?php echo $review->review_id ?>');">
                            <textarea id="comment_<?php echo $review->review_id ?>"
                                      placeholder="<?php echo JText::_('LNG_COMMENT') ?>"></textarea>

                            <div class="btnContainer">
                                <button type="submit"
                                        class="ui-hotel-button small"><?php echo JText::_('LNG_ADD_COMMENT') ?></button>
                            </div>
                        </form>
                    <?php } ?>
                </div>

                <div id="share_container_<?php echo $review->review_id ?>"  class="popup-review" style="display: none">
                        <div class="titleBar">
                             <span class="popup-review-title"></span>
                                <span  title="Cancel"  class="popup-close-button" onClick="jQuery.unblockUI();">
                                     <span title="Cancel" class="closeText">x</span>
                                </span>
                        </div>
                        <div class="popup-review-content">
                            <div class="title" id="sharing_<?php echo $review->review_id ?>">
                                <h3>
                                    <strong><?php echo JText::_('LNG_REVIEW', true) ?></strong>
                                </h3>
                            </div>
                            <div class="popup-review-content-body">
                                <iframe style="width: 100%;height: 480px;" name="review-body" class="review-body" id="reviewContent_<?php echo $review->review_id ?>" src="about:blank">

                                </iframe>
                            </div>
                        </div>
                </div>
            <?php } ?>
        </div>
		<div class="pagination">
			<?php echo $this->pagination->getListFooter(); ?>
			<div class="clear"></div>
		</div>

    </div>
</form>
<?php } ?>
<script type="text/javascript">
    function postComment(reviewId, commentId) {
        var comment = document.getElementById("comment_" + reviewId).value;
        var userId = '<?php echo JFactory::getUser()->id ?>';

        var siteRoot = '<?php echo JURI::root(); ?>';
        var bookingExtName = '<?php echo getBookingExtName()?>';
        var url = siteRoot + '/index.php?option=' + bookingExtName + '&view=hotel&task=hotel.saveComment';

        postCommentService(comment, userId, reviewId, commentId, url);
        return false;
    }

    function editCommentHTML(reviewId, commentId, comment, name) {
        var userId = '<?php echo JFactory::getUser()->id ?>';
        var commentBlock = document.getElementById("commentBlock_" + commentId);
        var innerCommentBlock = document.getElementById("innerCommentBlock_" + commentId);

        createForm(reviewId, commentId, userId, innerCommentBlock, commentBlock, comment, name);
        return false;
    }


    function editComment(reviewId, commentId, userId) {
        var siteRoot = '<?php echo JURI::root(); ?>';
        var bookingExtName = '<?php echo getBookingExtName()?>';
        var url = siteRoot + '/index.php?option=' + bookingExtName + '&view=hotel&task=hotel.editComment';
        var comment = document.getElementById("commentEditable_" + commentId).value;
        var formId = document.getElementById("editcomentform_" + commentId);

        editCommentService(commentId, userId, reviewId, formId, url, comment);
        return false;
    }

    function deleteComment(reviewId, commentId) {
        var userId = '<?php echo JFactory::getUser()->id ?>';
        var siteRoot = '<?php echo JURI::root(); ?>';
        var bookingExtName = '<?php echo getBookingExtName()?>';
        var url = siteRoot + '/index.php?option=' + bookingExtName + '&view=hotel&task=hotel.deleteComment';

        deleteService(commentId, userId, reviewId, url);
        return false;
    }

    function cancelEditing(commentBlock, formId, innerCommentBlock) {
        commentBlock.removeChild(formId);
        innerCommentBlock.style.display = "block";
        return false;
    }

    function loginUser(reviewId){
        var form 	= document.forms['reviewForm'];
        form.task.value	="hotel.loginUser";
        form.clickedLink.value = reviewId;
        form.submit();
    }

    function displayModalShare(reviewId){
        var fieldName = 'reviewContent_'+reviewId;
        var siteRoot = '<?php echo JURI::root();?>';
        var compName = '<?php echo getBookingExtName();?>';
        var share = 1;
        var url = siteRoot+'index.php?option='+compName+'&tmpl=component&task=hotelratings.printRating&view=hotelratings&review_id='+reviewId+'&share='+share;

        var frame = document.getElementById(fieldName);
        frame.setAttribute('src',url);

        jQuery.blockUI({
            message: jQuery('#share_container_'+reviewId), css: {
                top:  60 + 'px',
                left: '3%',
                width: '80%',
                backgroundColor: '#fff',
                cursor: 'default'
            }
        });
        jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);
        jQuery('.blockUI.blockMsg').center();
    }

<?php
   if(isset($this->reviewCommentId) && $this->reviewCommentId > 0 ){
   $link = JHotelUtil::getHotelLink($hotel).'?'.strtolower(JText::_("LNG_REVIEWS"));
   ?>
        window.onload = function() { checkClikedLeaveAComment() };
        window.history.pushState({},"", "<?php echo $link?>");
<?php } ?>
</script>