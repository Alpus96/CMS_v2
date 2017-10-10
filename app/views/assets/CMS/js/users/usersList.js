class UsersList {

    constructor () {
        if (window.location.href.indexOf('/settings') != -1 && cookie.read('token')) {
            $('#usersTab').on('click', () => { this.listUsers(); });
            $('#createUserbtn').on('click', () => { this.newUser(); });
        }
    }

    listUsers () {
        AJAX.post(baseURL+'/getUsers', {}, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    this.users = [];
                    $('#usersTable').html('');
                    for (let user in res.data) {
                        this.users.push(new UserRow('#usersTable', res.data[user]));
                    }
                } else { msgHelper.alert('#usersMsg', 'Inga användare hittades.', 'warning'); }
            } else { msgHelper.alert('#usersMsg', 'Kunde inte hämta användare.', 'danger'); }
        });
    }

    newUser () {
        this.listUsers();
        msgHelper.newModal('Ny användare', '<div class="col-xs-12"></div><div class="form-group"><input class="form-control new_username" type="text" name="" placeholder="Användarnamn" autofocus></div><div class="form-group"><select class="form-control new_type" name=""><option value="2">Användare</option><option value="1">Admin</option></select></div><div class="form-group"><input class="form-control new_password" type="password" name="" placeholder="Nytt lösenord"></div><div class="form-group"><input class="form-control new_passConf" type="password" name="" placeholder="Bekräfta lösenord"></div><p class="hidden newUserAlert"></p>', '<div class="btn-group"><button type="button" class="new_cancel btn btn-warning" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button><button type="button" class="new_ok btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button></div>');
        $('.new_cancel').on('click', () => { msgHelper.removeModal(); });
        $('.new_ok').on('click', () => { this.saveNewUser(); });
    }

    saveNewUser () {
        const new_password = $('input.new_password').val();
        const new_passConf = $('input.new_passConf').val();

        const newUser = {
            username: $('.new_username').val(),
            password: new_password,
            type: $('.new_type').val(),
        };

        if (newUser.username.indexOf(' ') === -1) {
            if (new_password.length >= 6) {
                if (new_password === new_passConf) {
                    for (let user of this.users) {
                        if (newUser.username === user.data.username) {
                            msgHelper.alert('.newUserAlert', 'Användarnamn måste vara unikt!', 'warning', 3000);
                            return;
                        }
                    }
                    AJAX.post(baseURL+'/newUser', newUser, (err, res) => {
                        if (!err) {
                            if (res && res.success) {
                                msgHelper.removeModal();
                                this.listUsers();
                                const rowName = '.tr_'+newUser.username;
                                $(rowName).addClass('success')
                                setTimeout(() => { $(rowName).removeClass('success'); }, 1250);
                            } else {
                                msgHelper.alert('.newUserAlert', 'Användarnamn måste vara unikt!', 'warning', 3000);
                            }
                        } else {
                            msgHelper.alert('.newUserAlert', 'Kunde inte skapa ny användare, försök igen senare!', 'danger', 3000);
                        }
                    });

                } else {
                    msgHelper.alert('.newUserAlert', 'Lösenorden måste matcha!', 'warning', 3000);
                }
            } else {
                msgHelper.alert('.newUserAlert', 'Lösenord måste vara minst 6 tecken!', 'warning', 3000);
            }
        } else { msgHelper.alert('.newUserAlert', 'Användarnamn får inte innehålla mellanslag!', 'warning', 3000); }
    }

}

$(document).ready(() => { const cms = new UsersList(); });