<!-- Styles -->
<script src="https://cdn.tiny.cloud/1/5aorefy1a3tzggygtpkx81v9k5puvldfm55a0il6y929m3fw/tinymce/6/tinymce.min.js"
        integrity="sha512-MIh5guD3Q4NZ8HcAjLtAr6ruYR5zsCTFHefr6y/FqgPAjub9cn/796tq5SCwCIwfYT4U/iSHEjWfmsb18QEECA=="
        crossorigin="anonymous" nonce="{{ csp_nonce() }}" referrerpolicy="origin"></script>
<script type="text/javascript" nonce="{{ csp_nonce() }}">
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
</script>
