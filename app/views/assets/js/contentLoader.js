$(document).ready(() => {
    if (!AJAX) { const AJAX = new Ajax(); }
    const content_loader = new ContentLoader();
});

/*
*   TODO:   Add response handeling.
*
*   TODO:   Review code and write comments.
* */
class ContentLoader {
    construct () {
        this.error= '';
    }

    loadPost (parameter) {
        this.error = '';
        let getVal;
        if (typeof parameter === 'number')) {
            getVal = 'amount='+parameter;
        } else if (typeof parameter === 'string') {
            getVal = 'category='+parameter;
        } else {
            this.error = 'loadPost(id) : Invalid parameter passed, must be numeric or string.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/post?'+getVal, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadArticle (parameter) {
        this.error = '';
        let getVal;
        if (typeof parameter === 'number')) {
            getVal = 'amount='+parameter;
        } else if (typeof parameter === 'string') {
            getVal = 'category='+parameter;
        } else {
            this.error = 'loadArticle(parameter) : Invalid id passed, must be numeric.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/article?'+getVal, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadImagePost (parameter) {
        this.error = '';
        let gatVal;
        if (typeof parameter === 'number')) {
            getVal = 'amount='+parameter;
        } else if (typeof parameter === 'string') {
            getVal = 'category='+parameter;
        } else {
            this.error = 'loadImagePost(parameter) : Invalid id passed, must be numeric.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/imagepost?'+getVal, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

    loadImageLink (parameter) {
        this.error = '';
        let getVal;
        if (typeof parameter === 'number')) {
            getVal = 'amount='+parameter;
        } else if (typeof parameter === 'string') {
            getVal = 'category='+parameter;
        } else {
            this.error = 'loadImageLink(parameter) : Invalid id passed, must be numeric.';
            console.error(this.error);
            return false;
        }

        AJAX.get('/imagelink?'+getVal, (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }

}
