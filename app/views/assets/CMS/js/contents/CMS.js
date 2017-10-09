class CMS {
    constructor () {
        this.getContainerIDs();
        this.initalizeContainers();
    }

    getContainerIDs () {
        this.contentContainerIDs = [];
        $.each($('div.contentContainer'), (i, obj) => {
            let skip = false;
            for (let existing of this.contentContainerIDs) {
                if (existing == obj.id) {
                    skip = true;
                }
            }
            if (!skip) { this.contentContainerIDs.push('#'+obj.id); }
            else { console.warn('Skipped content container with duplicate id. ("'+obj.id+'")'); }
        });
    }

    initalizeContainers () {
        this.contentContainers = [];
        for (let id of this.contentContainerIDs) {
            let container = new ContentContainer(id);
            this.contentContainers.push(container);
        }
    }

}

$(document).ready(() => { const cms = new CMS(); });