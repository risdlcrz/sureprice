@extends('layouts.app')

@section('is_landing', true)
@section('title', 'SurePrice — Construction & Procurement, Priced Right')

@php
    $user = auth()->user();
    $isClient = $user && (($user->user_type === 'company' && optional($user->company)->designation === 'client') || $user->role === 'client');
    $dashboardUrl = $user ? route($user->homeRoute()) : null;

    $catalogue = [
        [
            'name' => 'Fit-outs',
            'icon' => 'couch',
            'desc' => 'Interior and finishing solutions for modern spaces, including partitions, flooring, and cabinetry.',
            'img' => asset('images/fitout.jpg'),
            'scopes' => [
                ['name' => 'Drywall Installation', 'desc' => 'Professional installation of drywall partitions and ceilings.'],
                ['name' => 'Tile Installation', 'desc' => 'Expert tile laying for floors and walls.'],
                ['name' => 'Cabinetry Installation', 'desc' => 'Custom cabinetry solutions for kitchens, offices, and storage.'],
            ],
        ],
        [
            'name' => 'Painting',
            'icon' => 'paint-roller',
            'desc' => 'Professional painting services for interiors and exteriors, including surface prep and finishing.',
            'img' => asset('images/painting.jpg'),
            'scopes' => [
                ['name' => 'Painting Crew', 'desc' => 'High-quality painting for all surfaces.'],
                ['name' => 'Drywall Finishing', 'desc' => 'Smooth and seamless drywall finishing.'],
            ],
        ],
        [
            'name' => 'MEPFS',
            'icon' => 'bolt',
            'desc' => 'Mechanical, Electrical, Plumbing, Fire Protection, and Sanitary works for safe and efficient buildings.',
            'img' => asset('images/mepf.jpeg'),
            'scopes' => [
                ['name' => 'Fireproofing Spray', 'desc' => 'Application of fireproofing materials.'],
                ['name' => 'Electrical Wiring', 'desc' => 'Safe and code-compliant electrical wiring.'],
                ['name' => 'Plumbing Pipes', 'desc' => 'Installation of plumbing pipes for water and drainage.'],
            ],
        ],
        [
            'name' => 'Infrastructure',
            'icon' => 'building',
            'desc' => 'Structural and finishing works for durable, long-lasting spaces.',
            'img' => asset('images/infra.jpg'),
            'scopes' => [
                ['name' => 'Vinyl Flooring', 'desc' => 'Installation of durable and stylish vinyl flooring.'],
                ['name' => 'Concrete Waterproofing', 'desc' => 'Advanced concrete waterproofing solutions.'],
            ],
        ],
    ];

    $steps = [
        ['file-signature', 'Describe your project', 'Pick the services you need and tell us about each room or area.'],
        ['scale-balanced', 'Get a transparent quote', 'Material and labour costs are itemised, compared across accredited suppliers.'],
        ['handshake', 'Sign the contract', 'Review, sign and approve your contract online — no paperwork back and forth.'],
        ['chart-line', 'Track to completion', 'Follow tasks, deliveries and payments on your project timeline.'],
    ];

    $features = [
        ['tags', 'Data-Driven Supplier Scoring', 'Evaluate vendors consistently across price and performance metrics instead of sorting through disconnected quotes.'],
        ['diagram-project', 'Streamlined Bid-to-Award Workflow', 'Move from sourcing to approval faster with fewer manual handoffs and bottlenecks.'],
        ['money-check-dollar', 'Disciplined Budget Control', 'Allocate orders strategically to protect project margins and minimize costly last-minute changes.'],
        ['comments', 'Traceable, Contract-Ready Records', 'Maintain clear documentation and formal contract previews that support accountability and faster sign-off.'],
    ];
@endphp

