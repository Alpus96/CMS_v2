/*
*   TODO:   Write comments and review code.
* */
class UserHandler {
    constructor () {
        if (window.location.href.indexOf('/login') != -1) {
            this.addLoginListner();
        } else if (window.location.href.indexOf('/edit') != -1 || window.location.href.indexOf('/settings') != -1) {
            if (!cookie.read('token')) { window.location.href = '/projects/CMS_v2/login'; }
            this.addLogoutListner();
            if (window.location.href.indexOf('/settings') != -1) {
                this.addSettingsListners();
            }
        } else { console.warn('CMS class not applicable or has not been implemented.'); }
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

    addSettingsListners () {
        $('#changePW').on('click', () => { this.changePW(); });
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
            if (!err) {
                if (res.success) {
                    cookie.delete('token');
                    window.location.href = '/projects/CMS_v2/login';
                } else {

                }
            } else {
                console.log(err);
            }
        });
    }

    changePW () {
        //  TODO:  Write function.
        const newPass = {
            password: window.btoa($('#newPassword').val()),
            confPass: window.btoa($('#newPassConf').val())
        };
        if (newPass.password === newPass.confPass) {
            if (newPass.password.length < 6) {
                if ($('p#msg-text').hasClass('hidden'))
                { $('p#msg-text').removeClass('hidden'); }

                if ($('p#msg-text').hasClass('alert-success'))
                { $('p#msg-text').removeClass('alert-success'); }

                if ($('p#msg-text').hasClass('alert-danger'))
                { $('p#msg-text').removeClass('alert-danger'); }

                if (!$('p#msg-text').hasClass('alert-warning'))
                { $('p#msg-text').addClass('alert-danger'); }
                $('p#msg-text').text('Lösenordet måste vara längre än 6 karaktärer!');
                return;
            }

            AJAX.post('setPW', newPass, (err, res) => {
                if (!err) {
                    if (!res || !res.success) {
                        if ($('p#msg-text').hasClass('hidden'))
                        { $('p#msg-text').removeClass('hidden'); }

                        if ($('p#msg-text').hasClass('alert-success'))
                        { $('p#msg-text').removeClass('alert-success'); }

                        if ($('p#msg-text').hasClass('alert-warning'))
                        { $('p#msg-text').removeClass('alert-warning'); }

                        if (!$('p#msg-text').hasClass('alert-danger'))
                        { $('p#msg-text').addClass('alert-danger'); }
                        $('p#msg-text').text('Lösenordet ej updaterat!');
                    } else {
                        if ($('p#msg-text').hasClass('hidden'))
                        { $('p#msg-text').removeClass('hidden'); }

                        if ($('p#msg-text').hasClass('alert-warning'))
                        { $('p#msg-text').removeClass('alert-warning'); }

                        if ($('p#msg-text').hasClass('alert-danger'))
                        { $('p#msg-text').removeClass('alert-danger'); }

                        if (!$('p#msg-text').hasClass('alert-success'))
                        { $('p#msg-text').addClass('alert-success'); }
                        $('p#msg-text').text('Lösenordet har updaterats!');
                    }
                } else {
                    if ($('p#msg-text').hasClass('hidden'))
                    { $('p#msg-text').removeClass('hidden'); }

                    if ($('p#msg-text').hasClass('alert-success'))
                    { $('p#msg-text').removeClass('alert-success'); }

                    if ($('p#msg-text').hasClass('alert-warning'))
                    { $('p#msg-text').removeClass('alert-warning'); }

                    if (!$('p#msg-text').hasClass('alert-danger'))
                    { $('p#msg-text').addClass('alert-danger'); }
                    $('p#msg-text').text('Fel uppstod, försök igen senare!');
                }
            });
        } else {
            if ($('p#msg-text').hasClass('hidden'))
            { $('p#msg-text').removeClass('hidden'); }

            if ($('p#msg-text').hasClass('alert-success'))
            { $('p#msg-text').removeClass('alert-success'); }

            if ($('p#msg-text').hasClass('alert-danger'))
            { $('p#msg-text').removeClass('alert-danger'); }

            if (!$('p#msg-text').hasClass('alert-warning'))
            { $('p#msg-text').addClass('alert-warning'); }
            $('p#msg-text').text('Lösenorden måste matcha!');
        }
    }

}

$(document).ready(() => { const cms = new UserHandler(); });