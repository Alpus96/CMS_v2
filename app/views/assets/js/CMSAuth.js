/*
*   TODO:   Write code to handle negative resonse.
*
*   TODO:   Write comments.
* */
class CMS_auth {

    constructor () { this.addListener(); }

    addListener () {
        $('form#login').submit('submit', (event) => {
            event.preventDefault();
            this.loginRequest();
        });
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
            //  NOTE:   Backend gives res null instead of error code.
            if (!err) {
                if (res.success) {
                    cookie.create('token', res.token);
                    window.location.href = 'edit';
                } else {
                    if ($('p#msg-text').hasClass('hidden'))
                    { $('p#msg-text').removeClass('hidden'); }

                    if ($('p#msg-text').hasClass('alert-danger'))
                    { $('p#msg-text').removeClass('alert-danger'); }

                    $('p#msg-text').addClass('alert-warning');
                    $('p#msg-text').text(
                        'Fel användarnamn eller lösenord!'
                    );
                }
            } else {
                if ($('p#msg-text').hasClass('hidden'))
                { $('p#msg-text').removeClass('hidden'); }

                if ($('p#msg-text').hasClass('alert-warning'))
                { $('p#msg-text').removeClass('alert-warning'); }

                $('p#msg-text').addClass('alert-danger');
                $('p#msg-text').text(
                    'Fel uppstod, försök igen senare!'
                );
            }
        });
    }

}

$(document).ready( ()=>{ const page = new CMS_auth(); } );