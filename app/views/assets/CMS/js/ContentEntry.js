class ContentEntry {

    //  TODO:  Handle construct as new entry.
    constructor (parentID, type, category, id, content, timestamp, edited) {
        //  TODO: Confirm parent, type and category are valid.
        this.parent = parentID;
        this.type = type;
        this.category = category;

        //  TODO: Handle static editable or new.

        this.id = id;
        this.content = content;
        this.timestamp = new Date(timestamp*1000);
        this.edited = edited;

        $('#'+this.parent).append(this.toHTML());
        if (cookie.read('token')) {
            this.newEditListner();
        }
    }

    toHTML () {
        const dateString = this.timestamp.getHours()+':'+this.timestamp.getMinutes()+' '+this.timestamp.toLocaleDateString();
        //  TODO: add hidden for type?
        return '<div class="entry type_'+this.type+'" id="'+this.type+'_'+this.id+'"><div class="content">'+this.content+'</div><div class="timestamp">'+dateString+(this.edited ? '<span class="edited">' + this.edited : '')+(this.edited ? '</span>' : '') + '</div></div>';
    }

    newEditListner () { $('#'+this.type+'_'+this.id).on('click', () => { this.startEdit(); }); }

    async startEdit () {
        const loader = new ContentRequest();
        const contentJsonAsMD = await loader.read(this.type, false, 0, this.id, true);
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
        const cR = new contentRequest();
        if (cp.update(content)) {
            this.abort();
            $('#'+this.type+'_'+this.id).append('<p id="contentMsg" class="alert alert-success">Innehållet har uppdaterats!</p>');
            //  Show success message for 5sec.
        } else {
            //  Show error message for 5sec.
        }
    }

    remove () {

    }

    toNew() {

    }

    create () {

    }

}