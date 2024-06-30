<!doctype html>
<html lang="{{config("app.locale")}}">
{{view("Dashboard.Desktop.Component.BaseAssetsHeader")}}
<script data-name="BMC-Widget" data-cfasync="false" src="https://cdnjs.buymeacoffee.com/1.0.0/widget.prod.min.js" data-id="xshmrz" data-description="Support me on Buy me a coffee!" data-message="" data-color="#5F7FFF" data-position="Right" data-x_margin="18" data-y_margin="18"></script>
<body class="">
<div id="page-container" class="sidebar-dark side-scroll page-header-fixed page-header-dark main-content-custom">
	<div id="page-loader" class="show"></div>
	<nav id="sidebar">
		<div class="sidebar-content">
			<div class="content-header bg-black-10">
				<div>
					<a class="text-white" href="{{route("site.index")}}">{{config("app.name")}}</a>
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
				<a class="text-white" href="{{route("site.index")}}">{{config("app.name")}}</a>
			</div>
			<div class="d-none d-lg-block">
				<ul class="nav-main nav-main-horizontal nav-main-hover">
					{{view("Dashboard.Desktop.Component.BaseMenu")}}
				</ul>
			</div>
			<div class="space-x-1">
				@php
					$couponLastUpdate = CouponUpdate()->where([status_update => EnumProjectStatusUpdate::Success])->orderByDesc(id)->first();
				@endphp
				Update : {{now()::parse($couponLastUpdate->created_at)->format("H:i")}}
			</div>
		</div>
	</header>
	@yield("section-main")
	<footer id="page-footer" class="bg-body-light border-top">
		<div class="content py-3">
			<div class="row fs-sm">
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
