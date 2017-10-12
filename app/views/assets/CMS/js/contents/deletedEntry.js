class DeletedEntry {

    constructor (listId, data) {
        //console.log(Object.keys(data));
        if (typeof data === 'object' && data.hasOwnProperty('ID')  && data.hasOwnProperty('CONTENT_TEXT')  && data.hasOwnProperty('MARKER')) {
            this.data = data;
            this.parentId = listId;
            this.displayEntry();
        } else {
            throw new Error('DeletedEntry was instansed with invalid parameter.');
        }
    }

    displayEntry () {
        const entryInfo = '<tr class="row_' + this.data.ID + '"><td class="col-xs-9">'+this.data.CONTENT_TEXT+'</td><td class="col-xs-2">'+this.data.MARKER+'</td>';
        const restoreBtn = '<td class="text-center col-xs-1"><button class="btn btn-sm btn-primary restore_' + this.data.ID + '"><span class="glyphicon glyphicon-retweet"></span></button></td>';
        const closeRow = '</tr>';

        const entryRow = entryInfo + restoreBtn + closeRow;
        $(this.parentId).append(entryRow);
        $('.restore_' + this.data.ID).on('click', () => { this.confirmRestore(); })
    }

    confirmRestore () {
        msgHelper.newModal('Återställ', 'Är du säker på att du vill återställa inlägg till '+this.data.MARKER+'?', '<div class="btn-group"><button class="btn btn-warning" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button><button class="btn btn-success restore_ok" data-dismiss="modal"><span class="glyphicon glyphicon-ok"></span></button></div>');
        $('.restore_ok').on('click', () => { this.restoreEntry(); });
    }

    restoreEntry () {
        AJAX.post(baseURL+'/restoreDeleted', {id: this.data.ID}, (err, res) => {
            console.log(res);
            if (!err) {
                if (res && res.success) {
                    $('.row_' + this.data.ID).addClass('success');
                    setTimeout(() => { $('.row_' + this.data.ID).remove(); }, 1250);
                } else {
                    $('.row_' + this.data.ID).addClass('warning');
                    setTimeout(() => { $('.row_' + this.data.ID).removeClass('warning'); }, 1250);
                }
            } else {
                $('.row_' + this.data.ID).addClass('danger');
                setTimeout(() => { $('.row_' + this.data.ID).removeClass('danger'); }, 1250);
            }
        });
    }

}