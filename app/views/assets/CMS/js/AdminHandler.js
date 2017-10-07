class AdminHandler {
    constructor () {
        if (window.location.href.indexOf('/settings') != -1) {
            $('#usersTab').on('click', () => { this.listUsers(); });
            $('#createUserbtn').on('click', () => { this.newUser(); });
            $('.cancel_new').on('click', () => { this.userRow('tr_new'); });
            $('.ok_new').on('click', () => { this.newUserRequest('tr_new'); });
        }
    }

    newUser () {
        $('#createUserbtn').addClass('hidden');
        this.users['new'] = {username: undefined, type: undefined, locked: undefined};
        const rowId = 'tr_new';
        const userRow = '<tr class="'+rowId+' vertical-center"></tr>';
        $('#usersTable').append(userRow);
        this.startEdit(rowId);
    }

    listUsers () {
        this.users = {};
        AJAX.post('/projects/CMS_v2/getUsers', null, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    $('#usersTable').html('');
                    for (let user of res.data) {
                        this.users[user.username] = user;
                        let rowId = 'tr_'+user.username;
                        let userRow = '<tr class="'+rowId+' vertical-center"></tr>';
                        $('#usersTable').append(userRow);
                        this.userRow(rowId);
                    }
                } else {
                    $('#usersTable').html('');
                    $('#usersMsg').text('Inga användare hittades.');

                    if (!$('#usersMsg').hasClass('alert-warning'))
                    { $('#usersMsg').addClass('alert-warning'); }

                    if ($('#usersMsg').hasClass('alert-danger'))
                    { $('#usersMsg').removeClass('alert-danger'); }

                    if ($('#usersMsg').hasClass('hidden'))
                    { $('#usersMsg').removeClass('hidden'); }
                }
            } else {
                $('#usersMsg').text('Gick inte att hämta användare.');

                if ($('#usersMsg').hasClass('alert-warning'))
                { $('#usersMsg').removeClass('alert-warning'); }

                if (!$('#usersMsg').hasClass('alert-danger'))
                { $('#usersMsg').addClass('alert-danger'); }

                if ($('#usersMsg').hasClass('hidden'))
                { $('#usersMsg').removeClass('hidden'); }
            }
        });
    }

    startEdit (row) {
        for (let user in this.users) {
            if (user != 'new') {
                this.userRow('tr_'+user);
            }
        }
        const username = row.replace('tr_', '');
        const typeOrder = [
            [
                this.users[username].type==1||this.users[username].type===undefined?'1':'2',
                this.users[username].type==1||this.users[username].type===undefined?'Admin':'Användare'
            ],
            [
                this.users[username].type==2?'1':'2',
                this.users[username].type==2?'Admin':'Användare'
            ]
        ];
        const userRowStart = '<tr class="'+row+' vertical-center">'+(username=='new'?'<td class="padding-xs"><input type="text" class="form-control username_'+username+'" value="'+username+'">':'<td class="padding-sm">'+username+'</td>')+'</td><td class="padding-xs"><select class="form-control type_'+username+'"><option value="'+typeOrder[0][0]+'">'+typeOrder[0][1]+'</option><option value="'+typeOrder[1][0]+'">'+typeOrder[1][1]+'</option></select></td><td class="padding-sm">';
        const newUserUnlocked = username==='new'?'<td class="padding-xs"><div class="btn-group pull-right">':'<input type="checkbox" class="locked_'+username+'"></td><td class="padding-xs"><div class="btn-group pull-right">';
        const userRowUserExists = username==='new'?'':'<button type="button" class="delete_'+username+' new_userBtn btn btn-danger" data-toggle="modal" data-target="#deleteWarning"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
        const userRowMid = '<button type="button" class="cancel_'+username+' btn btn-warning"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';
        const userRowIsNew = username==='new'?'<button type="button" class="new_userBtn btn btn-success" data-toggle="modal" data-target="#newUserPass"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button>':'<button type="button" class="save_'+username+' btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button></div></td></tr>';
        const userRow = userRowStart + newUserUnlocked + userRowUserExists + userRowMid + userRowIsNew;

        $('.'+row).replaceWith(userRow);
        $('.locked_'+username).prop('checked', this.users[username].locked?true:false);

        if (userRowUserExists != '')
        { $('.delete_'+username).on('click', () => { this.delete(row) }); }

        $('.cancel_'+username).on('click', () => { this.userRow(row) });
        if (username != 'new') {
            $('.save_'+username).on('click', () => { this.save(row) });
        }
    }

    userRow (row) {
        if (!$('#usersMsg').hasClass('hidden'))
        { $('#usersMsg').addClass('hidden'); }

        const username = row.replace('tr_', '');
        if (username === 'new') {
            $('.'+row).remove();
            $('#createUserbtn').removeClass('hidden');
            return;
        }
        const buttonId = 'user_'+username;
        const userRow = '<tr class="'+row+' vertical-center"><td class="padding-sm">'+this.users[username].username+'</td><td class="padding-sm">'+(this.users[username].type == 1 ? 'Admin' : 'Användare')+'</td><td class="padding-sm">'+(this.users[username].locked ? '<div class="vertical-center"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>' : '')+'</td><td class="padding-xs"><button type="button" class="'+buttonId+' btn btn-default pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td></tr>';
        $('.'+row).replaceWith(userRow);
        $('.'+buttonId).on('click', () => { this.startEdit(row, this.users[username].type, this.users[username].locked); });
    }

    save (row) {
        const username = row.replace('tr_', '');

        const setType = $('.type_'+username).val();
        const setLocked = $('.locked_'+username).prop('checked');

        if (setType != this.users[username].type)
        { this.changeTypeRequest(username, setType); }
        if (setLocked != this.users[username].locked)
        { this.toggleLockedRequest(username, setLocked); }

        this.userRow(row);
    }

    newUserRequest (rowName) {
        const new_pass = $('.newPassword').val();
        $('.newPassword').val('');

        const new_confpass = $('.newPassConf').val();
        $('.newPassConf').val('');
        const newUser = {
            username: $('.username_new').val(),
            password: new_pass,
            type: $('.type_new').val(),
        };

        if (newUser.username === 'new') {
            $('#usersMsg').text('Reserverat namn "new"!');
            if (!$('#usersMsg').hasClass('alert-warning'))
            { $('#usersMsg').addClass('alert-warning') }
            if ($('#usersMsg').hasClass('hidden'))
            { $('#usersMsg').removeClass('hidden') }
            setTimeout(() => {
                if (!$('#usersMsg').hasClass('hidden'))
                { $('#usersMsg').addClass('hidden') }
            }, 3000);
            return;
        }
        if (newUser.password.length < 6) {
            $('#usersMsg').text('Lösenord måste vara minst 6 tecken!');
            if (!$('#usersMsg').hasClass('alert-warning'))
            { $('#usersMsg').addClass('alert-warning') }
            if ($('#usersMsg').hasClass('hidden'))
            { $('#usersMsg').removeClass('hidden') }
            setTimeout(() => {
                if (!$('#usersMsg').hasClass('hidden'))
                { $('#usersMsg').addClass('hidden') }
            }, 3000);
            return;
        }
        if (!new_pass === new_confpass) {
            $('#usersMsg').text('Lösenorden måste matcha!');
            if (!$('#usersMsg').hasClass('alert-warning'))
            { $('#usersMsg').addClass('alert-warning') }
            if ($('#usersMsg').hasClass('hidden'))
            { $('#usersMsg').removeClass('hidden') }
            setTimeout(() => {
                if (!$('#usersMsg').hasClass('hidden'))
                { $('#usersMsg').addClass('hidden') }
            }, 3000);
            return;
        }

        for (let user in this.users) {
            if (newUser.username === user) {
                $('#usersMsg').text('Användarnamn måste vara unikt!');
                if (!$('#usersMsg').hasClass('alert-warning'))
                { $('#usersMsg').addClass('alert-warning') }
                if ($('#usersMsg').hasClass('hidden'))
                { $('#usersMsg').removeClass('hidden') }
                setTimeout(() => {
                    if (!$('#usersMsg').hasClass('hidden'))
                    { $('#usersMsg').addClass('hidden') }
                }, 3000);
                return;
            }
        }

        AJAX.post('/projects/CMS_v2/newUser', newUser, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    delete newUser.password;
                    this.users[newUser.username] = newUser;
                    console.log(this.users);
                    this.listUsers(rowName);
                    $('#createUserbtn').removeClass('hidden');
                    if (!$('.'+rowName).hasClass('success')) {
                        $('.'+rowName).addClass('success');
                        setTimeout(() => { $('.'+rowName).removeClass('success'); }, 1250);
                    }
                } else {
                    if (!$('.'+rowName).hasClass('danger')) {
                        $('.'+rowName).addClass('danger');
                        setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 1250);
                    }
                }
            } else {
                if (!$('.'+rowName).hasClass('danger')) {
                    $('.'+rowName).addClass('danger');
                    setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 3000);
                }
            }
        });
    }

    changeTypeRequest (username, type) {
        const rowName = 'tr_'+username;
        const data = {
            username: username,
            type: type
        };

        AJAX.post('/projects/CMS_v2/setUserType', data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    this.users[username].type = type;
                    this.userRow(rowName);
                    if (!$('.'+rowName).hasClass('success')) {
                        $('.'+rowName).addClass('success');
                        setTimeout(() => { $('.'+rowName).removeClass('success'); }, 1250);
                    }
                } else {
                    if (!$('.'+rowName).hasClass('danger')) {
                        $('.'+rowName).addClass('danger');
                        setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 1250);
                    }
                }
            } else {
                if (!$('.'+rowName).hasClass('danger')) {
                    $('.'+rowName).addClass('danger');
                    setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 3000);
                }
            }
        });
    }

    toggleLockedRequest (username, locked) {
        const rowName = 'tr_'+username;
        const data = { username: username };

        AJAX.post('/projects/CMS_v2/toggleUserLock', data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    this.users[username].locked = locked;
                    this.userRow(rowName);
                    if (!$('.'+rowName).hasClass('success')) {
                        $('.'+rowName).addClass('success');
                        setTimeout(() => { $('.'+rowName).removeClass('success'); }, 1250);
                    }
                } else {
                    if (!$('.'+rowName).hasClass('danger')) {
                        $('.'+rowName).addClass('danger');
                        setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 1250);
                    }
                }
            } else {
                if (!$('.'+rowName).hasClass('danger')) {
                    $('.'+rowName).addClass('danger');
                    setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 3000);
                }
            }
        });
    }

    delete (row) {
        $('.ok_delete').off();
        $('.ok_delete').on('click', () => { this.deleteUserRequest(row); });
    }

    deleteUserRequest (rowName) {
        const username = rowName.replace('tr_', '');
        const data = { username: username };
        AJAX.post('/projects/CMS_v2/deleteUser', data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    this.userRow(rowName);
                    if (!$('.'+rowName).hasClass('success')) {
                        $('.'+rowName).addClass('success');
                        setTimeout(() => { $('.'+rowName).removeClass('success'); }, 1250);
                    }
                    delete this.users[username];
                    setTimeout(() => { this.listUsers(); }, 1300);
                } else {
                    if (!$('.'+rowName).hasClass('danger')) {
                        $('.'+rowName).addClass('danger');
                        setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 1250);
                    }
                }
            } else {
                if (!$('.'+rowName).hasClass('danger')) {
                    $('.'+rowName).addClass('danger');
                    setTimeout(() => { $('.'+rowName).removeClass('danger'); }, 3000);
                }
            }
        });
    }

}

$(document).ready(() => { const cms = new AdminHandler(); });