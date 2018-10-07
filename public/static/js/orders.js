$(function () {
    // Editing notes.
    $('.js__edit-note').click(function(ev){
    	$('.js__note-form textarea', $(this).parent()).text($('.js__note', $(this).parent()).html());
        $('.js__note-form', $(this).parent()).removeClass('hidden');
        return false;
    });
    $('.js__note-form').submit(function(ev){
        ev.preventDefault();
        let handler = new XHRForm(this, SUBMIT_DISABLED_TEXT);
        let FORM = this;
        handler.onsuccess = function (json) {
            $(FORM).addClass('hidden');
            $('.js__note', $(FORM).parent()).html(json.note);
        };
        handler.submit(true);
    });
});
