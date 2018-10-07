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

    // Changing order state.
    $('.js__actions-form button').click(function(ev) {
        let button = this;
        button.form.action.value = button.id;
        let handler = new XHRForm(button.form);
        handler.onsuccess = function (json) {
            if (button.id === 'finalize') {
                $(button).closest('.order').slideToggle(); // Depends on if state is set.
            }
            if (button.id === 'cancel') {
                $(button).closest('.order').slideToggle(); // Depends on if state is set.
            }
            if (button.id === 'roll') {
                $(button).closest('.order').slideToggle(); // Depends on if state is set.
            }
            if (button.id === 'blacklist') {
                $(button).closest('.order').slideToggle(); // Depends on if state is set.
            }
        };
        handler.submit(true);
        return false;
    });
});
