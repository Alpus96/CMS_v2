//let baseURL = '/projects/CMS_v2';

class UserHandler {

    constructor () {
        if (window.location.href.indexOf('/login') == -1 && !cookie.read('token'))
        { window.location.href = baseURL+'/login'; }

        if (window.location.href.indexOf('/login') != -1) {
            $('form#login').submit('submit', (event) => {
                event.preventDefault();
                this.loginRequest();
            });
        }

        else if (window.location.href.indexOf('/edit') != -1)
        { $('button#logout').on('click', () => { this.logoutRequest(); }); }

        else if (window.location.href.indexOf('/settings') != -1) {
            $('button#logout').on('click', () => { this.logoutRequest(); });
            $('#changePW').on('click', () => { this.changePW(); });
        }
    }

    loginRequest () {
        //  NOTE: window.btoa base64 encodes strings to not send in clear text.
        const credentials = {
            username : window.btoa($('#username').val()),
            password : window.btoa($('#password').val())
        };

        AJAX.post(baseURL+'/login', credentials, (err, res) => {
            if (!err && res) {
                if (res.success) {
                    cookie.create('token', res.token);
                    window.location.href = baseURL+'/edit';
                } else {
                    msgHelper.alert('p#msg-text', 'Fel användarnamn eller lösenord!', 'warning', 3000);
                }
            } else {
                msgHelper.alert('p#msg-text', 'Fel uppstod, försök igen senare!', 'danger', 3000);
            }
        });
    }

    logoutRequest () {
        //  TODO:  Decide what to do if logout does not work.
        AJAX.post(baseURL+'/logout', cookie.read('token'), (err, res) => {
            if (!err) {
                if (res.success) {
                    cookie.delete('token');
                    window.location.href = baseURL+'/login';
                } else {
                    //msgHelper.alert('', 'Fel uppstod!', 'danger', 3000);
                }
            } else {
                //msgHelper.alert('', 'Fel uppstod!', 'danger', 3000);
            }
        });
    }

    changePW () {
        const newPass = {
            password: window.btoa($('#newPassword').val()),
            confPass: window.btoa($('#newPassConf').val())
        };
        if (!newPass.password === newPass.confPass) {
            msgHelper.alert('p#msg-text', 'Lösenorden måste matcha!', 'warning', 3000);
            $('#newPassword').val('');
            $('#newPassConf').val('');
        } else {
            if (newPass.password.length < 6) {
                msgHelper.alert('p#msg-text', 'Lösenordet måste vara längre än 6 karaktärer!', 'warning', 3000);
                return;
            }

            AJAX.post('/setPW', newPass, (err, res) => {
                if (!err && res) {
                    if (res.success) {
                        msgHelper.alert('p#msg-text', 'Lösenordet har updaterats!', 'success', 3000);

                        $('#newPassword').val('');
                        $('#newPassConf').val('');
                    } else {
                        msgHelper.alert('p#msg-text', 'Lösenordet ej updaterat!', 'danger', 3000);
                    }
                } else {
                    msgHelper.alert('p#msg-text', 'Fel uppstod!', 'danger', 3000);
                }
            });
        }
    }

}

$(document).ready(() => { const cms = new UserHandler(); });