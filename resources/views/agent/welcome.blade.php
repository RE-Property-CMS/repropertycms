@extends('layouts.agents.default1')

@section('title', 'Welcome to ' . config('app.name'))

@section('content')

    <div class="w-full py-5">
        <div class="pb-5">
            <div class="page-heading">
                <h3 class="mb-0 text-center">Welcome to {{ config('app.name') }}, {{ auth()->user()->first_name }}!</h3>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <p>We're thrilled to have you on board! To get started and make the most of our platform, please take a
                    moment to review the following:</p>

                <h4 class="mt-4">1. Complete Your Profile</h4>
                <p>A complete profile helps you build trust with potential clients. Please ensure you've provided the
                    following information:</p>
                <ul>
                    <li><b>Contact Details:</b> Phone number, email address, and business name (if applicable).</li>
                    <li><b>Address:</b> Your business address or preferred contact address.</li>
                    <li><b>Profile Picture:</b> A professional headshot.</li>
                    <li><b>Social Media Links:</b> (Optional) Connect your social media accounts to expand your reach.
                    </li>
                </ul>
                <a href="{{ route('agent.profile') }}" class="button button-blue">Complete Your Profile</a>


                <h4 class="mt-4">2. Create Your First Property Listing!</h4>
                <p>Start showcasing your listings today. Add details, photos, videos, and more to attract potential
                    buyers or renters.</p>
                <a href="{{ route('agent.property.addresses') }}" class="button button-green">Create Your First
                    Property</a>


                <h4 class="mt-4">3. Explore our Subscription Plans (optional)</h4>
                <p>Boost your property visibility with our Subscription plans. Purchase Subscription to enhance your
                    listings and reach a wider audience.</p>
                <a href="{{ route('pricing') }}" class="button button-yellow">View Subscription Plans</a>


                <h4 class="mt-4">Need help?</h4>
                <p>Check out our <a href="#" style="color: blue;">Help Center</a> for FAQs and tutorials. You can also
                    contact our <a href="#" style="color: blue;">Support Team</a> for personalized assistance.</p>
            </div>
        </div>

    </div>
@stop