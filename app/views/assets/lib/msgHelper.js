class MsgHelper {

    constructor () {
        this.selector = true;
        if (typeof $ == 'undefined') {
            console.warn('jQuery undefined!');
            this.selector = false;
        }
    }

    alert (msgId, message, type = 'info', time = 0) {
        if (!this.selector) { console.warn('jQuery undefined!'); return; }
        if (!msgId) { console.warn('Cannot set message of undefined!'); return; }
        if (!message){ console.warn('Undefined message.'); }

        $(msgId).text(message);
        $(msgId).removeClass('hidden');
        this.alertType(msgId, type);
        if (time) { setTimeout(() => {
            if (!$(msgId).hasClass('hidden'))
            { $(msgId).addClass('hidden'); }
        }, time); }
    }

    alertType (msgId, type) {
        if (!this.selector) { console.warn('jQuery undefined!'); return; }
        if (type == 'success' || type == 'info' || type == 'warning' || type == 'danger') {
            if (!$(msgId).hasClass('alert'))
            { $(msgId).addClass('alert'); }
            if (!$(msgId).hasClass('text-center'))
            { $(msgId).addClass('text-center'); }

            if ($(msgId).hasClass('alert-success'))
            { $(msgId).removeClass('alert-success'); }
            if ($(msgId).hasClass('alert-info'))
            { $(msgId).removeClass('alert-info'); }
            if ($(msgId).hasClass('alert-warning'))
            { $(msgId).removeClass('alert-wanring'); }
            if ($(msgId).hasClass('alert-'))
            { $(msgId).removeClass('alert-danger'); }

            $(msgId).addClass('alert-'+type);
        }
    }

    hide (msgId) {
        if (!this.selector) { console.warn('jQuery undefined!'); return; }
        if (!$(msgId).hasClass('hidden'))
        { $(msgId).addClass('hidden'); }
    }

    newModal (title, content, options) {
        if (!this.selector) { console.warn('jQuery undefined!'); return; }
        
        const modal = '<div class="msgHelperModal modal fade" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">'+title+'</h4></div><div class="modal-body">'+content+'</div><div class="modal-footer">'+options+'</div></div></div></div>';
        $('body').append(modal);

        $('.msgHelperModal').modal({ show: false });
        $('.msgHelperModal').modal('show');

        $('.msgHelperModal').on('hidden.bs.modal', () => { this.removeModal(); });
    }

    removeModal () {
        $('.msgHelperModal').modal('hide');
        setTimeout(() => { $('.msgHelperModal').remove(); }, 250);
    }
}

const msgHelper = new MsgHelper();