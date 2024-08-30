import './bootstrap';
import 'bootstrap';
import '@popperjs/core';
import tinymce from 'tinymce';

tinymce.init({
    selector: 'textarea.wysiwyg',
    menubar: false,
    statusbar: false,
    browser_spellcheck: true,
    toolbar1: 'undo redo | styleselect | bold italic | link bullist numlist outdent indent | forecolor backcolor | paste',
    plugins: ['lists', 'link'],
    contextmenu: [], // we do this so that TinyMCE doesn't overwrite our users right click context menu
    paste_data_images: false,
    paste_webkit_styles: "color",
    paste_merge_formats: true,
    setup: function (editor) {
        editor.on('blur', function (e) {
            var content = tinymce.activeEditor.getContent();
            if (content.includes("<img src=")) {
                alert("I've noticed your input contains an image. This cannot be saved here. Please save and then add the image as an attachment in notes.");
            }
        });
    }
});

if (document.getElementById('logout-btn')) {
    document.getElementById('logout-btn')
        .addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('logout-form').submit();
        })
}
