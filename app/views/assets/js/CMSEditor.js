//$(() => { new CMS_Editor(); });

/*
*	NOTE:	Change state by making a form filled in
*			with the data from the post, or blank if new post.
* */

class CMS_Editor {

	constructor () {
		//	Adding event listeners for actions.
		this.postEvents();
		this.articleEvents();
		this.imagePostEvents();
		this.imageEvents();

		this.htmlTemplates = []; //	TODO: Fill the templates array. (Load them dynamicaly?)
	}

	/*
	*
	* */
	postEvents () {
		$("#startPost").on('click', this.startPost);	//	Start making a new post.
		$('#editPost').on('click', this.editPost);		//	Edit an existing post.
		$('#savePost').on('click', this.savePost);		//	Save new or edited post.
		$('#removePost').on('click', this.removePost);	//	Remove an existing post.
	}

	/*
	*
	* */
	startPost () {
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
		const post = JSON.encode();
		AJAX.post('/savePost', post, (err, res) => {
			if (!err) {
				if (res.success) {
					//	Success!
				} else {
					//	Something wrong with the request?
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
		const postID = JSON.encode();
		AJAX.post('/removePost', postID, (err, res) => {
			if (!err) {
				//	Success!
			} else {
				//	Something went wrong! :(
			}
		});
	}

	/*
	*
	* */
	articleEvents () {
		$('#addArticle').on('click', this.startArticle);		//	Start making a new article.
		$('#editArticle').on('click', this.editArticle);		//	Edit an existing article.
		$('#saveArticle').on('click', this.saveArticle);		//	Save a new or edited article.
		$('#removeArticle').on('click', this.removeArticle);	//	Remove an existing article.
	}

	/*
	*
	* */
	startArticle () {

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
