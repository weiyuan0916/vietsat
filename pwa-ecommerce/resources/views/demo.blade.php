@extends('layouts.app')

@section('title', 'Localization Demo - ' . ucfirst($texts['current_locale']))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Localization Demo</h1>

            {{-- Language Switcher --}}
            <div class="mb-4">
                <h3>Language Switcher</h3>
                <div class="btn-group" role="group">
                    @foreach($texts['supported_locales'] as $locale => $name)
                        <a href="{{ route('lang.switch', $locale) }}"
                           class="btn {{ $texts['current_locale'] === $locale ? 'btn-primary' : 'btn-outline-primary' }}">
                            {{ $name }} ({{ strtoupper($locale) }})
                        </a>
                    @endforeach
                </div>
                <p class="mt-2 text-muted">Current Locale: <strong>{{ strtoupper($texts['current_locale']) }}</strong></p>
            </div>

            {{-- Hero Section Demo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Hero Section</h3>
                </div>
                <div class="card-body">
                    <h1 class="display-4">{{ $texts['hero']['title'] }}</h1>
                    <h2 class="text-primary mb-3">{{ $texts['hero']['subtitle'] }}</h2>
                    <p class="lead">{{ $texts['hero']['description'] }}</p>
                    <button class="btn btn-success btn-lg">{{ $texts['hero']['cta_button'] }}</button>
                </div>
            </div>

            {{-- Navigation Demo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Navigation</h3>
                </div>
                <div class="card-body">
                    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
                        <div class="container-fluid">
                            <a class="navbar-brand" href="#">{{ $texts['footer']['brand_name'] }}</a>
                            <div class="navbar-nav">
                                <a class="nav-link" href="#">{{ $texts['nav']['home'] }}</a>
                                <a class="nav-link" href="#">{{ $texts['nav']['features'] }}</a>
                                <a class="nav-link" href="#">{{ $texts['nav']['pricing'] }}</a>
                                <a class="nav-link" href="#">{{ $texts['nav']['blog'] }}</a>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            {{-- Menu Demo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Menu Items</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <i class="ti ti-user me-2"></i>{{ $texts['menu']['my_profile'] }}
                        </li>
                        <li class="list-group-item">
                            <i class="ti ti-bell-ringing me-2"></i>{{ $texts['menu']['notifications'] }}
                        </li>
                        <li class="list-group-item">
                            <i class="ti ti-adjustments-horizontal me-2"></i>{{ $texts['menu']['settings'] }}
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Common Text Demo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Common Texts</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Loading:</strong> {{ $texts['common']['loading'] }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Error:</strong> {{ $texts['common']['error'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Helper Functions Demo --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Helper Functions Demo</h3>
                </div>
                <div class="card-body">
                    <p><strong>Using LocalizationHelper directly:</strong></p>
                    <ul class="list-unstyled">
                    <li>• Hero Title: <code>@php echo App\Helpers\LocalizationHelper::hero('title'); @endphp</code></li>
                    <li>• Menu Profile: <code>@php echo App\Helpers\LocalizationHelper::menu('my_profile'); @endphp</code></li>
                    <li>• Footer Brand: <code>@php echo App\Helpers\LocalizationHelper::getBrandName(); @endphp</code></li>
                    <li>• Current Locale: <code>@php echo App\Helpers\LocalizationHelper::getCurrentLocale(); @endphp</code></li>
                    </ul>
                </div>
            </div>

            {{-- Instructions --}}
            <div class="alert alert-info">
                <h4>How to use this system:</h4>
                <ol>
                    <li>Use <code>@php echo App\Helpers\LocalizationHelper::hero('title'); @endphp</code> in blade templates</li>
                    <li>Or pass localized data from controllers using <code>$texts</code> array</li>
                    <li>Add new text keys to <code>resources/lang/en/home.php</code> and <code>resources/lang/vi/home.php</code></li>
                    <li>Switch languages using <code>@php echo route('lang.switch', 'vi'); @endphp</code> or <code>@php echo route('lang.switch', 'en'); @endphp</code></li>
                    <li>Middleware automatically detects locale from URL, session, or query parameter</li>
                </ol>
            </div>

            <div class="text-center">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.display-4 {
    font-weight: 700;
    color: #2c3e50;
}

.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 5px;
}

.list-group-item {
    border: none;
    padding: 1rem;
    background-color: #f8f9fa;
    margin-bottom: 0.5rem;
    border-radius: 0.375rem !important;
}

code {
    background-color: #e9ecef;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: 'Courier New', monospace;
}
</style>
