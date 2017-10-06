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
                        console.log(user);

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
}

$(document).ready(() => { const cms = new AdminHandler(); });

/*

<tr id="$id" class="text-center">
    <td class="col-xs-3 username">
        <h5>$username</h5>
    </td>
    <td class="col-xs-3 password">
        <h5>********</h5>
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