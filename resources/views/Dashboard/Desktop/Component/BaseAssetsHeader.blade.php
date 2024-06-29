<head>
	<base href="{{config("app.url")}}">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<title>{{config("app.name")}}</title>
	<meta name="name" content="{{config("app.name")}}">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="robots" content="index, follow">
	<link rel="stylesheet" href="assets/ui-dashboard/js/plugins/datatables-bs5/css/dataTables.bootstrap5.min.css">
	<link rel="stylesheet" href="assets/ui-dashboard/js/plugins/datatables-buttons-bs5/css/buttons.bootstrap5.min.css">
	<link rel="stylesheet" href="assets/ui-dashboard/js/plugins/datatables-responsive-bs5/css/responsive.bootstrap5.min.css">
	<link rel="stylesheet" href="assets/ui-dashboard/_scss/main.min.css">
	<link rel="stylesheet" href="assets/vendor/jquery.autocomplete.min.css">
	<link rel="stylesheet" href="assets/vendor/jquery.filepond.min.css">
	<link rel="stylesheet" href="assets/vendor/jquery.slidemenu.min.css">
	<link rel="stylesheet" href="assets/vendor/jquery.toast.min.css">
	<link rel="stylesheet" href="assets/app.core.min.css">
	<link rel="stylesheet" href="assets/app.min.css">
	<script>
        (function (w, d, u) {
                w.readyQ     = [];
                w.bindReadyQ = [];
                function p(x, y) {
                    if (x === 'ready') {
                        w.bindReadyQ.push(y);
                    }
                    else {
                        w.readyQ.push(x);
                    }
                }
                var a = {
                    ready: p,
                    bind : p
                };
                w.$   = w.jQuery = function (f) {
                    if (f === d || f === u) {
                        return a;
                    }
                    else {
                        p(f);
                    }
                };
            }
        )(window, document);
	</script>
</head>
