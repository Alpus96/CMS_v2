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
        const url = ''+type+'?id='+id+'&asMD=true';
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

    update (content) {
        //  TODO: Confirm content is valid. (+has id)
        AJAX.post('/update', content, (err, res) => {
            if (!err) {
                if (res.success) {
                    return res;
                } else {
                    return false;
                }
            } else {
                //  TODO:  What to do on error?
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