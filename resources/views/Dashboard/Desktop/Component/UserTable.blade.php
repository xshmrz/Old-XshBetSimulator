<table class="table table-striped-columns table-vcenter jsDataTable no-wrap" style="min-width: 800px;">
	<thead>
	<tr>
		<th class="text-center" style="width: 80px;">{{trans("app.Id")}}</th>
		<th class="">Name</th>
		<th class="" style="width: 250px;">{{trans("app.E-Mail")}}</th>
		<th class="text-center" style="width: 80px;">{{trans("app.Role")}}</th>
		<th class="text-center" style="width: 80px">{{trans("app.Action")}}</th>
	</tr>
	</thead>
	<tbody>
	@foreach($userData as $user)
		<tr>
			<td class="text-center">{{$user->id}}</td>
			<td class="">{{$user->firstname." ".$user->lastname}}</td>
			<td class="">{{$user->email}}</td>
			<td class="text-center">
				<span class="badge p-2 font-12px bg-{{EnumUserRole::getColor($user->role)}}">{{EnumUserRole::getTranslation($user->role)}}</span>
			</td>
			<td class="text-center">
				<a role="button" href="javascript:void(0)" class="btn btn-secondary min-width-75px">{{trans("app.Show")}}</a>
			</td>
		</tr>
	@endforeach
	</tbody>
</table>
