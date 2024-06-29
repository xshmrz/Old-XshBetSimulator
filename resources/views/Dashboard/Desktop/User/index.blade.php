<?php
    $userData = User()->orderByDesc(id)->get();
?>
@extends("Dashboard.Desktop.layout")
@section("section-main")
	<main id="main-container">
		{{view("Dashboard.Desktop.Component.BaseBreadcrumb")}}
		<div class="content content-full">
			<div class="block block-rounded">
				<div class="block-header block-header-default min-height-55px">
					<h3 class="block-title">{{trans("app.User")}}</h3>
					<div class="block-options"><a role="button" href="javascript:void(0)" class="btn btn-primary min-width-75px">{{trans("app.Create")}}</a></div>
				</div>
				@if($userData->isEmpty())
					<div class="block-content border-top p-3">
						{{view("Dashboard.Desktop.Component.BaseAlertNoData",["name"=>trans("app.User")])}}
					</div>
				@else
					<div class="block-content border-top p-0">
						{{view("Dashboard.Desktop.Component.UserTable",["userData"=>$userData])}}
					</div>
				@endif
			</div>
		</div>
	</main>
@endsection

