/*
*   TODO:   Write code to handle negative resonse.
*
*   TODO:   Write comments.
* */
class CMS_auth {

    constructor () { this.addListener(); }

    addListener () { $('#login').on('click', () => { this.loginRequest(); }); }

    loginRequest () {
        //  Get the data from the login form.
        //  NOTE: window.btoa base64 encodes strings to not send in clear text.
        const form_data = {
            username : window.btoa($('#username').val()),
            password : window.btoa($('#password').val())
        };

        //  Send the login request.
        //  TODO:   Change the url on relese.
        AJAX.post('/projects/CMS_v2/login', form_data, (err, res) => {
            if (!err) {
                //  NOTE: Going with {bool success, object token} for now.
                if (res.success) {
                    //  NOTE:   Persumes if success
                    //          res.token = {*id* => 'token string'}
                    cookie.create('token', res.token);
                    window.location.href = 'edit';
                } else {
                    //  TODO: Authorization failed.
                    console.log('Request success false : '+res.error);
                }
            } else {
                //  TODO: Request failed.
                console.error('Request failed : '+err);
            }
        });
    }

}

$(document).ready( ()=>{ const page = new CMS_auth(); } );