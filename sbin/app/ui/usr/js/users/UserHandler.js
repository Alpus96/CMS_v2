class UserHandler {

    constructor () {
        if (window.location.href.indexOf('/login') == -1 && !cookie.read('token')) { window.location.href = baseURL+'/login';
        }

        if (window.location.href.indexOf('/login') != -1) {
            if (cookie.read('token')) {
                cookie.extendDuration('token');
                window.location.href = baseURL+'/edit';
            } else {
                $('form#login').submit('submit', (event) => {
                    event.preventDefault();
                    this.loginRequest();
                });
            }
        } else if (window.location.href.indexOf('/edit') != -1) {
            cookie.extendDuration('token');
            $('#logout').on('click', () => { this.logoutRequest(); });
        } else if (window.location.href.indexOf('/settings') != -1) {
            cookie.extendDuration('token');
            $('#accountTab').on('click', () => { this.showAuthName(); });
            $('#logout').on('click', () => { this.logoutRequest(); });
            $('#changePW').on('click', () => { this.confirmPass('password'); });
            $('#changeAuthName').on('click', () => { this.confirmPass('authName'); });
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
        AJAX.post(baseURL+'/logout', cookie.read('token'), (err, res) => {
            if (!err) {
                if (res.success) {
                    cookie.delete('token');
                    window.location.href = baseURL+'/login';
                }
            }
        });
    }

    confirmPass (forX) {
        msgHelper.newModal('Bekräfta', '<div class="form-group"><input class="form-control" type="password" id="password" placeholder="Bekräfta med lösenord"></div>', '<div class="btn-group"><button class="btn btn-warning confirm_cancel" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button><button class="btn btn-success confirm_ok" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></button></div>');

        setTimeout(() => { $('#password').focus(); }, 200);
        $('.confirm_ok').on('click', () => {
            const password = window.btoa($('#password').val())
            if (forX === 'password') {
                this.changePW(password);
            } else if (forX === 'authName') {
                this.changeAuthorName(password);
            }
        });
    }

    changePW (password) {
        const newPass = {
            password: window.btoa($('#newPassword').val()),
            confPass: window.btoa($('#newPassConf').val())
        };
        if (newPass.password !== newPass.confPass) {
            msgHelper.alert('p#msg-text', 'Lösenorden måste matcha!', 'warning', 3000);
            $('#newPassword').val('');
            $('#newPassConf').val('');
        } else {
            if (newPass.password.length < 6) {
                msgHelper.alert('p#msg-text', 'Lösenordet måste vara längre än 6 karaktärer!', 'warning', 3000);
                return;
            }

            AJAX.post(baseURL+'/setPW', {password: password, newPass: newPass.password}, (err, res) => {
                if (!err && res) {
                    if (res.success) {
                        msgHelper.alert('p#msg-text', 'Lösenordet har updaterats!', 'success', 3000);

                        cookie.create('token', res.data);
                        $('#newPassword').val('');
                        $('#newPassConf').val('');
                    } else {
                        msgHelper.alert('p#msg-text', 'Fel lösenord!', 'danger', 3000);
                    }
                } else {
                    msgHelper.alert('p#msg-text', 'Uppdatering misslyckades!', 'danger', 3000);
                }
            });
        }
    }

    showAuthName () {
        AJAX.post(baseURL+'/getAuthName', {}, (err, res) => {
            if (!err && res) {
                if (res.success) {
                    cookie.extendDuration('token');
                    this.authName = res.data;
                    $('#authName').val(res.data);
                } else {
                    msgHelper.alert('p#msg-text-name', 'Inget namn satt!', 'warning');
                }
            } else {
                msgHelper.alert('p#msg-tex-name', 'Fel uppstod!', 'danger', 30000);
            }
        });
    }

    changeAuthorName (password) {
        const newName = $('#authName').val();
        if (newName !== this.authName) {
            AJAX.post(baseURL+'/setAuthName', {authName: newName, password: password}, (err, res) => {
                if (!err && res) {
                    if (res.success) {
                        cookie.create('token', res.data);
                        this.authName = newName;
                        msgHelper.alert('p#msg-text-name', 'Förfatarnamn uppdaterat!', 'success', 3000);
                    } else {
                        msgHelper.alert('p#msg-text-name', 'Fel lösenord!', 'danger', 3000);
                    }
                } else {
                    msgHelper.alert('p#msg-tex-name', 'Uppdatering misslyckades!', 'danger', 30000);
                }
            });
        } else {
            msgHelper.alert('p#msg-text-name', 'Du måste ange ett nytt namn!', 'warning', 3000);
        }
    }

}

$(document).ready(() => { const cms = new UserHandler(); });