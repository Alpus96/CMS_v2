class AdminHandler {
    constructor () {
        if (window.location.href.indexOf('/settings') != -1) {
            this.addAdminSettingsListners();
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
}

$(document).ready(() => { const cms = new AdminHandler(); });