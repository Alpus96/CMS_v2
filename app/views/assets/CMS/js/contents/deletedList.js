class DeletedList {

    constructor () {
        if (window.location.href.indexOf('/settings') != -1 && cookie.read('token')) {
            this.loadDeleted();
            $('#contentsTab').on('click', () => { this.loadDeleted(); })
        }
    }

    loadDeleted () {
        AJAX.post(baseURL+'/getDeleted', {}, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    this.rows = [];
                    for (let deleted of res.data) {
                        this.rows.push(new DeletedEntry('#deletedList', deleted));
                    }
                } else {msgHelper.alert('#deletedMsg', 'Inget borttaget innehåll.', 'warning', 10000);}
            } else { msgHelper.alert('#deletedMsg', 'Kunde inte ladda borttaget innehåll.', 'danger', 10000); }
        });
    }
}

$(document).ready(() => { const deletedList = new DeletedList(); });