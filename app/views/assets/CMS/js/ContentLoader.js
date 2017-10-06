/*
*   @description    This class contains a function for getting
*
*   TODO: review code and comments.
* */
class ContentLoader {
    //  Create the error property as an empty string.
    construct () { this.error = ''; }

    /*
    *   @description    Gets content from url and
    *                   returns it or returns false.
    *                   On false sets error property to message.
    *
    *   @arguments      url:    The url with php get query.
    *   @returns        respose from backend or false on error.
    * */
    read (type, category = false, amount = 0, id = false) {
        this.error = '';
        let url;
        if (!category && id && amount === 0) {
            url = type+'?id='+id;
        } else if (!id && amount > 0 && category && category !== '') {
            url = type+'?category='+category+'&amount='+amount;
        } else {
            this.error = 'Cannot request content per category and id simultaneously.';
            return false;
        }
        //  Use the ajax class to send the request as a url.
        return new Promise(resolve => {
            AJAX.get(url, (err, res) => {
                if (!err) {
                    if (res.success) {
                        //  If no error return the response.
                        resolve(res.data);
                    } else {
                        this.error = res.error;
                        resolve(false);
                    }
                } else {
                    //  On error set the error property
                    //  before returning false.
                    this.error = err;
                    resolve(false);
                }
            });
        });
    }

}

const Content = new ContentLoader();