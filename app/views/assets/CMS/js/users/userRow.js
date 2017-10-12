class UserRow {

    constructor (listId, userData) {
        const abort = false;
        if ($(listId).length == 0) {
            console.warn('Entry container not found!');
            abort = true;
        }
        if (!userData) {
            console.warn('userData data not set!');
            this.errroAlert();
            abort = true;
        }

        if (!abort) {
            this.data = userData;
            this.listId = listId;
            this.deleted = false;

            if (userData != 'new') {
                this.rowId = '.tr_' + userData.username;
                $(this.listId).append('<tr class="' + this.rowId.replace('.', '') + '"></tr>');
                this.showRow();
            } else {
                this.rowId = '.tr_' + userData;
                $(this.listId).append('<tr class="' + this.rowId.replace('.', '') + '"></tr>');
                this.newUserRow();
            }
        }
    }

    showRow () {
        if (!this.deleted) {
            const buttonId = 'edit_'+this.data.username;
            const row = '<tr class="' + this.rowId.replace('.', '') + ' vertical-center"><td class="padding-sm">' + this.data.username + '</td><td class="padding-sm">' + (this.data.type == 1 ? 'Admin' : 'Användare') + '</td><td class="padding-sm">' + (this.data.locked == true ? '<div class="vertical-center"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>' : '') + '</td><td class="padding-xs"><button type="button" class="' + buttonId + ' btn btn-default pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td></tr>';

            $(this.rowId).replaceWith(row);

            $('.'+buttonId).off();
            $('.'+buttonId).on('click', () => { this.startEdit(); });
        }
    }

    startEdit () {
        if (!this.deleted) {
            const typeOrder = [
                [ this.data.type == 1 ? '1' : '2',
                    this.data.type == 1 ? 'Admin' : 'Användare' ],
                [ this.data.type == 2 ? '1' : '2',
                    this.data.type == 2 ? 'Admin' : 'Användare' ]
            ];

            const userRowStart = '<tr class="' + this.rowId.replace('.', '') + ' vertical-center">';
            const userNameOpt = '<td class="padding-sm">' + this.data.username + '</td>';
            const typeOpt = '</td><td class="padding-xs"><select class="form-control type_' + this.data.username + '"><option value="' + typeOrder[0][0] + '">' + typeOrder[0][1] + '</option><option value="' + typeOrder[1][0] + '">' + typeOrder[1][1] + '</option></select></td><td class="padding-sm">';
            const userUnlocked = '<input type="checkbox" class="locked_' + this.data.username + '"></td><td class="padding-xs"><div class="btn-group pull-right">';
            const userDeleteOpt = '<button type="button" class="delete_' + this.data.username + ' btn btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>';
            const userAbortOpt = '<button type="button" class="cancel_' + this.data.username + ' btn btn-warning"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>';
            const userSaveOpt = '<button type="button" class="save_' + this.data.username + ' btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button></div></td></tr>';

            const row = userRowStart + userNameOpt + typeOpt + userUnlocked + userDeleteOpt + userAbortOpt + userSaveOpt;
            $(this.rowId).replaceWith(row);

            $('.locked_'+this.data.username).prop('checked', this.data.locked)

            $('.delete_'+this.data.username).off();
            $('.cancel_'+this.data.username).off();
            $('.save_'+this.data.username).off();

            $('.delete_'+this.data.username).on('click', () => { this.confirmDelete(); });
            $('.cancel_'+this.data.username).on('click', () => { this.showRow(); });
            $('.save_'+this.data.username).on('click', () => { this.saveEdit(); });
        }
    }

    saveEdit () {
        const setType = $('.type_'+this.data.username).val();
        const setLocked = $('.locked_'+this.data.username).prop('checked');

        if (setType != this.data.type)
        { this.changeTypeRequest(setType); }
        if (setLocked != this.data.locked)
        { this.toggleLockedRequest(setLocked); }

        this.showRow();
    }

    changeTypeRequest (newType) {
        const data = {
            username: this.data.username,
            type: newType
        };
        AJAX.post(baseURL+'/setUserType', data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    cookie.extendDuration('token');
                    this.data.type = newType;
                    this.showRow();
                    this.requestSuccessful();
                } else { this.requestUnsuccessful(); }
            } else { this.requestFailed(); }
        });
    }

    toggleLockedRequest (locked) {
        const data = { username: this.data.username };
        AJAX.post(baseURL+'/toggleUserLock', data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    cookie.extendDuration('token');
                    this.data.locked = locked;
                    this.showRow();
                    this.requestSuccessful();
                } else { this.requestUnsuccessful(); }
            } else { this.requestFailed(); }
        });
    }

    confirmDelete () {
        msgHelper.newModal('Tabort användare', '<h5>Detta går inte att ångra, är du säker på att du vill ta bort '+this.data.username+' som användare?</h5>', '<div class="btn-group"><button class="delete_cancel btn btn-warning"><span class="glyphicon glyphicon-remove"></span></button><button class="delete_ok btn btn-danger"><span class="glyphicon glyphicon-trash"></span></button></div>');

        $('button.delete_cancel').on('click', () => { msgHelper.removeModal(); });
        $('.delete_ok').on('click', () => {
            msgHelper.removeModal();
            this.deleteUser();
        });
    }

    deleteUser () {
        const data = { username: this.data.username };
        AJAX.post(baseURL+'/deleteUser', data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    cookie.extendDuration('token');
                    this.showRow();
                    this.requestSuccessful();
                    this.deleted = true;
                    this.username = '';
                    setTimeout(() => { $(this.rowId).remove(); }, 1250);
                } else { this.requestUnsuccessful(); }
            } else { this.requestFailed(); }
        });
    }

    requestSuccessful () {
        $(this.rowId).addClass('success');
        setTimeout(() => { $(this.rowId).removeClass('success'); }, 1250);
    }

    requestUnsuccessful () {
        $(this.rowId).addClass('danger');
        setTimeout(() => { $(this.rowId).removeClass('danger'); }, 1250);
    }

    requestFailed (callToSelf = 0) {
        console.log(callToSelf);
        $(this.rowId).addClass('danger');
        setTimeout(() => {
            $(this.rowId).removeClass('danger');
            setTimeout(() => {
                if (callToSelf < 2)
                { this.requestFailed(++callToSelf); }
            }, 50);
        }, 100);
    }
}