@push('scripts')
<script src="https://cdn.tiny.cloud/1/{{ config('services.tinymce.key') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script nonce="{{ $cspNonce ?? '' }}">
    tinymce.init({
        selector: 'textarea.tinymce',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 400,
        branding: false,
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        }
    });
</script>
@endpush
