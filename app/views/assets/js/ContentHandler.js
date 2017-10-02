/*
*	NOTE: TODO:	Settings link should be added in backend.
* */

/*
*	NOTE:	Change state by making a form filled in
*			with the data from the post, or blank if new post.
* */

class ContentEditor {

	constructor () {
		//	TODO: Fill the templates array.
		this.htmlTemplates = {
			'newPost' : '',
			'newArticle': '',
			'newImagePost': '',
			'newImage': ''
		};

		//	Manage and configure links.

		//	Adding event listeners for actions.
		this.postEvents();
		this.articleEvents();
		this.imagePostEvents();
		this.imageEvents();
	}

	/*
	*
	* */
	postEvents () {
		$("#startPost").on('click', this.newPost);	//	Start making a new post.
		$('#editPost').on('click', this.editPost);		//	Edit an existing post.
		$('#savePost').on('click', this.savePost);		//	Save new or edited post.
		$('#removePost').on('click', this.removePost);	//	Remove an existing post.
	}

	/*
	*
	* */
	newPost () {
		//	Show the input for a new post and the buttons
		//	for 'Select image', 'Add image' and 'Publish post'.
	}

	/*
	*
	* */
	editPost () {
		console.log('Edit post...');
	}

	/*
	*
	* */
	savePost () {
		//	TODO:	Get the input from the form and handle the response.
		const post = '';
		AJAX.post('/savePost', post, (err, res) => {
			if (!err) {
				if (res.success) {
					//	Success!
				} else {
					//	Bad request?
				}
			} else {
				//	Something went wrong! :(
			}
		});
	}

	/*
	*
	* */
	removePost () {
		//	TODO:	Get the id from the DOM and hanle the response.
		AJAX.post('/removePost', postID, (err, res) => {
			if (!err) {
				if (res.success) {
					//	Success!
				} else {
					//	Bad request?
				}
			} else {
				//	Something went wrong! :(
			}
		});
	}

	/*
	*
	* */
	articleEvents () {
		//	Start making a new article.
		$('#addArticle').on('click', this.newArticle);
		//	Edit an existing article.
		$('#editArticle').on('click', this.editArticle);
		//	Save a new or edited article.
		$('#saveArticle').on('click', this.saveArticle);
		//	Remove an existing article.
		$('#removeArticle').on('click', this.removeArticle);
	}

	/*
	*
	* */
	newArticle () {

	}

	/*
	*
	* */
	editArticle () {

	}

	/*
	*
	* */
	saveArticle () {

	}

	/*
	*
	* */
	removeArticle () {

	}

	/*
	*
	* */
	imagePostEvents () {
		$('#addImagePost').on('click', this.startImagePost);		//	Start making a new image post.
		$('#editImagePost').on('click', this.editImagePost);		//	Edit an existing image post.
		$('#saveImagePost').on('click', this.saveImagePost);		//	Save a new or edited image post.
		$('#removeImagePost').on('click', this.removeImagePost);	//	Remove an existing image post.
	}

	/*
	*
	* */
	startImagePost () {

	}

	/*
	*
	* */
	editImagePost () {

	}

	/*
	*
	* */
	saveImagePost () {

	}

	/*
	*
	* */
	removeImagPost () {

	}

	/*
	*
	* */
	imageEvents () {

	}

	/*
	*
	* */
	imageEvents () {
		$('#startImage').on('click', this.startImage);		//	Start uploading a new image, select the file to upload.
		$('#saveImage').on('click', this.saveImage);		//	Save the selected image file from start by uploading it.
		$('#selectImage').on('click', this.selectImage);	//	Select an existing image to use with your post, article or image post.
		$('#removeImage').on('click', this.removeImage);	//	Remove an existing image.
	}

	/*
	*
	* */
	startImage () {

	}

	/*
	*
	* */
	saveImage () {

	}

	/*
	*
	* */
	selectImage () {

	}

	/*
	*
	* */
	removeImage () {

	}

}

$(document).ready(() => { const editor = new ContentEditor(); });