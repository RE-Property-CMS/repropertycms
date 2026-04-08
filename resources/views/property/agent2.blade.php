<section class="bg-black">
    <div class="container">
        <div class="row m-0">
            <div class="col-6 col-md-6 py-5">

                <!-- Dispaly Agent Pofile photo here -->
                @if(!is_null($agents->profile_image))
                    <div class="border-0 mb-3 agent-image text-center">
                        @if(str_starts_with($agents->profile_image, 'http'))
                            <img src="{{$agents->profile_image}}" class="d-block w-100 h-100">
                        @else
                            <img src="{{asset('/files/agents/')}}/{{$agents->id}}/{{$agents->profile_image}}"
                                 alt="">
                        @endif
                    </div>
                    <div class="property-social-media text-white">
                        <p>Share the profile</p>

                        @if(!is_null($agents->facebook_profile))
                            <a href="{{$agents->facebook_profile}}" class="text-white" target="_blank">
                                <i class="fa-brands fa-facebook-f py-3 border-end"></i>
                            </a>
                        @else
                            <a class="text-white">
                                <i class="fa-brands fa-facebook-f py-3 border-end"></i>
                            </a>
                        @endif

                        @if(!is_null($agents->instagram_profile))
                            <a href="{{ $agents->instagram_profile }}" class="text-white"
                               target="_blank">
                                <i class="fa-brands fa-instagram py-3 border-end"></i>
                            </a>
                        @else
                            <a class="text-white">
                                <i class="fa-brands fa-instagram py-3 border-end"></i>
                            </a>
                        @endif

                        @if(!is_null($agents->twitter_profile))
                            <a href="{{ $agents->twitter_profile }}" class="text-white" target="_blank">
                                <i class="fa-brands fa-twitter py-3 border-end"></i>
                            </a>
                        @else
                            <a class="text-white">
                                <i class="fa-brands fa-twitter py-3 border-end"></i>
                            </a>
                        @endif

                        @if(!is_null($agents->linkedin_profile))
                            <a href="{{$agents->linkedin_profile}}" class="text-white" target="_blank">
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
                <p class="text-white text-uppercase pb-2 agent_name">{{$agents->first_name}} {{ $agents->last_name}}</p>

                <!-- Dispaly Agent logo here -->
                @if(!is_null($agents->logo_image))
                    <span>
                            @if(str_starts_with($agents->logo_image, 'http'))
                            <img src="{{$agents->logo_image}}" class="pb-3 my-4 agent_logo" alt="">
                        @else
                            <img src="{{asset('/files/agents/')}}/{{$agents->id}}/{{$agents->logo_image}}"
                                 class="pb-3 my-4 agent_logo" alt="">
                        @endif
                        </span>
                @endif

                <!-- Dispaly Agent phone number here -->
                @if($agent_address != '')
                    @if(!is_null($agent_address->phone))
                        <p class="text-white text-uppercase my-3">{{ $agent_address->phone }}</p>
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
            </div>
        </div>
    </div>
</section>