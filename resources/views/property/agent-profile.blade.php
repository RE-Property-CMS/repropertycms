<section class="bg-black">
    <div class="container">
        <div class="row m-0">
            <div class="col-6 col-md-6 py-5 mobile_position_wrap">
                <!-- Dispaly Agent Pofile photo here -->
                @if(!is_null($agent->profile_image))
                    <div class="border-0 mb-3 agent-image text-center">
                        @if(str_starts_with($agent->profile_image, 'http'))
                            <img src="{{ $agent->profile_image }}" class="d-block w-100 h-100">
                        @else
                            <img src="{{ asset('/files/agents/' . $agent->id . '/' . $agent->profile_image) }}" class="d-block w-100 h-100">
                        @endif
                    </div>
                    <div class="property-social-media text-white mobile_position">
                        <p>Share the profile</p>

                        @if(!is_null($agent->facebook_profile))
                            <a href="{{$agent->facebook_profile}}" class="text-white" target="_blank">
                                <i class="fa-brands fa-facebook-f py-3 border-end"></i>
                            </a>
                        @else
                            <a class="text-white">
                                <i class="fa-brands fa-facebook-f py-3 border-end"></i>
                            </a>
                        @endif

                        @if(!is_null($agent->instagram_profile))
                            <a href="{{ $agent->instagram_profile }}" class="text-white" target="_blank">
                                <i class="fa-brands fa-instagram py-3 border-end"></i>
                            </a>
                        @else
                            <a class="text-white">
                                <i class="fa-brands fa-instagram py-3 border-end"></i>
                            </a>
                        @endif

                        @if(!is_null($agent->twitter_profile))
                            <a href="{{ $agent->twitter_profile }}" class="text-white" target="_blank">
                                <i class="fa-brands fa-twitter py-3 border-end"></i>
                            </a>
                        @else
                            <a class="text-white">
                                <i class="fa-brands fa-twitter py-3 border-end"></i>
                            </a>
                        @endif

                        @if(!is_null($agent->linkedin_profile))
                            <a href="{{$agent->linkedin_profile}}" class="text-white" target="_blank">
                                <i class="fa-brands fa-linkedin-in py-3"></i>
                            </a>
                        @else
                            <a class="text-white">
                                <i class="fa-brands fa-linkedin-in py-3"></i>
                            </a>
                        @endif

                    </div>
                @endif

            </div>
            <div class="col-6 col-md-6 text-center py-5">
                <!-- Dispaly Agent Name here -->
                <p class="text-white text-uppercase pb-2 agent_name">{{$agent->first_name}} {{ $agent->last_name}}</p>

                <!-- Dispaly Agent logo here -->
                @if(!is_null($agent->logo_image))
                    @if(str_starts_with($agent->logo_image, 'http'))
                        <span><img src="{{ $agent->logo_image }}" class="pb-3 agent_logo" alt=""></span>
                    @else
                        <span><img src="{{ asset('/files/agents/' . $agent->id . '/' . $agent->logo_image) }}" class="pb-3 agent_logo" alt=""></span>
                    @endif
                @endif

                <!-- Dispaly Agent phone number here -->
                @if($agent_address != '')
                    @if(!is_null($agent_address->phone))
                        <p class="text-white text-uppercase">{{ $agent_address->phone }}</p>
                    @endif
                @endif
                <!-- Dispaly Agent Address here -->
                @if($agent_address != '')
                    @if(!is_null($agent_address->address && $agent_address->city && $agent_address->state && $agent_address->zip ))
                        <p class="text-white text-capitalize lh-lg">{{ $agent_address->address }}
                            , {{ $agent_address->city }}
                            <br> {{ isset($agent_address->state) ? $agent_address->state->name : '' }}
                            <br> {{ $agent_address->zip }}</p>
                    @endif
                @endif
                <div class="row d-none d-md-flex">
                    @if($property->video_credit)
                        <div class="col-12 col-md-6">
                            <p class="text-white text-capitalize lh-lg">
                                <strong>Videography</strong><br/>{{ $property->video_credit }}
                            </p>
                        </div>
                    @endif
                    @if($property->photographer_credit)
                        <div class="col-12 col-md-6">
                            <p class="text-white text-capitalize lh-lg">
                                <strong>Photography</strong><br/>{{ $property->photographer_credit }}
                            </p>
                        </div>
                    @endif
                </div>

        </div>
    </div>
</section>