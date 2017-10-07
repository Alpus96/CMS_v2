class AdminHandler {
    constructor () {
        if (window.location.href.indexOf('/settings') != -1) {
            this.addAdminSettingsListners();
            this.getUsers();
        }
    }

    addAdminSettingsListners () {
        /*
        *   createUser
        *   changeUserPass
        *   toggleLockedUser
        *   changeUserType
        *   deleteUser
        *   updateDBConfig
        *   newTokenKey
        */
    }

    getUsers () {
        AJAX.post('/projects/CMS_v2/getUsers', null, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    for (let user of res.data) {
                        let rowId = 'tr_'+user.username;
                        let buttonId = 'user_'+user.username;
                        let userRow = '<tr class="'+rowId+' vertical-center"><td class="padding-sm">'+user.username+'</td><td class="padding-sm">'+(user.type === 1 ? 'Admin' : 'Användare')+'</td><td class="padding-sm">'+(user.locked ? '<div class="vertical-center"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></div>' : '')+'</td><td class="padding-xs"><button type="button" class="'+buttonId+' btn btn-default pull-right"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button></td></tr>';
                        $('#usersTable').append(userRow);
                        this.addedEditListner(buttonId, rowId);
                    }
                } else {
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

    addedEditListner (button, row) {
        $('.'+button).on('click', () => {
            //  TODO:  Update DOM and add new listners.
            $('#usersMsg').text('Edit started for '+row.replace('tr_', '')+'.');
            if (!$('#usersMsg').hasClass('alert-success'))
            { $('#usersMsg').addClass('alert-success'); }
            $('#usersMsg').removeClass('hidden');
        })
    }

}

$(document).ready(() => { const cms = new AdminHandler(); });

/*

<tr id="$id" class="text-center">
    <td class="col-xs-3 username">
        <h5>$username</h5>
    </td>
    <td class="col-xs-2 type">
        <h5>$type</h5>
    </td>
    <td class="col-xs-1 locked">
        <div class="form-group">
            <input type="checkbox" value="$locked">
        </div>
    </td>
    <td class="col-xs-2 opt">
        <div class="btn-group">
            <button type="button" class="btn btn-danger edit">
                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
            </button>
        </div>
    </td>
</tr>

<tr class="text-center">
    <td class="col-xs-3">
        <div class="form-group">
            <input type="text" class="form-control" value="$username">
        </div>
    </td>
    <td class="col-xs-3">
        <div class="form-group">
            <input type="password" class="form-control" value="password">
        </div>
    </td>
    <td class="col-xs-2">
        <div class="form-group">
            <select class="form-control type">
                <option value ="1">Admin</option>
                <option value="0">User</option>
            </select>
        </div>
    </td>
    <td class="col-xs-1">
        <div class="form-group">
            <input type="checkbox" value="">
        </div>
    </td>
    <td class="col-xs-2">
        <div class="btn-group">
            <button type="button" class="btn btn-danger">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>

            <button type="button" class="btn btn-success">
                <span class="glyphicon glyphicon-undo" aria-hidden="true"></span>
            </button>

            <button type="button" class="btn btn-success">
                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
            </button>
        </div>
    </td>
</tr>

*/