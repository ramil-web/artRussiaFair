@php use App\Models\Guest; @endphp
<table>
    <thead>
    <tr>
        <th>Фамилия</th>
        <th>Имя</th>
        <th>Организация</th>
        <th>От кого</th>
        <th>Email</th>
        <th>Email организатора</th>
    </tr>
    </thead>
    <tbody>
    @php
        /** @var Guest $guest */
    @endphp
    @foreach($guests as $guest)
        <tr>

            <td style="vertical-align: top">
                {{$guest->last_name}}
            </td>
            <td style="vertical-align: top">
                {{$guest->first_name}}
            </td>
            <td style="vertical-align: top">
                {{$guest->organization}}
            </td>
            <td style="vertical-align: top">
                {{$guest->from_whom}}
            </td>
            <td style="vertical-align: top">
                {{$guest->email}}
            </td>
            <td style="vertical-align: top">
                {{$guest->order->user->email}}
            </td>
        </tr>

    @endforeach
    </tbody>
</table>
