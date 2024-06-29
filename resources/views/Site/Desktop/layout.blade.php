<!doctype html>
<html lang="{{config("app.locale")}}">
{{view("Dashboard.Desktop.Component.BaseAssetsHeader")}}
<body class="">
<div id="page-container" class="sidebar-dark side-scroll page-header-fixed page-header-dark main-content-custom">
	<div id="page-loader" class="show"></div>
	<nav id="sidebar">
		<div class="sidebar-content">
			<div class="content-header bg-black-10">
				<div>
					<a class="text-white" href="{{route("dashboard.index")}}">{{config("app.name")}}</a>
				</div>
			</div>
			<div class="js-sidebar-scroll">
				<div class="content-side content-side-full">
					<ul class="nav-main">
						{{view("Dashboard.Desktop.Component.BaseMenu")}}
					</ul>
				</div>
			</div>
		</div>
	</nav>
	<header id="page-header">
		<div class="content-header">
			<div class="space-x-1">
				<a class="text-white" href="{{route("dashboard.index")}}">{{config("app.name")}}</a>
			</div>
			<div class="d-none d-lg-block">
				<ul class="nav-main nav-main-horizontal nav-main-hover">
					{{view("Dashboard.Desktop.Component.BaseMenu")}}
				</ul>
			</div>
			<div class="space-x-1">
				{{-- <button type="button" class="btn btn-sm btn-link text-white">
					Root Demo
				</button> --}}
			</div>
		</div>
	</header>
	@yield("section-main")
	<footer id="page-footer" class="bg-body-light border-top">
		<div class="content py-3">
			<div class="row fs-sm">
				<div class="col-sm-6 order-sm-2 py-1 text-center text-sm-end">
					<a class="fw-semibold" href="javascript:void(0)">xshmrz</a>
				</div>
				<div class="col-sm-6 order-sm-1 py-1 text-center text-sm-start">
					<a class="fw-semibold" href="javascript:void(0)">{{config("app.name")}}</a> &copy;
					<span data-toggle="year-copy"></span>
				</div>
			</div>
		</div>
	</footer>
</div>
{{view("Dashboard.Desktop.Component.BaseAssetsFooter")}}
</body>
</html>
