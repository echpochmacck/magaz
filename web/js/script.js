$(() => {
    $('#registerform-password').on('keyup', function (e) {
        // console.log('dsd')
        const el = $(this),
            val = el.val(),
            info = $('.password-info');
        let message = '',
            color = ''
        if (val.length > 5) {
            message = 'Слабый апароль';
            color = 'text-danger'

            if (val.match('^(?=.*[0-9]){3}.+')) {
                message = 'средний апароль';
                color = 'text-warning'
                
            if (val.match('^(?=.*Max).+')) {
                message = 'прекрасный апароль';
                color = 'text-success'
            }
            }
        } else if (val.length > 3) {
            el.parent('.password-block').find('invalid-feedback').html('534');
            el.removeClass('is-invalid');
            el.addClass('is-valid');
            message = 'Слабый апароль';
            color = 'text-danger';
        }

        info.removeClass('text-warning')
        info.removeClass('text-danger')
        info.removeClass('text-success')

        info.html(message)
        info.addClass(color)
    })
})