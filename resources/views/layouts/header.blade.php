<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}"><i class="fas fa-map-marked-alt"></i> Wisata Malang</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Beranda</a></li>
            </ul>

            <ul class="navbar-nav ms-auto">
                {{-- Jika User Belum Login --}}
                @guest
                    <li class="nav-item"><a class="nav-link" href="{{ url('login') }}">Login</a></li>
                    <li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2 mt-1"
                            href="{{ url('register') }}">Daftar</a></li>
                    {{-- Jika User Sudah Login --}}
                @else
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('rekomendasi') ? 'active' : '' }}"
                            href="{{ url('/rekomendasi') }}">
                            Rekomendasi Cerdas (SAW)
                        </a>
                    </li>
                    {{-- Menu Khusus Admin (Level 1) --}}
                    @if (Auth::user()->level_id == 1)
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin') }}">Dashboard Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ url('kriteria') }}">Bobot SAW</a></li>
                        <li class="nav-item">
                            <a href="{{ url('user') }}"
                                class="nav-link {{ isset($activeMenu) && $activeMenu == 'user' ? 'active' : '' }}">
                                <i class="fas fa-users-cog"></i> Manajemen User
                            </a>
                        </li>
                    @endif

                    {{-- Menu Dropdown User/Admin --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->nama }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            {{-- Menu Profil HANYA untuk User Biasa (Level selain 1) --}}
                            @if (Auth::user()->level_id != 1)
                                <a class="dropdown-item" href="{{ url('profile') }}"><i class="fas fa-id-card"></i> Profil
                                    Saya</a>
                                <div class="dropdown-divider"></div>
                            @endif

                            <form action="{{ url('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
