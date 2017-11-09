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

            $(this.parentID).append('<div class="entry_'+this.data.id+'"></div>');
            this.displayContent();
        }
    }

    parseWidth (width) {
        //  should be anything xs/sm/md/lg/xl + (-) + 1-12
        if (width) {
            const ws = width.indexOf() ? width.split('-') : false;
            if (ws) {
                let wStr = '';
                for (let i = 0; i < ws.length; i++) {
                    wStr += 'col-'+ws[i]+'-'+ws[++i]+' ';
                }
                this.width = wStr;
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
        const entryString = '<div class="'+this.width+name+'">'+this.data.text+'<p class="pull-right text-right"><small>'+authString+'</small><br><small>'+dateString+'</p></div>';

        $('.'+name).replaceWith(entryString);
        if (cookie.read('token') && window.location.href.indexOf('/edit') != -1)
        { $('.'+name).on('click', () => { this.startEdit(); }); }
    }

    startEdit () {
        const name = 'entry_'+this.data.id;
        AJAX.post(baseURL+'/getMD', {id: this.data.id}, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    cookie.extendDuration('token');
                    const entryString = '<div class="'+this.width+name+'"><textarea class="form-control editArea edit_'+this.data.id+'">'+res.data+'</textarea><div class="top-margin-sm pull-left"><a href="#" data-toggle="tooltip" title="'+cs+'"><span class="glyphicon glyphicon-question-sign"></span></a></div><div class="top-margin-sm pull-right btn-group"><button class="btn btn-danger edit_delete"><span class="glyphicon glyphicon-trash"></span></button><button class="btn btn-warning edit_abort"><span class="glyphicon glyphicon-remove"></span></button><button class="btn btn-success edit_save"><span class="glyphicon glyphicon-ok"></span></button></div></div>';
                    $('.'+name).replaceWith(entryString);

                    const area = $('textarea.editArea');
                    area.height(0);
                    area.height(area[0].scrollHeight+parseInt(area.css('font-size')));
                    area.keyup(() => {
                        area.height(0);
                        area.height(area[0].scrollHeight+parseInt(area.css('font-size')));
                    });

                    $('.edit_delete').on('click', () => { this.removeEntry(); });
                    $('.edit_abort').on('click', () => { this.displayContent(); });
                    $('.edit_save').on('click', () => { this.saveEdit(); });
                } else { this.requestDenied(); }
            } else { this.requestFailed(); }
        });

    }

    saveEdit () {
        const name = 'entry_'+this.data.id;
        const newText = $('.edit_'+this.data.id).val();
        if (newText != '') {
            AJAX.post(baseURL+'/updateContents', {id: this.data.id, newText: newText}, (err, res) => {
                if (!err) {
                    if (res && res.data) {
                        cookie.extendDuration('token');
                        this.data.text = res.data;
                        this.displayContent();
                    } else { this.requestDenied(); }
                } else { this.requestFailed(); }
            });
        } else {
            msgHelper.newModal('Fel uppstod!', '<h5>Kan inte spara ett tomt inlägg!</h5>', '<button class="btn btn-warning" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>');
        }
    }

    removeEntry () {
        AJAX.post(baseURL+'/deleteContents', {id: this.data.id}, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    cookie.extendDuration('token');
                    $('.entry_'+this.data.id).replaceWith('<div class="deleted"></div>');
                } else { this.requestDenied(); }
            } else { this.requestFailed(); }
        });
    }

    requestDenied () {
        if ($('.error_'+this.data.id).length == 0) {
            $('.entry_'+this.data.id).append('<p class="text-center top-margin-xl error_'+this.data.id+'"></p>')
        }
        msgHelper.alert('.error_'+this.data.id, 'Gick inte att uppdatera!', 'warning', 3000);
    }

    requestFailed () {
        if ($('.error_'+this.data.id).length == 0) {
            $('.entry_'+this.data.id).append('<p class="text-center top-margin-xl error_'+this.data.id+'"></p>')
        }
        msgHelper.alert('.error_'+this.data.id, 'Gick inte att skicka förfrågan!', 'danger', 3000);
    }

}