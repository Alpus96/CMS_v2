class LogoutHandler {
    constructor () {
        if (!cookie.read('token')) { window.location.href = '/projects/CMS_v2/login'; }
        
        this.buttonHTML = '<button class="btn btn-default pull-right logout_button" id="logout">Logout</button>';
        this.addButton();
        this.addListener();
    }

    addButton () { $('.container').prepend(this.buttonHTML); }

    addListener () { $('#logout').on('click', this.logoutRequest); }

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

$(document).ready( () => { const logutHandler = new LogoutHandler(); } );