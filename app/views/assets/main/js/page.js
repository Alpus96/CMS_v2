class Page {
    constructor () {
        this.readContentContainers();
    }

    readContentContainers () {
        $('#mainpage_posts');
    }

}

$(document).ready(() => { const page = new Page(); })