$(function () {
    $('.js__upload-input').change(function(){addFile(this)});
    $form = $('form');
    $form.submit(function(ev){
        ev.preventDefault();
        $('button[type=submit]', $form).attr({'disabled':'disabled'}).text(DISABLED_TEXT);
        handler = new XHRForm(this);
        handler.onsuccess = function (json) {
            $('button[type=submit]', $form).removeAttr('disabled').text(ENABLED_TEXT);
            if (json.location)
                window.location = json.location;
            if (json.error)
                $('.js-flash').html(`<div class="alert alert-danger">${json.error}</div>`);
        };
        handler.onerror = function (json) {
            $('button[type=submit]', $form).removeAttr('disabled').text(ENABLED_TEXT);
            $('.js-flash').html(`<div class="alert alert-danger">${json.error}</div>`);
        };
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

function addFile(obj) {
    let v = obj.files;
    if (parseInt(v.length) > 10) {
        alert(FILE_LIMIT);
        $(obj).parent()
            .prepend('<input type="file" class="js__upload-input" name="file[]" multiple />')
            .find('input').change(function() { addFile(this); });
        $(obj).remove();
        return true;
    }
    $(obj).hide();
    $(obj).parent()
        .prepend('<input type="file" class="js__upload-input" name="file[]" multiple />')
        .find('input').change(function() { addFile(this); });

    var $list = $('<div></div>');
    for (let i = 0; i < v.length; i++) {
        if (v[i].name) {
            $list.append(v[i].name + '<br />');
        }
    }
    $('#upload-queue').append($list);
    $list
        .append('(<a class="remove">remove</a>)')
        .find('.remove').click(function(){ $list.remove(); $(obj).remove(); return false; });
}

