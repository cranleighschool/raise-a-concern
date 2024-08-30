import './bootstrap';
import 'bootstrap';
import '@popperjs/core';
import tinymce from 'tinymce';


/* Default icons are required. After that, import custom icons if applicable */
import 'tinymce/icons/default/icons.min.js';

/* Required TinyMCE components */
import 'tinymce/themes/silver/theme.min.js';
import 'tinymce/models/dom/model.min.js';

/* Import a skin (can be a custom skin instead of the default) */
import 'tinymce/skins/ui/oxide/skin.js';

/* Import plugins */
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/code';
import 'tinymce/plugins/emoticons';
import 'tinymce/plugins/emoticons/js/emojis';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';

/* Import premium plugins */
/* NOTE: Download separately and add these to /src/plugins */
/* import './plugins/<plugincode>'; */

/* content UI CSS is required */
// don't think I need this... import 'tinymce/skins/ui/oxide/content.js';

/* The default content CSS can be changed or replaced with appropriate CSS for the editor content. */
import contentCss from 'tinymce/skins/content/default/content.js';

/* Initialize TinyMCE */
tinymce.init({
    selector: 'textarea.wysiwyg',
    menubar: false,
    license_key: 'gpl',
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
