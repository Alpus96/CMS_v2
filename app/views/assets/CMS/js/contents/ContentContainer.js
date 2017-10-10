if (typeof baseURL === 'undefined' || baseURL === null) { let baseURL = '/projects/CMS_v2'; }

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
                if (cookie.read('token'))
                { this.newEntryButton(); }
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
        AJAX.post(baseURL+'/getContents', this.data, (err, res) => {
            if (!err) {
                if (res && res.success) {
                    let entryCount;
                    $(this.id).html('');
                    for (let entry of res.data) {
                        this.entries.push(new ContentEntry(this.id, entry, this.data));
                        entryCount++;
                    }
                    if (entryCount == this.amount || this.data.offset != 0)
                    { this.addPageButtons(); }
                } else {
                    $(this.id).append('<div class="clearfix"></div><p class="text-center alert '+this.id.replace('#', '')+'"></p>');
                    msgHelper.alert(this.id.replace('#', '.'), 'Inga inlägg.');
                }
            } else {
                $(this.id).append('<div class="clearfix"></div><p class="text-center alert '+this.id.replace('#', '')+'"></p>');
                msgHelper.alert(this.id.replace('#', '.'), 'Kunde inte hämta inlägg.', 'danger');
            }
        });
    }

    addPageButtons () {
        //  TODO:  Add buttons for skipping next/previous page.
        const buttonNextId = this.id+'_next';
        const buttonPrevId = this.id+'_prev';
        $(this.id).append('<div class="col-xs-12 btn-group text-center"><button class="btn btn-primary"><span class="glyphicon glyphicon-"></span></button><button class="btn btn-primary"><span class="glyphicon glyphicon-"></span></button></div');
        $('.'+buttonNextId).off();
        $('.'+buttonNextId).on('click', () => { this.nextPage(); });
        $('.'+buttonPrevId).off();
        $('.'+buttonPrevId).on('click', () => { this.previousPage(); });
    }

    nextPage () {
        this.data.offset = this.data.offset + this.data.amount;
        this.loadContent();

    }

    previuosPage () {
        this.data.offset = this.data.offset - this.data.amount;
        this.loadContent();
    }

    newEntryButton () {
        const name = this.id.replace('#', '')+'_button';
        const button = '<div class="col-xs-12 text-center bottom-margin-lg"><button class="' + name + ' btn btn-primary"><span class="glyphicon glyphicon-plus"></span></button></div>';
        $(this.id).prepend(button);
        $('.'+name).off();
        $('.'+name).on('click', () => { this.startNewEntry(); });
    }

    startNewEntry () {
        msgHelper.newModal('Nytt inlägg', '<div class="col-xs-12"></div><div class="form-group"><textarea class="form-control editArea newEntry_text" name="" placeholder="Innehåll..." placeholder></textarea></div><p class="hidden newEntryAlert"></p>', '<div class="btn-group"><button type="button" class="newEntryCancel btn btn-warning"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button><button type="button" class="newEntrySave btn btn-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></button></div>');

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
            msgHelper.removeModal();
        });
    }

    saveNewEntry () {
        console.log('Saving...');
    }

}