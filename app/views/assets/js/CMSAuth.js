$(document).ready(() => {
    const AJAX = new Ajax();
    const cookie = new Cookies(10*60*1000);
    const page = new CMS_auth();
    const editor = new CMS_editor();
});

class CMS_auth {

    construct () {
        this.addListeners();
    }

    addListeners () {
        $('#login').on('click', this.loginRequest);
        
        //  NOTE: Add after logged in?
        $('#logout').on('click', this.logoutRequest);
    }

    loginRequest () {
        //  TODO: Get the data from the login form.

        //  NOTE: window.btoa base64 encodes strings to not send in clear text.
        const form_data = {
            username : window.btoa(),
            password : window.btoa()
        };

        AJAX.post('/login', form_data, (err, res) => {
            if (!err) {
                //  TODO: Decide how the response should be structured.
                //  NOTE: Going with {bool success, object data} for now.
                if (res.success) {
                    //  NOTE:   Persumes if success
                    //          res.data = {*id* => 'token'}
                    const id = Object.keys(res.data)[0];
                    cookie.create('token', res.data);
                    //  TODO: Show login success/redirect.
                } else {
                    //  TODO: Authorization failed.
                }
            } else {
                //  TODO: Request failed.
            }
        });
    }

    logoutRequest () {
        AJAX.post('/logout', cookie.read('token'), (err, res) => {
            if (!err) {
                if (res.success) {

                } else {

                }
            } else {

            }
        });
    }
}
