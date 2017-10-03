class Page {
    constructor () {
        this.contentContainerIDs = [];
        this.contentContainers = [];

        this.getContainerIDs();
        this.initalizeContainers();
        this.loadAll();
    }

    getContainerIDs () {
        $.each($('div.contentContainer'), (i, obj) => {
            let skip = false;
            for (let existing of this.contentContainerIDs) {
                if (existing == obj.id) {
                    skip = true;
                }
            }
            if (!skip) { this.contentContainerIDs.push(obj.id); }
            else { console.warn('Skipped content container with duplicate id. ("'+obj.id+'")'); }
        });
    }

    initalizeContainers () {
        for (let id of this.contentContainerIDs) {
            let classes = $('#'+id).attr('class').split(/\s+/);
            let pars = {type: '', category: '', amount: 0};
            //  NOTE:  type = 'type_*', category = 'cat_*', amount = 'amount_*'
            for (let clname of classes) {
                if (clname == 'contentContainer') { continue; }
                if (clname.substr(0, 5) === 'type_') {
                    pars.type = clname.substring('type_'.length);
                } else if (clname.substr(0, 4) === 'cat_') {
                    pars.category = clname.substring('cat_'.length);
                } else if (clname.substr(0, 7) === 'amount_') {
                    pars.amount = clname.substring('amount_'.length);
                }
            }
            if (pars.type != '' && pars.category != '' && pars.amount > 0) {
                this.contentContainers.push(
                    new ContentContainer(id, pars.type, pars.category, pars.amount)
                );
            } else {
                console.warn('insufficient info for conatiner "'+id+'".');
            }
        }
    }

    loadAll () {
        for (let container of this.contentContainers) {
            container.loadContent();
        }
    }

}

$(document).ready(() => { const page = new Page(); })