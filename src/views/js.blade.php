<script>
    @if ($errors->any())
    var zfw_errors = [];
        @foreach ($errors->all() as $error)
        zfw_errors.push('{{ addslashes($error) }}');
        @endforeach
    @endif
    $(function() {
        if (typeof zfw_errors !== 'undefined') {
            var error_html = '';
            error_html += '<div class="alert alert-danger">'; /* Move to a config block */
            zfw_errors.forEach(function(error) {
                error_html += error + '<br>';
            });
            error_html += '</div>';
            /*
            Do we have a custom placeholder for the response? NOT TESTED */
            if ($('#zfw-response').length) {
                $('#zfw-response').html(error_html);
            }
            else {
                $('form.zfw').before(error_html);
            }
        }
    });
</script>