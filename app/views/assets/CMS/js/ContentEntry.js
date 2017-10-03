class ContentEntry {

    constructor (type, category, id, content, timestamp, edited) {
        this.type = type;
        this.category = category;
        this.id = id;
        this.content = content;
        this.timestamp = new Date(timestamp*1000);
        this.edited = edited;

        if (cookie.read('token')) {
            this.addEditListners();
        }
    }

    toHTML () {
        return '<div class="entry" id="'+this.id+'"><div class="content">'+this.content+'</div><div class="timestamp">'+this.timestamp.toGMTString()+(this.edited ? '<span class="edited">' + this.edited : '')+(this.edited ? '</span>' : '') + '</div></div>';
    }

    addEditListners () {
        $('#'+this.id).on('click', this.startEdit);
    }

    async startEdit () {
        const loader = new ContentLoader();
        const currentContent = await loader.read(this.type, false, 0, this.id, true);
    }

    update () {

    }

    remove () {

    }

}