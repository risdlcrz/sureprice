@extends('layouts.app')

@section('is_landing', true)

@section('content')
<!-- Full-width Carousel -->
<div id="landingCarousel" class="carousel slide mb-0" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 landing-carousel-img" alt="Forest landscape">
            <div class="carousel-caption-custom" data-aos="fade-up">
                <h1>Build with Confidence</h1>
                <p>Your trusted partner for construction, renovation, and project management. Get transparent pricing, expert teams, and seamless project delivery.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 landing-carousel-img" alt="Mountain landscape">
            <div class="carousel-caption-custom" data-aos="fade-up">
                <h1>Modern Interiors</h1>
                <p>Transform your space with style, function, and quality craftsmanship. SurePrice makes it easy.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 landing-carousel-img" alt="Teamwork">
            <div class="carousel-caption-custom" data-aos="fade-up">
                <h1>Teamwork & Trust</h1>
                <p>Join hundreds of satisfied clients and contractors. SurePrice is your all-in-one platform for project success.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#landingCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#landingCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Welcome Text Section -->
<div class="welcome-section">
    <div class="container text-center py-5">
        <h1 class="display-4 fw-bold mb-3">Welcome to SurePrice</h1>
        <p class="lead mb-0">Your all-in-one platform for Construction Project & Procurement Management</p>
        @if(auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
            <a href="{{ route('client.dashboard') }}" class="btn btn-light btn-lg mt-4">Go to Dashboard</a>
        @endif
    </div>
</div>

@php
$catalogue = [
    [
        'name' => 'Fit-outs',
        'desc' => 'Interior and finishing solutions for modern spaces, including partitions, flooring, and cabinetry.',
        'img' => asset('images/fitout.jpg'),
        'scopes' => [
            [
                'name' => 'Drywall Installation',
                'desc' => 'Professional installation of drywall partitions and ceilings.'
            ],
            [
                'name' => 'Tile Installation',
                'desc' => 'Expert tile laying for floors and walls.'
            ],
            [
                'name' => 'Cabinetry Installation',
                'desc' => 'Custom cabinetry solutions for kitchens, offices, and storage.'
            ],
        ]
    ],
    [
        'name' => 'Painting',
        'desc' => 'Professional painting services for interiors and exteriors, including surface prep and finishing.',
        'img' => asset('images/painting.jpg'),
        'scopes' => [
            [
                'name' => 'Painting Crew',
                'desc' => 'High-quality painting for all surfaces.'
            ],
            [
                'name' => 'Drywall Finishing',
                'desc' => 'Smooth and seamless drywall finishing.'
            ],
        ]
    ],
    [
        'name' => 'MEPFS',
        'desc' => 'Mechanical, Electrical, Plumbing, Fire Protection, and Sanitary works for safe and efficient buildings.',
        'img' => asset('images/mepf.jpeg'),
        'scopes' => [
            [
                'name' => 'Fireproofing Spray',
                'desc' => 'Application of fireproofing materials.'
            ],
            [
                'name' => 'Electrical Wiring',
                'desc' => 'Safe and code-compliant electrical wiring.'
            ],
            [
                'name' => 'Plumbing Pipes',
                'desc' => 'Installation of plumbing pipes for water and drainage.'
            ],
        ]
    ],
    [
        'name' => 'Infrastructure',
        'desc' => 'Structural and finishing works for durable, long-lasting spaces.',
        'img' => asset('images/infra.jpg'),
        'scopes' => [
            [
                'name' => 'Vinyl Flooring',
                'desc' => 'Installation of durable and stylish vinyl flooring.'
            ],
            [
                'name' => 'Concrete Waterproofing',
                'desc' => 'Advanced concrete waterproofing solutions.'
            ],
        ]
    ],
];
$altColors = ['#f9fafb', '#e8f5e9'];
@endphp

<!-- Catalogue Section -->
<div class="catalogue-bg">
    <div class="container-fluid px-0" style="max-width: 1300px;">
        <div class="catalogue-heading" data-aos="fade-down">Our Service Catalogue</div>
        <!-- All Services Card -->
        <div class="category-card row align-items-center mx-0 py-5" style="background: #e3fcec;" data-aos="fade-up">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=800&q=80" alt="All Services" class="category-img shadow-sm">
            </div>
            <div class="col-md-8">
                <div class="category-content p-4">
                    <h2 class="fw-bold text-success mb-2">All Services</h2>
                    <p class="mb-4 text-secondary fs-5">Select any combination of our main services to request a quotation for multiple scopes at once.</p>
                    <button class="btn btn-success btn-lg px-5 py-2 fw-bold ask-quote-btn" data-bs-toggle="modal" data-bs-target="#allServicesModal">
                        Show All Services
                    </button>
                </div>
            </div>
        </div>
        <!-- Existing catalogue cards -->
        @foreach($catalogue as $i => $cat)
            <div class="category-card row align-items-center mx-0 py-5" style="background: {{ $altColors[$i % 2] }};" data-aos="fade-up" data-aos-delay="{{ 100 * $i }}">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    <img src="{{ $cat['img'] }}" alt="{{ $cat['name'] }} image" class="category-img shadow-sm">
                </div>
                <div class="col-md-8">
                    <div class="category-content p-4">
                        <h2 class="fw-bold text-success mb-2">{{ $cat['name'] }}</h2>
                        <p class="mb-4 text-secondary fs-5">{{ $cat['desc'] }}</p>
                        <ul class="list-unstyled mb-4">
                            @foreach($cat['scopes'] as $scope)
                                <li class="mb-3">
                                    <span class="fw-semibold text-dark"><i class="fas fa-check-circle text-success me-2"></i>{{ $scope['name'] }}</span>
                                    <span class="text-muted">- {{ $scope['desc'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="
                            @if(!auth()->check())
                                {{ route('login.form') }}
                            @elseif(auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
                                {{ route('client.quotation.create', ['category' => $cat['name']]) }}
                            @else
                                {{ route('client.dashboard') }}
                            @endif
                        " class="btn btn-success btn-lg px-5 py-2 fw-bold ask-quote-btn">
                            Ask for Quotation
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- All Services Modal -->
<div class="modal fade" id="allServicesModal" tabindex="-1" aria-labelledby="allServicesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allServicesModalLabel">Select Services for Quotation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="allServicesForm">
                    <div class="row">
                        @foreach($catalogue as $i => $cat)
                            <div class="col-md-6 mb-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input service-checkbox" type="checkbox" value="{{ $cat['name'] }}" id="serviceCheck{{ $i }}">
                                        <label class="form-check-label fw-bold text-success" for="serviceCheck{{ $i }}">
                                            {{ $cat['name'] }}
                                        </label>
                                    </div>
                                    <ul class="list-unstyled ms-3">
                                        @foreach($cat['scopes'] as $scope)
                                            <li class="mb-2">
                                                <span class="fw-semibold text-dark">{{ $scope['name'] }}</span>
                                                <span class="text-muted">- {{ $scope['desc'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="requestAllServicesBtn">Request Quotation</button>
            </div>
        </div>
    </div>
</div>

<div class="join-section py-5">
    <div class="container text-center">
        <h3 class="fw-bold mb-3 text-success">Be a part of the SurePrice team</h3>
        <a href="
            @if(!auth()->check())
                {{ route('register') }}
            @elseif((auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client') || auth()->user()->role === 'client')
                {{ route('client.dashboard') }}
            @else
                {{ route('contracts.index') }}
            @endif
        " class="btn btn-lg px-5 py-3 join-btn">
            {{ auth()->check() ? 'Go to Dashboard' : 'Sign Up / Log In' }}
        </a>
        <p class="mt-3 text-muted">Join us to access personalized quotations and more features for your projects.</p>
    </div>
</div>

@endsection

@push('styles')
    @vite(['resources/css/landing/catalogue.css'])
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@push('scripts')
    <script>
        window.landingCatalogueQuotationRoute = "{{ route('client.quotation.create') }}";
    </script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    @vite(['resources/js/landing-catalogue.js'])
@endpush 

<style>
body {
    background: #f7fafc;
    font-family: 'Poppins', 'Inter', Arial, sans-serif;
    color: #222;
}
#landingCarousel {
    width: 100vw;
    max-width: 100vw;
    margin-left: 50%;
    transform: translateX(-50%);
    border-radius: 1.5rem;
    overflow: hidden;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10), 0 2px 8px 0 rgba(0,0,0,0.08);
    position: relative;
    background: #fff;
    border: 1.5px solid #e0e0e0;
}
.landing-carousel-img {
    width: 100%;
    height: 38vw;
    min-height: 220px;
    max-height: 420px;
    object-fit: cover;
    filter: brightness(0.97) saturate(1.04);
    transition: transform 0.7s cubic-bezier(.4,2,.3,1), box-shadow 0.3s;
    border-radius: 0.5rem;
}
.carousel-item:hover .landing-carousel-img {
    transform: scale(1.03);
    box-shadow: 0 8px 32px 0 rgba(34,70,34,0.10);
}
.carousel-caption-custom {
    position: absolute;
    top: 25%;
    left: 37%;
    max-width: 480px;
    background: rgba(255,255,255,0.82);
    border-radius: 18px;
    padding: 1.7rem 2rem 1.5rem 2rem;
    color: #1b5e20;
    box-shadow: 0 4px 24px 0 rgba(31, 38, 135, 0.10);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid #e0e0e0;
    z-index: 2;
    text-align: left;
    animation: fadeInDown 1s;
    transition: box-shadow 0.3s, transform 0.3s;
}
.carousel-caption-custom:hover {
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
    transform: scale(1.03) translateY(-6px);
}
.carousel-caption-custom h1 {
    font-family: 'Poppins', 'Inter', Arial, sans-serif;
    font-size: 2.3rem;
    font-weight: 800;
    margin-bottom: 0.7rem;
    letter-spacing: 1px;
    color: #1b5e20;
    text-shadow: 0 2px 8px rgba(0,0,0,0.07);
}
.carousel-caption-custom p {
    font-size: 1.15rem;
    font-weight: 400;
    color: #333;
    margin-bottom: 0;
    text-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.ask-quote-btn {
    background: #219150;
    color: #fff !important;
    border: 2px solid #1b5e20;
    font-weight: 700;
    border-radius: 2rem;
    box-shadow: 0 2px 8px 0 rgba(34,70,34,0.10);
    transition: box-shadow 0.3s, filter 0.3s, background 0.3s, border 0.3s;
    padding: 0.8rem 2.5rem;
    font-size: 1.15rem;
    letter-spacing: 0.5px;
    outline: none;
    display: inline-block;
    opacity: 1;
    cursor: pointer;
}
.ask-quote-btn:hover, .ask-quote-btn:focus {
    background: #17693a;
    color: #fff !important;
    box-shadow: 0 4px 16px 0 #1b5e2088;
    border: 2px solid #17693a;
    filter: brightness(1.08);
    text-decoration: none;
}

.join-btn {
    background: #fff;
    color: #219150 !important;
    border: 2px solid #219150;
    font-weight: 700;
    border-radius: 2rem;
    box-shadow: 0 2px 8px 0 rgba(34,70,34,0.08);
    transition: box-shadow 0.3s, filter 0.3s, background 0.3s, color 0.3s, border 0.3s;
    padding: 0.8rem 2.5rem;
    font-size: 1.15rem;
    letter-spacing: 0.5px;
    outline: none;
    display: inline-block;
    opacity: 1;
    cursor: pointer;
}
.join-btn:hover, .join-btn:focus {
    background: #219150;
    color: #111 !important;
    border: 2px solid #17693a;
    box-shadow: 0 4px 16px 0 #1b5e2088;
    filter: brightness(1.08);
    text-decoration: none;
}

.category-card {
    background: rgba(255,255,255,0.92);
    border-radius: 1.5rem;
    box-shadow: 0 2px 8px 0 rgba(31, 38, 135, 0.06);
    margin-bottom: 2.5rem;
    transition: box-shadow 0.3s, transform 0.3s;
    animation: fadeInUp 1.2s;
}
.category-card:hover {
    box-shadow: 0 8px 32px 0 #1b5e2055;
    transform: scale(1.02) translateY(-4px);
}
.category-img {
    border-radius: 1.2rem;
    box-shadow: 0 2px 8px 0 #1b5e2044;
    transition: box-shadow 0.3s, transform 0.3s;
}
.category-img:hover {
    box-shadow: 0 8px 32px 0 #1b5e2055;
    transform: scale(1.04);
}
.catalogue-heading {
    font-family: 'Poppins', 'Inter', Arial, sans-serif;
    font-size: 2.3rem;
    font-weight: 700;
    color: #1b5e20;
    margin-bottom: 2.5rem;
    letter-spacing: 1px;
    text-align: center;
    text-shadow: 0 2px 8px #e0e0e055;
    animation: fadeInDown 1.2s;
}
.welcome-section {
    margin-top: 2.5rem;
    margin-bottom: 2.5rem;
    animation: fadeIn 1.2s;
}
.join-section {
    margin-top: 3.5rem;
    margin-bottom: 2.5rem;
    animation: fadeIn 1.2s;
}
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-40px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@media (max-width: 992px) {
    #landingCarousel {
        border-radius: 1rem;
    }
    .landing-carousel-img {
        height: 38vw;
        min-height: 140px;
        max-height: 220px;
    }
    .carousel-caption-custom {
        top: 10%;
        left: 4%;
        max-width: 90vw;
        padding: 1rem 1.2rem 1rem 1.2rem;
        border-radius: 1.2rem;
    }
    .carousel-caption-custom h1 {
        font-size: 1.2rem;
    }
    .carousel-caption-custom p {
        font-size: 0.95rem;
    }
}
</style> 