@section('content')
<!-- Hero -->
<section class="lp-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 lp-reveal">
                <span class="lp-eyebrow"><i class="fas fa-hard-hat me-2"></i>Construction &amp; procurement management</span>
                <h1 class="lp-hero-title">Streamline Procurement.<br><span>Maximize Project Margins.</span></h1>
                <p class="lp-hero-text">
                    SurePrice empowers construction firms to replace manual bidding bottlenecks with intelligent supplier evaluation
                    and automated cost optimization—turning complex procurement workflows into fast, data-driven decisions.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('client.quotation.create') }}" class="btn btn-light btn-lg lp-btn-primary">
                        <i class="fas fa-file-invoice me-2"></i>Request a Quotation
                    </a>
                    @auth
                        <a href="{{ $dashboardUrl }}" class="btn btn-outline-light btn-lg">Go to Dashboard</a>
                    @else
                        <a href="#services" class="btn btn-outline-light btn-lg">Explore Services</a>
                    @endauth
                </div>
                <ul class="lp-hero-points">
                    <li><i class="fas fa-check-circle"></i>Itemised material &amp; labour costs</li>
                    <li><i class="fas fa-check-circle"></i>Accredited, ranked suppliers</li>
                    <li><i class="fas fa-check-circle"></i>Track progress online</li>
                </ul>
            </div>
            <div class="col-lg-6 lp-reveal" style="--delay: .1s">
                <div class="lp-collage" aria-hidden="true">
                    <img src="{{ asset('images/fitout.jpg') }}" alt="" class="lp-collage-a">
                    <img src="{{ asset('images/infra.jpg') }}" alt="" class="lp-collage-b">
                    <img src="{{ asset('images/painting.jpg') }}" alt="" class="lp-collage-c">
                    <div class="lp-collage-badge">
                        <i class="fas fa-shield-halved"></i>
                        <div><strong>Transparent pricing</strong><span>No hidden costs in your quote</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How it works -->
<section class="lp-section" id="how-it-works">
    <div class="container">
        <div class="lp-section-head lp-reveal">
            <span class="lp-kicker">How it works</span>
            <h2>From idea to handover in four steps</h2>
        </div>
        <div class="row g-4">
            @foreach($steps as $n => [$icon, $title, $text])
                <div class="col-sm-6 col-lg-3 lp-reveal" style="--delay: {{ $n * 0.08 }}s">
                    <div class="lp-step">
                        <span class="lp-step-num">{{ $n + 1 }}</span>
                        <span class="lp-step-icon"><i class="fas fa-{{ $icon }}"></i></span>
                        <h3>{{ $title }}</h3>
                        <p>{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Services -->
<section class="lp-section lp-section-alt" id="services">
    <div class="container">
        <div class="lp-section-head lp-reveal">
            <span class="lp-kicker">Service catalogue</span>
            <h2>What we can build for you</h2>
            <p>Ask for a quotation on a single service, or combine several in one request.</p>
        </div>

        <div class="row g-4">
            @foreach($catalogue as $i => $cat)
                <div class="col-md-6 lp-reveal" style="--delay: {{ ($i % 2) * 0.08 }}s">
                    <article class="lp-service">
                        <div class="lp-service-img">
                            <img src="{{ $cat['img'] }}" alt="{{ $cat['name'] }}" loading="lazy">
                            <span class="lp-service-icon"><i class="fas fa-{{ $cat['icon'] }}"></i></span>
                        </div>
                        <div class="lp-service-body">
                            <h3>{{ $cat['name'] }}</h3>
                            <p>{{ $cat['desc'] }}</p>
                            <ul class="lp-scopes">
                                @foreach($cat['scopes'] as $scope)
                                    <li title="{{ $scope['desc'] }}"><i class="fas fa-check"></i>{{ $scope['name'] }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('client.quotation.create', ['category' => $cat['name']]) }}" class="btn btn-primary">
                                Ask for Quotation <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>

        <div class="lp-multi lp-reveal">
            <div>
                <h3>Need more than one service?</h3>
                <p>Select any combination and get a single quotation covering every scope of work.</p>
            </div>
            <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#allServicesModal">
                <i class="fas fa-layer-group me-2"></i>Combine Services
            </button>
        </div>
    </div>
