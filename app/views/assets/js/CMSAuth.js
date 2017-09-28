class CMS_auth {

    constructor () {
        this.addListeners();
        //cookie.delete('token');
    }

    addListeners () {
        $('#login').on('click', () => { this.loginRequest(); });

        //  NOTE: Add after logged in?
        $('#logout').on('click', this.logoutRequest);
    }

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
                //  TODO: Decide how the response should be structured.
                //  NOTE: Going with {bool success, object token} for now.
                if (res.success) {
                    //  NOTE:   Persumes if success
                    //          res.token = {*id* => 'token string'}
                    cookie.create('token', res.token);
                    console.log(res);
                    window.location.href = 'projects/CMS_v2/edit';
                    //  TODO: Show login success/redirect.
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

    logoutRequest () {
        AJAX.post('/logout', cookie.read('token'), (err, res) => {
            if (!err) {
                if (res.success) {
                    console.log('success');
                } else {
                    console.log('fail');
                }
            } else {
                console.log('error');
            }
        });
    }
}

$(document).ready(()=>{
    const page = new CMS_auth();
});

const cookie = new Cookies(10*60*1000);
