$(document).ready(() => {
    const AJAX = new Ajex();
    const content_loader = new ContentLoader();
});

/*
*   TODO:   Rewrite to combine id and category.
*
*   TODO:   Add response handeling.
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

}
