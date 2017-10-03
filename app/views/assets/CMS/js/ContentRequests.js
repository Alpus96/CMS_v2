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
            if (!err) {
                if (res.success) {
                    return res;
                } else {
                    return false;
                }
            } else {
                //  What to do on error?
                return false;
            }
        });
    }

    readMD (type, id) {
        //  TODO: Confirm type and id is valid.
        const url = '/read'+type+'?id='+id;
        AJAX.post(url, content, (err, res) => {
            if (!err) {
                if (res.success) {
                    return res;
                } else {
                    return false;
                }
            } else {
                //  What to do on error?
                return false;
            }
        });
    }

    update (type, content) {
        //  TODO: Confirm content is valid. (+has id)
        const url = '/update'+type;
        AJAX.post(url, content, (err, res) => {
            if (!err) {
                if (res.success) {
                    return res;
                } else {
                    return false;
                }
            } else {
                //  What to do on error?
                return false;
            }
        });
    }

    delete (type, id) {
        //  TODO: Confirm id is valid.
        const url = '/delete'+type;
        AJAX.post(url, id, (err, res) => {
            if (!err) {
                if (res.success) {
                    return res;
                } else {
                    return false;
                }
            } else {
                //  What to do on error?
                return false;
            }
        });
    }
}

const contentRequest = new ContentRequests();