</section>

<!-- Features -->
<section class="lp-section" id="why-sureprice">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 lp-reveal">
                <h2 class="lp-h2">Why SurePrice</h2>
                <p class="lp-muted">
                    Purpose-built to bring structure, speed, and financial clarity to construction procurement—from
                    supplier evaluation to final award.
                </p>
                <img src="{{ asset('images/mepf.jpeg') }}" alt="MEPFS works in progress" class="lp-feature-img" loading="lazy">
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    @foreach($features as $n => [$icon, $title, $text])
                        <div class="col-sm-6 lp-reveal" style="--delay: {{ $n * 0.08 }}s">
                            <div class="lp-feature">
                                <span class="lp-feature-icon"><i class="fas fa-{{ $icon }}"></i></span>
                                <h3>{{ $title }}</h3>
                                <p>{{ $text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Join -->
<section class="lp-join">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-md-6 lp-reveal">
                <div class="lp-join-card">
                    <i class="fas fa-house-chimney"></i>
                    <h3>Planning a project?</h3>
                    <p>Create a client account to request quotations, sign contracts and follow your project online.</p>
                    @if($isClient)
                        <a href="{{ $dashboardUrl }}" class="btn btn-light">Go to My Dashboard</a>
                    @elseif(!$user)
                        <a href="{{ route('register') }}" class="btn btn-light">Sign Up as a Client</a>
                    @endif
                </div>
            </div>
            <div class="col-md-6 lp-reveal" style="--delay: .08s">
                <div class="lp-join-card lp-join-card-alt">
                    <i class="fas fa-truck-ramp-box"></i>
                    <h3>Supply construction materials?</h3>
                    <p>Join as an accredited supplier, list your materials and receive requests for quotation and purchase orders.</p>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-light">Become a Supplier</a>
                    @endguest
                </div>
            </div>
        </div>
        @guest
            <p class="lp-join-note">Already have an account? <a href="{{ route('login.form') }}">Log in</a></p>
        @endguest
    </div>
</section>

<!-- Footer -->
<footer class="lp-footer">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('images/sureprice_logo.png') }}" alt="SurePrice" class="lp-footer-logo">
            <span>&copy; {{ date('Y') }} SurePrice. All rights reserved.</span>
        </div>
        <nav class="d-flex gap-4">
            <a href="#services">Services</a>
            <a href="{{ route('privacy.policy') }}">Privacy Policy</a>
            <a href="{{ route('terms.conditions') }}">Terms &amp; Conditions</a>
        </nav>
    </div>
</footer>

<!-- Combine services modal -->
<x-modal-dialog id="allServicesModal" dialog-class="modal-lg modal-dialog-centered">
    <x-slot:title>Select Services for Quotation</x-slot:title>
    <div class="modal-body">
        <form id="allServicesForm">
            <div class="row g-3">
                @foreach($catalogue as $i => $cat)
                    <div class="col-md-6">
                        <label class="lp-pick" for="serviceCheck{{ $i }}">
                            <input class="form-check-input service-checkbox" type="checkbox" value="{{ $cat['name'] }}" id="serviceCheck{{ $i }}">
                            <span>
                                <strong><i class="fas fa-{{ $cat['icon'] }} me-2"></i>{{ $cat['name'] }}</strong>
                                <small>{{ collect($cat['scopes'])->pluck('name')->join(', ') }}</small>
                            </span>
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="text-danger small mt-3 d-none" id="allServicesError">Please select at least one service.</div>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="requestAllServicesBtn">Request Quotation</button>
    </div>
</x-modal-dialog>
@endsection

@push('styles')
    @vite('resources/css/pages/landing-catalogue.css')
@endpush

@push('scripts')
    <script>window.__page = Object.assign(window.__page || {}, { quotationUrl: {{ Js::from(route('client.quotation.create')) }} });</script>
    @vite('resources/js/landing-catalogue.js')
@endpush
