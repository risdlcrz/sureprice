@extends('layouts.app')

@section('is_landing', true)

@section('content')
<!-- Full-width Carousel -->
<div id="landingCarousel" class="carousel slide mb-0" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 landing-carousel-img" alt="Construction site">
            <div class="carousel-caption-custom" data-aos="fade-up">
                <h1>Build with Confidence</h1>
                <p>Your trusted partner for construction, renovation, and project management. Get transparent pricing, expert teams, and seamless project delivery.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=1200&q=80" class="d-block w-100 landing-carousel-img" alt="Modern interior">
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
<div class="container text-center my-5 pb-2">
    <h1 class="display-4 fw-bold mb-3 text-success">Welcome to SurePrice</h1>
    <p class="lead text-secondary mb-0">Your all-in-one platform for Construction Project & Procurement Management</p>
    @if(auth()->check() && auth()->user()->user_type === 'company' && auth()->user()->company && auth()->user()->company->designation === 'client')
        <a href="{{ route('client.dashboard') }}" class="btn btn-primary btn-lg mt-4">Go to Dashboard</a>
    @endif
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

<div class="bg-light py-5">
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
        " class="btn btn-lg btn-success px-5 py-3 join-btn">
            {{ auth()->check() ? 'Go to Dashboard' : 'Sign Up / Log In' }}
        </a>
        <p class="mt-3 text-muted">Join us to access personalized quotations and more features for your projects.</p>
    </div>
</div>

@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<style>
body {
    background: #f4f7f6;
    font-family: 'Poppins', Arial, sans-serif;
}
.landing-navbar {
    background: #1b5e20 !important;
    color: #fff !important;
    border-radius: 0;
    box-shadow: 0 4px 24px 0 rgba(27, 94, 32, 0.10);
    margin-bottom: 0;
}
.landing-carousel-img {
    height: 440px;
    max-height: 50vh;
    object-fit: cover;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    filter: brightness(0.80);
    margin-bottom: 0;
    box-shadow: 0 8px 32px 0 rgba(27,94,32,0.13);
}
.carousel-caption-custom {
    position: absolute;
    top:20%;
    left: 32%;
    transform: translate(-50%, -50%);
    color: #fff;
    background: rgba(34, 70, 34, 0.72);
    padding: 2.2rem 3.2rem;
    border-radius: 1.7rem;
    box-shadow: 0 4px 24px 0 rgba(56, 142, 60, 0.18);
    z-index: 3;
    width: 80vw;
    max-width: 700px;
    text-align: center;
    backdrop-filter: blur(2px);
    margin: 0;
    right: auto;
    bottom: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
}
.carousel-caption-custom h1, .carousel-caption-custom h2 {
    font-family: 'Poppins', Arial, sans-serif;
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 0.7rem;
    letter-spacing: 1px;
    color: #fff;
    text-shadow: 0 2px 8px rgba(0,0,0,0.18);
}
.carousel-caption-custom p {
    font-family: 'Poppins', Arial, sans-serif;
    font-size: 1.2rem;
    font-weight: 400;
    color: #e0f2f1;
    margin-bottom: 1.5rem;
    text-shadow: 0 2px 8px rgba(0,0,0,0.13);
}
.carousel-caption-custom .hero-btn {
    font-family: 'Poppins', Arial, sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 2rem;
    padding: 0.7rem 2.2rem;
    background: #43a047;
    color: #fff;
    border: none;
    box-shadow: 0 4px 16px 0 rgba(56, 142, 60, 0.13);
    transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.2s;
    margin-top: 0.5rem;
}
.carousel-caption-custom .hero-btn:hover {
    background: #1b5e20;
    color: #fff;
    transform: scale(1.05);
    box-shadow: 0 8px 32px 0 rgba(56, 142, 60, 0.18);
}
.carousel-inner::after {
    content: '';
    position: absolute;
    left: 0; right: 0; top: 0; bottom: 0;
    background: linear-gradient(180deg,rgba(27,94,32,0.18) 0%,rgba(27,94,32,0.28) 100%);
    pointer-events: none;
    z-index: 2;
}
.catalogue-bg {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f9fafb 60%, #e8f5e9 100%);
    padding: 40px 0;
}
.catalogue-heading {
    text-align: center;
    margin-bottom: 2.5rem;
    font-size: 2.5rem;
    font-weight: 700;
    color: #388e3c;
    letter-spacing: 1px;
}
.category-card {
    border-radius: 2rem;
    box-shadow: 0 8px 40px 0 rgba(27,94,32,0.10);
    margin: 0 auto 2.5rem auto;
    max-width: 1200px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
    background: #fff;
    display: flex;
    align-items: center;
}
.category-card:hover {
    transform: translateY(-8px) scale(1.01);
    box-shadow: 0 16px 48px 0 rgba(56, 142, 60, 0.18);
}
.category-img {
    width: 100%;
    max-width: 340px;
    height: 220px;
    object-fit: cover;
    border-radius: 1.5rem;
    box-shadow: 0 4px 24px 0 rgba(56, 142, 60, 0.13);
    background: #e8f5e9;
    transition: box-shadow 0.2s;
}
.category-card:hover .category-img {
    box-shadow: 0 8px 32px 0 rgba(56, 142, 60, 0.22);
}
.category-content {
    background: #fff;
    border-radius: 1.5rem;
    box-shadow: 0 2px 12px 0 rgba(56, 142, 60, 0.07);
    padding: 2rem;
    width: 100%;
}
.ask-quote-btn, .join-btn {
    font-size: 1.1rem;
    border-radius: 2rem;
    box-shadow: 0 4px 16px 0 rgba(56, 142, 60, 0.10);
    font-weight: 600;
    letter-spacing: 0.5px;
}
.ask-quote-btn:hover {
    background: #1b5e20 !important;
    color: #fff !important;
    box-shadow: 0 8px 32px 0 rgba(56, 142, 60, 0.18);
}
.text-success {
    color: #388e3c !important;
}
.bg-success {
    background-color: #388e3c !important;
}
@media (max-width: 900px) {
    .category-card {
        flex-direction: column;
        border-radius: 1.2rem;
        margin-bottom: 1.5rem;
    }
    .category-img {
        max-width: 100%;
        height: 140px;
        border-radius: 1.2rem;
    }
    .category-content {
        border-radius: 1.2rem;
        padding: 1.2rem !important;
    }
    .landing-carousel-img {
        height: 220px;
        max-height: 30vh;
    }
    .carousel-caption-custom {
        padding: 1.1rem 1.2rem;
        font-size: 1.1rem;
        width: 95vw;
        max-width: 98vw;
        border-radius: 1.1rem;
        top: 35%;
        left: 45%;
        transform: translate(-50%, -50%);
        text-align: center;
        align-items: center;
    }
    .carousel-caption-custom h1, .carousel-caption-custom h2 {
        font-size: 1.3rem;
    }
    .carousel-caption-custom p {
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
AOS.init({
    duration: 700,
    once: true
});
document.getElementById('requestAllServicesBtn').addEventListener('click', function() {
    const checked = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => cb.value);
    if (checked.length === 0) {
        alert('Please select at least one service.');
        return;
    }
    // Redirect to quotation form with selected categories as query params
    const params = checked.map(cat => 'category[]=' + encodeURIComponent(cat)).join('&');
    window.location.href = '{{ route('client.quotation.create') }}' + '?' + params;
});
</script>
@endpush 