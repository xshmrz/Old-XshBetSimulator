@extends("Site.Desktop.layout")
@section("section-main")
	<main id="main-container">
		{{view("Dashboard.Desktop.Component.BaseBreadcrumb")}}
		<div class="content content-full">
			<div class="row">
				@foreach(Coupon()->orderBy(id,"DESC")->get() as $coupon)
					<div class="col-md-12">
						<div class="block block-rounded shadow">
							<div class="block-header block-header-default p-3 bg-body-dark">
								<h3 class="block-title">{{$coupon->no}}</h3>
								<div>
									<span class="badge rounded bg-{{EnumProjectStatus::getColor($coupon->status)}} text-uppercase text-end min-width-100px">{{number_format($coupon->odd * 1000),2}} TL</span>
									<span class="badge rounded bg-primary text-uppercase min-width-50px">{{number_format($coupon->odd,2)}}</span>
								</div>
							</div>
							<div class="block-content p-0 border-top">
								<ul class="list-group list-group-flush push mb-0">
									@foreach(Bet()->whereIn(marketNo,explode(",",$coupon->data))->orderBy(eventDate,"ASC")->get() as $bet)
										<li class="list-group-item d-flex justify-content-between align-items-start p-3">
											<div class="me-auto">
												<div class="fw-bold">{{Str::limit($bet->eventName,25,"...")}}</div>
												<div class="text-muted">{{now()::parse($bet->eventDate)->format("d-m-Y H:i")}}</div>
											</div>
											<div class="me-2">
												<div><span class="badge rounded bg-body-light text-dark min-width-50px mb-1">{{$bet->score}}</span></div>
												<div>&nbsp;</div>
											</div>
											<div>
												<div><span class="badge rounded bg-{{EnumProjectStatus::getColor($bet->status)}} min-width-50px mb-1 text-uppercase">{{$bet->outcomeName}}</span></div>
												<div><span class="badge rounded bg-primary min-width-50px">{{number_format($bet->odd,2)}}</span></div>
											</div>
										</li>

										@if(now()->diffInHours(now()::parse($bet->eventDate),false) < (0 - 2.5) and $bet->status == EnumProjectStatus::Lost)
											@php
												$coupon->status = EnumProjectStatus::Lost;
												$coupon->save();
											@endphp
										@endif

									@endforeach
								</ul>
							</div>
							<div class="block-content d-flex justify-content-between align-items-start  p-3 border-top bg-body-light">
								<div>&nbsp;</div>
								<div></div>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</main>
@endsection

