<p>Your following properties are expired today, and they are marked as unpublished.
    If you want to extend the expiry date, then please visit your Agent panel using the link:
    <a href="{{ route('agent.dashboard') }}">{{ route('agent.dashboard') }}</a>
    and publish the property again.</p>
<table>
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Publish Date</th>
        <th>Expiry Date</th>
    </tr>
    @foreach ($props as $prop)
        <tr>
            <td> {{$prop['name']}} </td>
            <td> {{$prop['desc']}} </td>
            <td> {{$prop['pub_date']}} </td>
            <td> {{$prop['exp_date']}} </td>
        </tr>
    @endforeach
</table>