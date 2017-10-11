class ContentContainer {

    constructor (id) {
        if (!id) { console.warn('Invalid content container id \'undefined\'!'); }
        if (typeof $ == 'undefined') { console.warn('jQuery undefined!'); }

        if (id && $) {
            this.id = id;
            this.getOptions();

            if (this.data.marker != '' && this.data.amount != -1) {
                this.entries = [];
                this.loadContent();
            } else {
                console.warn('Insufficient data! ("'+this.id+'")');
                $(this.id).append('<p class="text-center alert '+id.replace('#', '')+'"></p>');
                msgHelper.alert(this.id.replace('#', '.'), 'Otillräcklig information!', 'warning');
            }
        }
    }

    getOptions () {
        let options = $(this.id).attr('class').split(/\s+/);
        this.data = {
            marker: '',
            amount: -1,
            offset: 0,
            incAuth: false,
            incDate: false
        };
        for (let opt of options) {
            if (opt.indexOf('marker_') != -1) {
                const marker = opt.replace('marker_', '');
                this.data.marker = typeof marker == 'string' ? marker : '';
            } else if (opt.indexOf('amount_') != -1) {
                const amount = opt.replace('amount_', '');
                this.data.amount = isNaN(amount) ? 0 : parseInt(amount);
            } else if (opt.indexOf('incAuth_') != -1) {
                const incAuth = opt.replace('incAuth_', '');
                this.data.incAuth = incAuth && incAuth != 'false' ? true : false;
            } else if (opt.indexOf('incDate_') != -1) {
                const incDate = opt.replace('incDate_', '');
                this.data.incDate = incDate && incDate != 'false' ? true : false;
            } else if (opt.indexOf('entryWidth_') != -1) {
                const entryWidth = opt.replace('entryWidth_', '');
                this.data.entryWidth = entryWidth;
            }
        }
    }

    loadContent () {
        $(this.id).html('');
        if (cookie.read('token') && window.location.href.indexOf('/edit') != -1)
        { this.newEntryButton(); }
        let entryCount = 0;
        AJAX.post(baseURL+'/getContentsByMarker', this.data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    for (let entry of res.data) {
                        this.entries.push(new ContentEntry(this.id, entry, this.data));
                        entryCount++;
                    }
                } else {
                    $(this.id).append('<div class="clearfix"></div><p class="top-margin-lg text-center alert '+this.id.replace('#', '')+'"></p>');
                    msgHelper.alert(this.id.replace('#', '.'), 'Inga inlägg.', 'info');
                }
            } else {
                $(this.id).append('<div class="clearfix"></div><p class="top-margin-lg text-center alert '+this.id.replace('#', '')+'"></p>');
                msgHelper.alert(this.id.replace('#', '.'), 'Kunde inte hämta inlägg.', 'danger');
            }
            console.log(this.data + ' : ' + entryCount);
            //  TODO: Know if there is contents for next page.
            if (entryCount == this.data.amount || this.data.offset != 0)
            { this.addPageButtons(); }
        });

    }

    addPageButtons () {
        //  TODO:  Figure if to add both as clickable.
        const buttonNextId = this.id.replace('#', '')+'_next';
        const buttonPrevId = this.id.replace('#', '')+'_prev';
        $(this.id).append('<div class="top-margin-lg col-xs-12 text-center"><div class="btn-group"><button class="btn btn-primary '+buttonPrevId+'"><span class="glyphicon glyphicon-chevron-left"></span></button><button class="btn btn-primary '+buttonNextId+'"><span class="glyphicon glyphicon-chevron-right"></span></button></div></div>');
        $('.'+buttonNextId).off();
        $('.'+buttonNextId).on('click', () => { this.nextPage(); });
        $('.'+buttonPrevId).off();
        $('.'+buttonPrevId).on('click', () => { this.previousPage(); });
    }

    nextPage () {
        this.data.offset = this.data.offset + this.data.amount;
        this.loadContent();
    }

    previousPage () {
        this.data.offset = this.data.offset - this.data.amount;
        this.loadContent();
    }

    newEntryButton () {
        const name = this.id.replace('#', '')+'_button';
        const button = '<div class="col-xs-12 text-center bottom-margin-lg"><button class="' + name + ' btn btn-primary"><span class="glyphicon glyphicon-plus"></span></button></div>';
        $(this.id).prepend(button);
        $('.'+name).on('click', () => { this.startNewEntry(); });
    }

    startNewEntry () {
        msgHelper.newModal('Nytt inlägg', '<div class="col-xs-12"></div><div class="form-group"><textarea class="form-control editArea newEntry_text" name="" placeholder="Innehåll..." autofocus></textarea></div><p class="hidden newEntryAlert"></p>', '<div class="btn-group"><button type="button" class="newEntryCancel btn btn-warning" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button><button type="button" class="newEntrySave btn btn-success" data-dismiss="modal"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button></div>');

        const area = $('textarea.editArea');
        area.height(0);
        area.height(area[0].scrollHeight+parseInt(area.css('font-size'))+60);
        area.keyup(() => {
            area.height(0);
            area.height(area[0].scrollHeight+parseInt(area.css('font-size')));
        });

        $('.newEntryCancel').on('click', () => { msgHelper.removeModal(); });
        $('.newEntrySave').on('click', () => {
            this.saveNewEntry();
        });
    }

    saveNewEntry () {
        const newEntry = $('.newEntry_text').val();
        if (newEntry == '') {
            msgHelper.removeModal();
            setTimeout(() => {
                msgHelper.newModal('Fel uppstod!', '<h5>Kan inte spara ett tomt inlägg!</h5>', '<button class="btn btn-warning" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>');
            }, 210);
        } else {
            const data = {
                text: newEntry,
                marker: this.data.marker
            };
            AJAX.post(baseURL+'/newContents', data, (err, res) => {
                if (!err) {
                    if (res && res.success) {
                        this.loadContent();
                        msgHelper.removeModal();
                    } else {
                        msgHelper.removeModal();
                        setTimeout(() => {
                            msgHelper.newModal('Fel uppstod!', 'Det gick tyvär inte att spara inlägget.', '<button class="save_error btn btn-warning" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>');
                            $('.save_error').on('click', () => {
                                msgHelper.removeModal();
                            })
                        }, 210);
                    }
                } else {
                    msgHelper.removeModal();
                    setTimeout(() => {
                        msgHelper.newModal('Fel uppstod!', 'Det gick inte att skicka förfrågan!', '<button class="save_error btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span></button>');
                        $('.save_error').on('click', () => {
                            msgHelper.removeModal();
                        })
                    }, 210);
                }
            });
        //}
    }

}