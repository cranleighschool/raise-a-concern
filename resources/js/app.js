import './bootstrap';

import 'bootstrap';
import '@popperjs/core';

if (document.getElementById('logout-btn')) {
    document.getElementById('logout-btn')
        .addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('logout-form').submit();
        })
}
