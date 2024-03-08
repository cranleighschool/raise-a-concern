import * as Popper from '@popperjs/core';

window.Popper = Popper;

import './bootstrap';

if (document.getElementById('logout-btn')) {
    document.getElementById('logout-btn')
        .addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('logout-form').submit();
        })
}
