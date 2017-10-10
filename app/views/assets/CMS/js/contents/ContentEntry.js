class ContentEntry {

    constructor (containerId, entryObject, containerInfo) {
        this.abort = false;
        if ($(containerId).length == 0) {
            console.warn('Entry container not found!');
            this.abort = true;
        }
        if (!entryObject) {
            console.warn('Entry data not set!');
            this.errroAlert();
            this.abort = true;
        }

        if (!this.abort) {
            this.parentID = containerId;
            this.data = entryObject;
            this.data.created = new Date(this.data.created);
            this.data.edited = new Date(this.data.edited);
            this.containerInfo = containerInfo;
            this.parseWidth(containerInfo.entryWidth);

            $(this.parentID).prepend('<div class="entry_'+this.data.id+'"></div>');
            this.displayContent();
        }
    }

    parseWidth (width) {
        //  should be anything xs/sm/md/lg/xl + (-) + 1-12
        if (width) {
            const ws = width.split('-');
            if (ws.length == 2) {
                this.width = ws[0]+'-'+ws[1];
            }
        }
        else { this.width = false; }
    }

    displayContent () {
        //  NOTE:  this.data = {id, entryContent, (auhtor), (posted), (lastEdited)}
        let dateString = '';
        if (this.containerInfo.incDate && this.data.created != 'Invalid Date') {
            dateString = this.data.created.toLocaleDateString();
            if (this.data.edited != 'Invalid Date') {
                dateString += ' ('+this.data.edited.toLocaleDateString()+')';
            }
        }
        const authString = this.containerInfo.incAuth && this.data.author ? this.data.author : '';

        const name = 'entry_'+this.data.id;
        const entryString = '<div class="col-'+this.width+' '+name+'">'+this.data.text+'<p class="pull-right text-right"><small>'+authString+'</small><br><small>'+dateString+'</p></div>';

        $('.'+name).replaceWith(entryString);
        if (cookie.read('token')) {
            $('.'+name).on('click', () => { this.startEdit(); });
        }
    }

    startEdit () {
        //  TODO: Get as MD.
        const name = 'entry_'+this.data.id;
        const entryString = '<div class="col-'+this.width+' '+name+'"><textarea class="form-control editArea">'+this.data.text+'</textarea><div class="top-margin-sm pull-right btn-group"><button class="btn btn-danger edit_delete"><span class="glyphicon glyphicon-trash"></span></button><button class="btn btn-warning edit_abort"><span class="glyphicon glyphicon-remove"></span></button><button class="btn btn-success edit_save"><span class="glyphicon glyphicon-ok"></span></button></div></div>';
        $('.'+name).replaceWith(entryString);
        $('.edit_delete').on('click', () => { this.removeEntry(); });
        $('.edit_abort').on('click', () => { this.displayContent(); });
        $('.edit_save').on('click', () => { this.saveEdit(); });
    }

    saveEdit () {
        console.log('Saving...');
    }

    removeEntry () {
        console.log('Deleting...');
    }

    errroAlert () {
        const id = 'entry_error_' + Math.random()*1000;
        $(this.parentID).prepend('<div class="'+id+'"></div>');
        msgHelper.alert(id, 'Ingen data!', 'danger');
    }

}