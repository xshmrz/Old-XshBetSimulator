<script src="assets/ui-dashboard/js/codebase.app.min.js"></script>
<script src="assets/ui-dashboard/js/jquery.min.js"></script>
<!-- Page JS Plugins -->
<script src="assets/ui-dashboard/js/plugins/datatables/dataTables.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-buttons/dataTables.buttons.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-buttons-jszip/jszip.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-buttons-pdfmake/pdfmake.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-buttons-pdfmake/vfs_fonts.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-buttons/buttons.print.min.js"></script>
<script src="assets/ui-dashboard/js/plugins/datatables-buttons/buttons.html5.min.js"></script>
<script src="assets/vendor/jquery.autocomplete.min.js"></script>
<script src="assets/vendor/jquery.blockui.min.js"></script>
<script src="assets/vendor/jquery.filepond.min.js"></script>
<script src="assets/vendor/jquery.filepond-jquery.min.js"></script>
<script src="assets/vendor/jquery.googlemap.min.js"></script>
<script src="assets/vendor/jquery.mask.min.js"></script>
<script src="assets/vendor/jquery.number.min.js"></script>
<script src="assets/vendor/jquery.slidemenu.min.js"></script>
<script src="assets/vendor/jquery.toast.min.js"></script>
<script src="assets/app.core.min.js"></script>
<script src="assets/app.min.js"></script>
<script>
    (function ($, d) {
            $.each(readyQ, function (i, f) {
                $(f);
            });
            $.each(bindReadyQ, function (i, f) {
                $(d).bind('ready', f);
            });
        }
    )(jQuery, document);
</script>
<script>
    jQuery('[name="birthday"]').mask('0000-00-00', {clearIfNotMatch: true});
</script>
