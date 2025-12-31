<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Localization Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1>Simple Localization Demo</h1>

        <div class="alert alert-info">
            <strong>Current Locale:</strong> {{ App::getLocale() }}
        </div>

        <div class="card mb-3">
            <div class="card-header">Hero Section</div>
            <div class="card-body">
                <h2>{{ __('home.hero.title') }}</h2>
                <h3>{{ __('home.hero.subtitle') }}</h3>
                <p>{{ __('home.hero.description') }}</p>
                <button class="btn btn-primary">{{ __('home.hero.cta_button') }}</button>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">Menu</div>
            <div class="card-body">
                <p>{{ __('home.menu.my_profile') }}</p>
                <p>{{ __('home.menu.notifications') }}</p>
                <p>{{ __('home.menu.settings') }}</p>
            </div>
        </div>

        <div class="btn-group">
            <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline-primary">English</a>
            <a href="{{ route('lang.switch', 'vi') }}" class="btn btn-outline-primary">Tiếng Việt</a>
        </div>

        <br><br>
        <a href="{{ route('home') }}" class="btn btn-secondary">Back to Home</a>
    </div>
</body>
</html>
