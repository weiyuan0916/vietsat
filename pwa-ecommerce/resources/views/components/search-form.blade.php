<!-- Search Form -->
<div class="container">
    <div class="search-form pt-3 rtl-flex-d-row-r">
        <form action="{{ route('search') }}" method="GET">
            <input class="form-control" type="search" name="q" placeholder="Search in {{ config('app.name') }}" value="{{ request('q') }}">
            <button type="submit"><i class="ti ti-search"></i></button>
        </form>
        <!-- Alternative Search Options -->
        <div class="alternative-search-options">
            <div class="dropdown">
                <a class="btn btn-primary dropdown-toggle" id="altSearchOption" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-adjustments-horizontal"></i>
                </a>
                <!-- Dropdown Menu -->
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="altSearchOption">
                    <li><a class="dropdown-item" href="#"><i class="ti ti-microphone"></i> Voice</a></li>
                    <li><a class="dropdown-item" href="#"><i class="ti ti-layout-collage"></i> Image</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

