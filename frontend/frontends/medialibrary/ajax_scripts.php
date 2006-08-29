<script>
function submitForm(form, url) {
<?php
    if (defined('NO_AJAX_LINKS')) {
      echo 'form.submit();';
    } else {
?>
      ajax_submit_form(form, url, maindiv_cb); 
<?php
    }
?>
}
</script>