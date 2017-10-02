/*
*   TODO:   Write code. Functions should be able to be used with any content type.
*
*   TODO:   Write comments.
*
*   TODO:   Review code and comments.
* */

class ContentRequests {
    constructor () { this.error = ''; }

    create (type, content) {
        //  TODO: Confirm content is valid.
        const url = '/create'+type;
        AJAX.post(url, content, (err, res) => {

        });
    }

    update (type, content) {
        //  TODO: Confirm content is valid. (+has id)
        const url = '/update'+type;
        AJAX.post(url, content, (err, res) => {

        });
    }

    delete (type, id) {
        //  TODO: Confirm id is valid.
        const url = '/delete'+type;
        AJAX.post(url, id, (err, res) => {

        });
    }
}

const contentRequest = new ContentRequests();