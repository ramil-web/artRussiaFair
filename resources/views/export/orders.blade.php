@php use App\Models\Stand; @endphp
@php use App\Models\Guest; @endphp
<table>
    <thead>
    <tr>
        <th>Email</th>
        <th>ФИО</th>
        <th>Название</th>
        <th>Артикул</th>
        <th>Количество</th>
        <th>Цена</th>
        <th>Покраска</th>
        <th>Монтаж</th>
        <th>Представители</th>
        <th>VIP гости</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $order)
        @php
            $orderitems =$order->products->toArray();
            if(count($orderitems) == 0){
                $countproduct =1;
            }else{
                $countproduct = count($orderitems);
            }
        @endphp
        <tr>
            <td rowspan="{{$countproduct}}" style="vertical-align: top; ">{{ $order->user->email }}</td>

            <td rowspan="{{$countproduct}}" style="vertical-align: top;">
                {{ $order->user->name }}<br>
                {{ $order->user->phone }}
            </td>


            <td style="vertical-align: top">
                @if(count($orderitems)==0)
                    -
                @else
                    {{$orderitems[0]['category']['name']}}
                @endif
            </td>
            <td style="vertical-align: top">
                @if(count($orderitems)==0)
                    -
                @else
                    {{$orderitems[0]['name']}}
                @endif

            </td>
            <td style="vertical-align: top">
                @if(count($orderitems)==0)
                    -
                @else
                    {{$orderitems[0]['pivot']['quantity']}}
                @endif

            </td>
            <td style="vertical-align: top">
                @if(count($orderitems)==0)
                    -
                @else
                    {{$orderitems[0]['price']}}
                @endif

            </td>


            <td rowspan="{{$countproduct}}" style="vertical-align: top;">
                @if($order->stand->painting == Stand::ENUM_PAINTING_CUSTOM)
                    Под заказ
                @else
                    Стандартная
                @endif
            </td>

            <td rowspan="{{$countproduct}}" style="vertical-align: top;">
                @foreach($order->stand->fitters as $fitter)
                    <p>
                        {{$fitter->full_name}} <br>
                        {{$fitter->passport_number}} <br>
                        {{--                                        Вход {{$fitter->stand->entrance_number}}<br>--}}
                        Заезд: {{$fitter->stand->start_datetime}}<br>
                        Выезд: {{$fitter->stand->end_datetime}}
                    </p><br>
                @endforeach

            </td>

            <td rowspan="{{$countproduct}}" style="vertical-align: top;">
                @foreach($order->stand->representatives as $representative)
                    <p>
                        {{$representative->full_name}} <br>
                        {{$representative->passport_number}}
                    </p><br>
                @endforeach

            </td>

            <td rowspan="{{$countproduct}}" style="vertical-align: top;">
                @php
                    /** @var Guest $guest */
                @endphp
                @foreach($order->guests as $guest)
                    <p>
                        {{$guest->fullName}} <br>
                        {{$guest->email}}
                    </p><br>
                @endforeach
            </td>
        </tr>
        @for($i = 1; $i <= count($orderitems)-1; $i++)
            <tr>
                <td style="vertical-align: top">
                    {{$orderitems[$i]['category']['name']}}
                </td>
                <td style="vertical-align: top">
                    {{$orderitems[$i]['name']}}
                </td>
                <td style="vertical-align: top">
                    {{$orderitems[$i]['pivot']['quantity']}}
                </td>
                <td style="vertical-align: top">
                    {{$orderitems[$i]['price']}}
                </td>
            </tr>

        @endfor
    @endforeach
    </tbody>
</table>
