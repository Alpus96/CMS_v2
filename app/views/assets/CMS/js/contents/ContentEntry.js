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

        this.parseWidth(containerInfo.entryWidth);

        if (!this.abort) {
            this.parentID = containerId;
            this.data = entryObject;

            if (entryObject === 'newEntry') {
                this.prependAsEditable();
            } else {
                this.prependAsStatic();
                if (cookie.read('token')) {
                    this.addEditListner();
                }
            }
        }
    }

    parseWidth (width) {
        this.width = width.indexOf('p') ? parseInt(width.replace('p', '')) : parseInt(width);
        this.widthType = width.indexOf('p') ? 1 : 0;
    }

    prependAsStatic () {
        //  NOTE:  this.data = {id, entryContent, (auhtor), (posted), (lastEdited)}
        const name = 'entry_'+this.data.id;
        this.id = '.'+name;
        const entry = '<div class="'+name+'"><p id="testEntry"></p></div>';
        msgHelper.alert();
        $(this.parentID).prepend(entry);
    }

    prependAsEditable () {}

    addEditListner () {}

    startEdit () {}

    cancelEdit () {}

    saveEdit () {}

    removeEntry () {}

    errroAlert () {
        const id = 'entry_error_' + Math.random()*1000;
        $(this.parentID).prepend('<div class="'+id+'"></div>');
        msgHelper.alert(id, 'Ingen data!', 'danger');
    }

}

/*

toHTML () {
    const dateString = this.timestamp.getHours()+':'+this.timestamp.getMinutes()+' '+this.timestamp.toLocaleDateString();
    //  TODO: add hidden for type?
    return '<div class="entry type_'+this.type+'" id="'+this.type+'_'+this.id+'"><div class="content">'+this.content+'</div><div class="timestamp">'+dateString+(this.edited ? '<span class="edited">' + this.edited : '')+(this.edited ? '</span>' : '') + '</div></div>';
}

newEditListner () { $('#'+this.type+'_'+this.id).on('click', () => { this.startEdit(); }); }

async startEdit () {
    const contentJsonAsMD = await contentRequest.readMD(this.type, this.id);
    const currentContent = JSON.parse(contentJsonAsMD);
    //  TODO: Complete form and fill with current content.
    const editForm = '<div id="'+this.type+'_'+this.id+'"><div class="form-group"><textarea class="form-control editArea" autofocus>'+currentContent.content+'</textarea></div><div class="btn-group pull-right"><input type="button" class="btn btn-danger" value="Ta bort" id="editDelete"><input type="button" class="btn btn-warning" value="Avbryt" id="editAbort"><input type="button" class="btn btn-success" value="Spara" id="editSave"></div></div>';
    $('#'+this.type+'_'+this.id).replaceWith(editForm);
    this.addEditListners();
}

addEditListners () {
    $('#editDelete').on('click', () => {this.delete()});
    $('#editAbort').on('click', () => {this.abort()});
    $('#editSave').on('click', () => {this.update()});

    const area = $('textarea.editArea');
    area.height(0);
    area.height(area[0].scrollHeight+parseInt(area.css('font-size'))+6);
    area.keyup(() => {
        area.height(0);
        area.height(area[0].scrollHeight+parseInt(area.css('font-size'))+6);
    });
}

abort () {
    $('#'+this.type+'_'+this.id).replaceWith(this.toHTML());
    if (cookie.read('token')) { this.newEditListner(); }
}

update () {
    const content = {id: this.id, type: this.type, content: $('textarea.editArea').val()};
    if (contentRequest.update(content)) {
        this.abort();
        $('#'+this.type+'_'+this.id).append('<p id="contentMsg" class="alert alert-success">Innehållet har uppdaterats!</p>');
        //  Show success message for 5sec.
        setTimeout(() => {$('#contentMsg').remove()}, 5000);
    } else {
        //  Show error message for 5sec.
        this.abort();
        $('#'+this.type+'_'+this.id).append('<p id="contentMsg" class="alert alert-warning">Innehållet har inte uppdaterats!</p>');
        setTimeout(() => {$('#contentMsg').remove()}, 5000);
    }
}

remove () {

}

toNew() {

}

create () {

}

*/