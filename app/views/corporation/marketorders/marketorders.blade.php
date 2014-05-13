@extends('layouts.masterLayout')

@section('html_title', 'Corporation Market Orders')

@section('page_content')

{{-- open a empty form to get a crsf token --}}
{{ Form::open(array()) }} {{ Form::close() }}

<div class="row">
	<div class="col-md-12">
		<div class="box">
            <div class="box-header">
                <h3 class="box-title">Market Orders ({{ count($market_orders) }})</h3>
            </div><!-- /.box-header -->
            <div class="box-body no-padding">
		        <table class="table table-hover table-condensed">
		            <tbody>
			            <tr>
			                <th style="width: 10px">#</th>
			                <th>Type</th>
			                <th>Wallet Div</th>
			                <th>Price P/U</th>
			                <th>Issued</th>
			                <th>Expires</th>
			                <th>Order By</th>
			                <th>Location</th>
			                <th>Type</th>
			                <th>Vol</th>
			                <th>Min. Vol</th>
			                <th>State</th>
			            </tr>

						@foreach ($market_orders as $order)
				            <tr>
				                <td>{{ $order->orderID }}</td>
				                <td>
				                	@if ($order->bid)
					                	Buy
					                @else
					                	Sell
					                @endif
				                </td>
				                <td>{{ $wallet_divisions[$order->accountKey] }}</td>
				                <td>
				                	@if ($order->escrow > 0)
					                	<span data-toggle="tooltip" title="" data-original-title="Escrow: {{ $order->escrow }}">
						                	<i class="fa fa-money pull-right"></i> {{ number_format($order->price, 2, '.', ' ') }}
						                </span>
						             @else
					                	{{ number_format($order->price, 2, '.', ' ') }}
						             @endif
				                </td>									                
				                <td>{{ $order->issued }}</td>
				                <td>
				                	{{ Carbon\Carbon::parse($order->issued)->addDays($order->duration)->diffForHumans() }}
				                </td>
				                <td>
									<a href="{{ action('CharacterController@getView', array('characterID' => $order->charID)) }}">
										<img src='//image.eveonline.com/Character/{{ $order->charID }}_32.jpg' class='img-circle' style='width: 18px;height: 18px;'>
									</a>
				                	<span rel="id-to-name">{{ $order->charID }}</span>
				                </td>
				                <td>{{ $order->location }}</td>
				                <td>
				                	<img src='//image.eveonline.com/Type/{{ $order->typeID }}_32.png' style='width: 18px;height: 18px;'>
				                	{{ $order->typeName }}
				                </td>
				                <td>{{ $order->volRemaining }}/{{ $order->volEntered }}</td>
				                <td>{{ $order->minVolume }}</td>
				                <td>{{ $order_states[$order->orderState] }}</td>
				            </tr>
						@endforeach

		        	</tbody>
		        </table>
            </div><!-- /.box-body -->
        </div>
	</div> <!-- ./col-md-12 -->
</div> <!-- ./row -->

@stop

@section('javascript')
<script type="text/javascript">

	$( document ).ready(function() {
		var items = [];
		var arrays = [], size = 250;

		$('[rel="id-to-name"]').each( function(){
		//add item to array
			items.push( $(this).text() );
		});

		var items = $.unique( items );

		while (items.length > 0)
			arrays.push(items.splice(0, size));

		$.each(arrays, function( index, value ) {

			$.ajax({
				type: 'POST',
				url: "{{ action('HelperController@postResolveNames') }}",
				data: {
					'ids': value.join(',')
				},
				success: function(result){
					$.each(result, function(id, name) {

						$("span:contains('" + id + "')").html(name);
					})
				},
				error: function(xhr, textStatus, errorThrown){
					console.log(xhr);
					console.log(textStatus);
					console.log(errorThrown);
				}
			});
		});
	});

</script>
@stop