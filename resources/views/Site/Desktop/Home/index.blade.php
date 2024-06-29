@extends("Site.Desktop.layout")
@section("section-main")
	<main id="main-container">
		<div class="bg-body-light border-bottom">
			<div class="content py-2 text-end">
                <?php
                    $totalSpent   = Coupon()->count() * 1000;
                    $totalLost    = Coupon()->where([status => EnumProjectStatus::Lost])->count() * 1000;
                    $totalWin     = 0;
                    $totalPending = 0;
                    foreach (Coupon()->where([status => EnumProjectStatus::Win])->get() as $coupon):
                        $totalWin += $coupon->odd * 1000;
                    endforeach;
                    foreach (Coupon()->where([status => EnumProjectStatus::Pending])->get() as $coupon):
                        $totalPending += $coupon->odd * 1000;
                    endforeach;
                ?>
				<div>- {{number_format($totalSpent,2)}} TL</div>
				<div>+ {{number_format($totalWin,2)}} TL</div>
				<div>? {{number_format($totalPending,2)}} TL</div>
			</div>
		</div>
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
											@if($bet->live == EnumProjectFinish::Yes)
												<div class="me-3">
													<i class="fa fa-spinner fa-spin"></i>
												</div>
											@endif
											<div class="me-2">
												<div><span class="badge rounded bg-body-light text-dark min-width-50px mb-1">{{$bet->score}}</span></div>
												<div>&nbsp;</div>
											</div>
											<div>
												<div><span class="badge rounded bg-{{EnumProjectStatus::getColor($bet->status)}} min-width-50px mb-1 text-uppercase">{{$bet->outcomeName}}</span></div>
												<div><span class="badge rounded bg-primary min-width-50px">{{number_format($bet->odd,2)}}</span></div>
											</div>
										</li>
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

