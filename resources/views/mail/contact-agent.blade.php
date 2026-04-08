Hi {{ $agent->first_name }},
<br/>
A new contact request received on {{ config('app.name') }} for your property. The details are given below.
<br/><br/>
Please try to reply as soon as possible and no later than 2 working days.<br/><br/>
The details submitted by the user:<br/>
<b>Name:</b> {{ $user["name"] }}<br/>
<b>Email:</b> {{ $user["email"] }}<br/>
<b>Phone Number:</b> {{ $user["phone"] }}<br/>
<b>Message:</b><br/>
{!! $user["message"] ?? '' !!}
<br/><br/>
Thank you for using {{ config('app.name') }}