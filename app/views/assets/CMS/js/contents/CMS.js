/**
*   @desc
**/
class CMS {
    constructor () {
        this.getContainerIDs();
        this.initalizeContainers();
        if (cookie.read('token')) {
            if (window.location.href.indexOf('/edit') != -1 || window.location.href.indexOf('/settings') != -1) {
                this.loadCheetSheet();
            }
        }
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

    loadCheetSheet () {
        AJAX.get(baseURL+'/app/views/assets/CMS/json/mdcs.json', (err, res) => {
            if (!err && res) {
                cs = res.cheetsheet;
                return;
            }
            console.warn('Unable to load markdown cheet sheet!');
        });
    }

}
let cs;
$(document).ready(() => { const cms = new CMS(); });