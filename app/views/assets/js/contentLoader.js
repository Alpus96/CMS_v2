$(document).ready(() => {
    const AJAX = new Ajex();
    const content_loader = new ContentLoader();
});

/*
*   TODO:   Add response handeling.
* */
class ContentLoader {
    construct () {
        this.error= '';
    }

    loadPost (id) {
        this.error = '';
        if (!is_numeric(id)) {
            this.error = 'loadPost(id) : Invalid id passed, must be numeric.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/post/'+id, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadPosts (category) {
        this.error = '';
        if (!is_string(category) || category === '') {
            this.error = 'loadPosts(category) : Invalid category passed, must be non-empty string.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/posts/'+category, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadArticle (id) {
        this.error = '';
        if (!is_numeric(id)) {
            this.error = 'loadArticle(id) : Invalid id passed, must be numeric.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/article/'+id, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadArticles (category) {
        if (!is_string(category) || category === '') {
            this.error = 'loadArticles(category) : Invalid category passed, must be non-empty string.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/articles/'+category, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadImagePost (id) {
        this.error = '';
        if (!is_numeric(id)) {
            this.error = 'loadImagePost(id) : Invalid id passed, must be numeric.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/imagepost/'+id, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadImagePosts (category) {
        if (!is_string(category) || category === '') {
            this.error = 'loadImagePosts(category) : Invalid category passed, must be non-empty string.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/imageposts/'+category, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadImageLink (id) {
        this.error = '';
        if (!is_numeric(id)) {
            this.error = 'loadImageLink(id) : Invalid id passed, must be numeric.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/imagelink/'+id, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadImageLinks (category) {
        if (!is_string(category) || category === '') {
            this.error = 'loadImageLinks(category) : Invalid category passed, must be non-empty string.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/imagelinks/'+category, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

}
