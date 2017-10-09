//let baseURL = '/projects/CMS_v2';

class ContentContainer {

    constructor (id) {
        if (!id) { console.warn('Invalid content container id \'undefined\'!'); }
        if (typeof $ == 'undefined') { console.warn('jQuery undefined!'); }

        if (id && $) {
            this.id = id;
            this.getOptions();

            if (this.data.marker != '' && this.data.amount != -1) {
                this.loadContent();
                if (cookie.read('token'))
                { this.newEntryButton(); }
            } else {
                console.warn('Insufficient data! ("'+this.id+'")');
                $(id).append('<p class="'+id.replace('#', '')+'"></p>');
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
            includeAuthor: false,
            includeDate: false
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
                this.data.includeAuthor = incAuth ? true : false;
            } else if (opt.indexOf('incDate_') != -1) {
                const incDate = opt.replace('incDate_', '');
                this.data.includeDate = incDate ? true : false;
            } else if (opt.indexOf('entryWidth_') != -1) {
                const entryWidth = opt.replace('entryWidth_', '');
                this.entryWidth = entryWidth;
            }
        }
    }

    loadContent () {
        AJAX.post(baseURL+'/getContents', this.data, (err, res) => {
            if (!err) {
                if (res && count(res.data) > 0) {
                    let entryCount;
                    $(this.id).html('');
                    for (let entry of res.data) {
                        this.entries.push(new ContentEntry(this.id, entry, this.data));
                        entryCount++;
                    }
                    if (entryCount == this.amount || this.data.offset != 0)
                    { this.addPageButtons(); }
                } else {
                    $(this.id).append('<p class="'+this.id.replace('#', '')+'"></p>');
                    msgHelper.alert(this.id.replace('#', '.'), 'Inga inlägg.');
                }
            } else {
                $(this.id).append('<p class="'+this.id.replace('#', '')+'"></p>');
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
        const name = this.id.replace('#', '');
        const button = '<div class="col-xs-12 text-center"><button class="'+name+' btn btn-primary"><span class="glyphicon glyphicon-plus"></span></button></div>';
        $(this.id).prepend(button);
        $('.'+name).on('click', () => {
            this.entries.push(new ContentEntry(this.id, 'newEntry'));
        });
    }

}