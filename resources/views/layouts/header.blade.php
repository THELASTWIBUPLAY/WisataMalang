<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-map-marked-alt"></i> Wisata Malang</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/wisata') }}">Kelola Wisata</a></li>
                <li class="nav-item"><a class="nav-link" href="/rekomendasi">Rekomendasi SAW</a></li>
            </ul>
        </div>
    </div>
</nav>