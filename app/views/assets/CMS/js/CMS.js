/*
*   TODO:   Write comments and review code.
* */
class CMS {
    constructor () {
        if (window.location.href.indexOf('/login') != -1) {
            this.addLoginListner();
        } else if (window.location.href.indexOf('/edit') != -1 || window.location.href.indexOf('/settings') != -1) {
            if (!cookie.read('token')) { window.location.href = '/projects/CMS_v2/login'; }
            //  NOTE: Logout button should be added in backend.
            this.addLogoutListner();
        } else { console.warn('CMS class not applicable.'); }
    }

    addLoginListner () {
        $('form#login').submit('submit', (event) => {
            event.preventDefault();
            this.loginRequest();
        });
    }

    addLogoutListner () {
        $('button#logout').on('click', this.logoutRequest);
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

    logoutRequest () {
        AJAX.post('/projects/CMS_v2/logout', cookie.read('token'), (err, res) => {
            console.log(res);
            if (!err) {
                if (res.success) {
                    cookie.delete('token');
                    window.location.href = '/projects/CMS_v2/';
                } else {

                }
            } else {
                console.log(err);
            }
        });
    }

}

$(document).ready(() => { const cms = new CMS(); });