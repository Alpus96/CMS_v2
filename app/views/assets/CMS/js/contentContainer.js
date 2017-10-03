class ContentContainer {
    constructor (id, contentType, category, amount) {
        if (typeof id != 'string' && id.indexOf('') != -1 && typeof contentType != 'string' && contentType.indexOf('') != -1 && typeof category != 'string' && category.indexOf('') != -1 && typeof amount != 'number') { return false; }
        this.id = id;
        this.contentType = contentType;
        this.category = category;
        this.amount = amount;

        this.contents = [];

        this.noResponseError = '<p class="alert alert-danger">Kunde inte hämta data!</p>';
    }

    updateType (newType) {
        if (typeof newType != 'string' && newType.indexOf('') != -1)
        { return false; }

        $('#'+id).removeClass(this.contentType);
        $('#'+id).addClass(newType);
        this.contentType = newType;
    }

    updateCategory (newCategory) {
        if (typeof newCategory != 'string' && newCategory.indexOf('') != -1)
        { return false; }

        $('#'+id).removeClass(this.category);
        $('#'+id).addClass(newCategory);
        this.contentType = newCategory;
    }

    updateAmount (newAmount) {
        if (typeof newCategory != 'number')
        { return false; }

        $('#'+id).removeClass(this.amount);
        $('#'+id).addClass(newAmount);
        this.contentType = newAmount;
    }

    async loadContent () {
        const loader = new ContentLoader();
        let content = await loader.read(this.contentType, this.category, this.amount);
        if (content) {
            if (content.substr(0,1) === '{') {
                content = JSON.parse(content);
                const entryType = this.contentType.charAt(0).toUpperCase() + this.contentType.slice(1) + 'Entry';
                for (let key in content) {
                    let entry = content[key];
                    //  Create instances of entry.
                    const entryObj = new ContentEntry(this.contentType, this.category, entry.id, entry.content, entry.timestamp, entry.edited);
                    this.contents.push(entryObj);

                    $('#'+this.id).append(entryObj.toHTML());
                    if (cookie.read('token')) {
                        this.contents[this.contents.length-1].addEditListners();
                    }
                }
            } else {
                $('#'+this.id).html(content);
            }
        } else {
            //  NOTE:  Show error or no data message?
            $('#'+this.id).html(this.noResponseError);
        }
    }

}