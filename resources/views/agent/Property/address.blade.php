@extends('layouts.agents.default1')


@section('title', 'Details | ' . $property?->name)

@section('content')
    @php
        $country_name = "United States";
        if(isset($_POST)){
            if(isset($property)){
                $data =  $property->id;
                $agent_id = $property->agent_id;
                $unique_url = $property->unique_url;
                $address_line_1 = $property->address_line_1;
                $address_line_2 = $property->address_line_2;
                $city = $property->city;
                $state = $property->state_id;
                $zip = $property->zip;
                $country_id = $property->country_id;
            }
            else{
                $data = "";
                $address_line_1 = "";
                $city = "";
                $state = "";
                $zip = "";
            }
        } else {
            $data = "";

            $unique_url = old('unique_url');
            $address_line_1 = old('address_line_1');
            $address_line_2 = old('address_line_2');
            $city = old('city');
            $state = old('state') ;
            $zip = old('zip');
            $country_id = old('country_id');
        }
    @endphp
        @if(request()->has('newSignup') && request()->get('newSignup') == true)
            <div class="welcome-banner text-center">
                <img src="{{ asset('images/icon-house-3d.png') }}" alt="Welcome Icon" class="banner-icon">
                <h1>Welcome to <span class="brand-name">{{ config('app.name') }}</span></h1>
                <p>Your journey to a beautifully designed, story-driven property website begins here. Enter your property details below and let us transform them into an exceptional digital showcase.</p>
            </div>
     @endif

    <div class="w-full rounded">
        <form action="{{url('agent/property/store-address', $data)}}"  id="amenitesForm"  method="post">
            @csrf
            <div class="w-full">
                <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
                    <h5 class="mb-0">Property Address</h5>
                </div>
                <div class="w-full bg-grey-100 border-1 p-5 form-bg">
                    <div class="flex my-5 responsive-row">
                        <div class="w-1/2 w-50 input-group input-group-outline mx-1">
                            <span>Street 1 :</span>
                            <input type="text" id="address_line_1" name="address_line_1" maxlength="100" value="{{isset($address_line_1) ? $address_line_1 : $address_line_1 }}"  style="border:2px solid lightgrey;" class="form-control" placeholder="" />
                        </div>
                        <div class="w-1/2 w-50 input-group input-group-outline mx-1">
                            <span>Street 2 :</span>
                            <input type="text" id="address_line_2" name="address_line_2" maxlength="255" value="{{isset($address_line_2) ? $address_line_2 : '' }}" style="border:2px solid lightgrey;" class="form-control" placeholder="" />
                        </div>
                    </div>

                    <div class="flex my-5 responsive-row">
                        <div class="w-1/2 w-50 input-group input-group-outline mx-1">
                            <span>City :</span>
                            <input type="text" id="city" name="city" maxlength="50" style="border:2px solid lightgrey;" value="{{$city}}" class="form-control" placeholder="" />
                        </div>
                        <div class="w-1/2 w-50 input-group input-group-outline mx-1 is-filled">
                            <span>State :</span>
                            <select class="form-control" name="state_name" style="border:2px solid lightgrey;" id="state_id" maxlength="50">
                                <option value="{{isset($property->state) ? $property->state->name : $state}}" selected>{{isset($property->state) ? $property->state->name : $state}}</option>
                                @foreach($states as $state)
                                    <option value="{{isset($state) ? $state->name : $state}}">{{$state->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex my-5 responsive-row">
                        <div class="w-1/2 w-50 input-group input-group-outline mx-1">
                            <span>Zip :</span>
                            <input type="text" id="zip" name="zip" maxlength="10" value="{{$zip}}" style="border:2px solid lightgrey;" class="form-control" placeholder="" />
                        </div>
                        <div class="w-1/2 w-50 input-group input-group-outline mx-1">
                            <span>Country :</span><br>
                            <input type="text" value="{{isset($property->country) ? $property->country->name : $country_name}}" class="form-control" style="border:2px solid lightgrey;" disabled/>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="d-flex align-items-center button button-blue font-bold text-base mb-0 mt-5 justify-content-end d-flex ml-auto" data-ripple-light="true"> Save & Next <i class="fa fa-arrow-right ml-2"></i></button>
        </form>
    </div>
@stop