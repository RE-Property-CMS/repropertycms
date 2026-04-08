Hi {{ $user["name"] }},
<br/>
Your contact request on {{ config('app.name') }}, is being forwarded to the Agent "{{ $agent->first_name }} {{ $agent->last_name }}".
You should receive a response within 2-3 days.
<br/><br/>
For your reference the details submitted by you are given below:<br/>
<b>Name:</b> {{ $user["name"] }}<br/>
<b>Email:</b> {{ $user["email"] }}<br/>
<b>Phone Number:</b> {{ $user["phone"] }}<br/>
<b>Message:</b><br/>
{{ $user["message"] ?? '' }}
<br/><br/>
In case of any queries you can email the agent "{{ $agent->email }}".
<br/><br/>
Thank you for visiting {{ config('app.name') }}
