$(function () {
    $form = $('form');
    $form.submit(function(ev){
        ev.preventDefault();
        $('button[type=submit]', $form).attr({'disabled':'disabled'}).text(DISABLED_TEXT);
        handler = new XHRForm(this);
        handler.onprogress = function (evt) {
            $('.js__progress-bar').removeClass('hidden');
            if (evt.lengthComputable) {
                let percent = evt.loaded / evt.total * 100;
                $('.js__progress-bar div').css({'width' : percent + '%'});
            }
        };
        handler.submit(true);
    });
});
