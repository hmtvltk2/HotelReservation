function createForm(reviewId, commentId, userId, innerCommentBlock, commentBlock, comment, name) {

    var container = document.createElement("div");
    container.setAttribute("class", "commentWrapper");
    container.setAttribute('id', "editcomentform_" + commentId);
    //create the tmp for to edit a comment
    var form = document.createElement("form");
    form.setAttribute('method', "post");
    form.setAttribute('action', "");
    form.setAttribute('class', "commentForm");
    form.setAttribute('onsubmit', "return editComment(" + reviewId + "," + commentId + "," + userId + ")");

    var label = document.createElement("label");
    label.setAttribute("class", "author");

    var nameAdmin = document.createTextNode(name);
    label.appendChild(nameAdmin);


    var editableTextarea = document.createElement("textarea"); //input element, text
    editableTextarea.setAttribute('type', "text");
    editableTextarea.setAttribute('id', "commentEditable_" + commentId);
    var defaultComment = document.createTextNode(comment);
    editableTextarea.appendChild(defaultComment);


    var editButton = document.createElement("input"); //input element, Submit button
    editButton.setAttribute('class', "save");
    editButton.setAttribute('value', "Save");
    editButton.setAttribute('type', "submit");

    var cancelButton = document.createElement("input");
    cancelButton.setAttribute('class', "cancel");
    cancelButton.setAttribute('type', "button");
    cancelButton.setAttribute('value', "Cancel");
    cancelButton.setAttribute('onclick', "cancelEditing(commentBlock_" + commentId + "," + "editcomentform_" + commentId + ", " + "innerCommentBlock_" + commentId + ")");

    var adminName = document.createElement("p");
    adminName.appendChild(nameAdmin);

    form.appendChild(label);
    form.appendChild(editableTextarea);
    form.appendChild(editButton);
    form.appendChild(cancelButton);
    form.appendChild(adminName);

    container.appendChild(form);


    //and some more input elements here
    //and dont forget to add a submit button
    innerCommentBlock.style.display = "none";
    commentBlock.appendChild(container);
}

function postCommentService(comment, userId, reviewId, commentId, url) {
    if (comment != '') {
        if (comment && userId && reviewId) {
            jQuery.ajax
            ({
                type: 'post',
                url: url,
                data: {
                    comment: comment,
                    userId: userId,
                    reviewId: reviewId,
                    id: commentId
                },
                success: function (response) {
                    document.getElementById("all_comments_" + reviewId).innerHTML = document.getElementById("all_comments_" + reviewId).innerHTML + response;
                    document.getElementById("comment_" + reviewId).value = '';
                    showCommentLink(reviewId);
                },
                error: function (data) {
                    console.log(data);
                }
            });

            return false;
        }
    } else {
        showCommentLink(reviewId);
        return false;
    }
}

function editCommentService(commentId, userId, reviewId, formId, url, comment) {
    if (commentId && userId && reviewId) {
        //after the DOM Changes
        if (comment.value != '') {
            jQuery.ajax
            ({
                type: 'post',
                url: url,
                data: {
                    comment: comment,
                    userId: userId,
                    reviewId: reviewId,
                    id: commentId
                },
                success: function (response) {
                    //remove the form from the comment block
                    document.getElementById("commentBlock_" + commentId).removeChild(formId);
                    //update the comment block with the response
                    document.getElementById("commentBlock_" + commentId).innerHTML = response;
                },
                error: function (data) {
                    console.log(data);
                }
            });

            return false;
        }
    } else {
        return false;
    }
}

function deleteService(commentId, userId, reviewId, url) {
    if (commentId && userId && reviewId) {
        jQuery.ajax
        ({
            type: 'post',
            url: url,
            cache: false,
            data: {
                userId: userId,
                reviewId: reviewId,
                id: commentId
            },
            success: function () {
                var commentBlockEl = document.getElementById("commentBlock_" + commentId);
                commentBlockEl.parentNode.removeChild(commentBlockEl);
            },
            error: function (data) {
                console.log(data);
            }
        });

        return false;
    }
}

function showCommentLink(reviewId){
    var commentLink = document.getElementById('leaveaComment_'+reviewId);
    commentLink.style.display = 'block';
    var commentForm = document.getElementById('form_'+reviewId);
    commentForm.style.display = 'none';
}


function showTheCommentForm(reviewId){
    var commentLink = document.getElementById('leaveaComment_'+reviewId);
    commentLink.style.display = 'none';
    var commentForm = document.getElementById('form_'+reviewId);
    var commetTextArea = document.getElementById('comment_'+reviewId);
    commentForm.style.display = 'flex';
    commentForm.scrollIntoView(true);
    var top = commentForm.documentOffsetTop() - ( window.innerHeight / 2 );
    window.scrollTo( 0, top );
    commetTextArea.setFocus();
}
Element.prototype.documentOffsetTop = function () {
    return this.offsetTop + ( this.offsetParent ? this.offsetParent.documentOffsetTop() : 0 );
};

Element.prototype.setFocus = function(){
    this.focus();
    return false;
};

function checkClikedLeaveAComment() {
    var submittedReviewId = document.getElementById('clickedLink');
    if (submittedReviewId.value > 0 && submittedReviewId.value != '') {
        showTheCommentForm(submittedReviewId.value);
        submittedReviewId.parentNode.removeChild(submittedReviewId);
    }
}

