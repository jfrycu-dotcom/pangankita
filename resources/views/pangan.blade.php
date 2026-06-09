<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PanganKita - Platform Distribusi Makanan Berlebih</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50 font-sans antialiased">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-emerald-600 flex items-center gap-2">
                        <i class="fas fa-leaf"></i> PanganKita
                    </span>
                </div>
                <div class="flex items-center gap-4" id="nav-buttons">
                    @auth
                        <div class="relative mr-2">
                            <button onclick="toggleNotificationDropdown()"
                                class="text-gray-500 hover:text-emerald-600 focus:outline-none relative p-2 flex items-center justify-center">
                                <i class="fas fa-bell text-xl"></i>
                                @if (isset($unreadCount) && $unreadCount > 0)
                                    <span
                                        class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <div id="notification-dropdown"
                                class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg py-2 border border-gray-100 z-50 max-h-96 overflow-y-auto">
                                <div class="px-4 py-2 font-bold text-gray-800 border-b border-gray-100">
                                    Notifikasi Masuk
                                </div>
                                @if (isset($notifications) && count($notifications) > 0)
                                    @foreach ($notifications as $notif)
                                        <div
                                            class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition {{ !$notif->is_read ? 'bg-emerald-50/60' : '' }}">
                                            <p class="text-sm text-gray-700">{{ $notif->message }}</p>
                                            <span
                                                class="text-xs text-gray-400 block mt-1">{{ $notif->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="px-4 py-6 text-center text-gray-400 text-sm">Tidak ada notifikasi baru.
                                    </div>
                                @endif
                            </div>
                        </div>
                        <span class="text-gray-700 font-medium">Halo, {{ Auth::user()->name }}
                            ({{ strtoupper(Auth::user()->role) }})</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">Keluar</button>
                        </form>
                    @else
                        <button onclick="showPage('page-home')"
                            class="text-gray-600 hover:text-emerald-600 font-medium">Home</button>
                        <button onclick="showPage('page-login')"
                            class="bg-emerald-500 text-white px-4 py-2 rounded-lg hover:bg-emerald-600 transition">Masuk</button>
                        <button onclick="showPage('page-register')"
                            class="border border-emerald-500 text-emerald-500 px-4 py-2 rounded-lg hover:bg-emerald-50 transition">Daftar</button>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 mt-4">
        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-4 shadow-sm"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-amber-100 border border-amber-400 text-amber-700 px-4 py-3 rounded relative mb-4 shadow-sm"
                role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div id="page-home" class="page-section">
            <div
                class="text-center py-12 px-4 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl shadow-sm mb-12">
                <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">
                    Hubungkan Makanan Berlebih dengan <span class="text-emerald-600">Mereka yang Membutuhkan</span>
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-8">
                    PanganKita membantu Hotel, Restoran, dan Katering (HOREKA) mendonasikan makanan layak konsumsi
                    kepada komunitas sosial dan lembaga kemanusiaan.
                </p>
                <div class="flex justify-center gap-4">
                    <button onclick="showPage('page-register')"
                        class="bg-emerald-600 text-white font-semibold px-6 py-3 rounded-xl hover:bg-emerald-700 transition shadow-md">Mulai
                        Berbagi</button>
                    <a href="#alur-kerja"
                        class="bg-white text-gray-700 border font-semibold px-6 py-3 rounded-xl hover:bg-gray-50 transition shadow-sm">Pelajari
                        Alur</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-4 bg-emerald-50 text-emerald-600 rounded-xl text-2xl"><i class="fas fa-utensils"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalPorsi ?? 0) }}+</h3>
                        <p class="text-gray-500 text-sm">Porsi Makanan Terselamatkan</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-4 bg-teal-50 text-teal-600 rounded-xl text-2xl"><i class="fas fa-hotel"></i></div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalHoreka ?? 0 }}</h3>
                        <p class="text-gray-500 text-sm">Mitra HOREKA Bergabung</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="p-4 bg-amber-50 text-amber-600 rounded-xl text-2xl"><i class="fas fa-users"></i></div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalKomunitas ?? 0 }}</h3>
                        <p class="text-gray-500 text-sm">Komunitas Penerima Manfaat</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="page-login"
            class="page-section hidden max-w-md mx-auto bg-white p-8 rounded-2xl shadow-md border border-gray-100 my-12">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Masuk ke PanganKita</h2>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <div class="relative">
                        <input type="password" id="login-password" name="password"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 pr-10"
                            required>
                        <button type="button" onclick="togglePassword('login-password', 'eye-login')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-emerald-600 focus:outline-none">
                            <i id="eye-login" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-emerald-700 transition shadow-md">Masuk</button>
            </form>
            <p class="text-sm text-gray-600 text-center mt-4">Belum punya akun? <button
                    onclick="showPage('page-register')" class="text-emerald-600 font-semibold hover:underline">Daftar
                    di
                    sini</button></p>
        </div>

        <div id="page-register"
            class="page-section hidden max-w-md mx-auto bg-white p-8 rounded-2xl shadow-md border border-gray-100 my-12">
            <h2 class="text-2xl font-bold text-gray-900 text-center mb-6">Daftar Akun Baru</h2>
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap / Instansi</label>
                    <input type="text" name="name"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" name="email"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-4">Password</label>
                    <div class="relative">
                        <input type="password" id="register-password" name="password"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 pr-10"
                            required>
                        <button type="button" onclick="togglePassword('register-password', 'eye-register')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-emerald-600 focus:outline-none">
                            <i id="eye-register" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Peran Anda</label>
                    <select name="role"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        required>
                        <option value="horeka">HOREKA (Hotel, Restoran, Katering)</option>
                        <option value="komunitas">Komunitas / Lembaga Sosial</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-emerald-700 transition shadow-md">Daftar
                    Sekarang</button>
            </form>
            <p class="text-sm text-gray-600 text-center mt-4">Sudah punya akun? <button
                    onclick="showPage('page-login')" class="text-emerald-600 font-semibold hover:underline">Masuk di
                    sini</button></p>
        </div>

        @auth
            @if (Auth::user()->role === 'horeka')
                <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6"><i
                            class="fas fa-plus-circle text-emerald-600 mr-2"></i>Formulir Donasi Makanan</h2>
                    <form action="{{ route('donations.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-semibold mb-1">Nama Restoran/Hotel</label>
                                <input type="text" name="restaurant_name"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                    required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-semibold mb-1">Jumlah Porsi</label>
                                <input type="number" name="portions" min="1"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                    required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-semibold mb-1">Alamat Penjemputan</label>
                                <textarea name="pickup_address" rows="3"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500" required></textarea>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-semibold mb-1">Nomor Kontak WhatsApp</label>
                                <input type="text" name="contact_number"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                    required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-semibold mb-1">Batas Waktu
                                    Pengambilan</label>
                                <input type="text" name="pickup_time" placeholder="Contoh: Sebelum jam 21:00 WITA"
                                    class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500"
                                    required>
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-4 bg-emerald-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-emerald-700 transition shadow-md">
                            Publikasikan Donasi
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6"><i
                        class="fas fa-box-open text-emerald-600 mr-2"></i>Donasi Makanan Tersedia</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($availableDonations as $dn)
                        <div
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition flex flex-col justify-between">
                            <div>
                                <div class="bg-emerald-600 text-white p-4">
                                    <h3 class="font-bold text-lg flex justify-between items-center">
                                        {{ $dn->restaurant_name }}
                                        <span
                                            class="bg-white text-emerald-700 text-xs px-2 py-1 rounded-full font-extrabold">{{ $dn->portions }}
                                            Porsi</span>
                                    </h3>
                                </div>
                                <div class="p-6 pb-2">
                                    <p class="text-gray-600 text-sm mb-2"><i
                                            class="fas fa-map-marker-alt text-gray-400 mr-2"></i>{{ $dn->pickup_address }}
                                    </p>
                                    <p class="text-gray-600 text-sm mb-4"><i
                                            class="fas fa-clock text-gray-400 mr-2"></i>{{ $dn->pickup_time }}</p>
                                </div>
                            </div>

                            <div class="p-6 pt-0">
                                @if (Auth::user()->role === 'komunitas')
                                    <div class="flex flex-col gap-2">
                                        <form action="{{ route('donations.claim', $dn->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-emerald-600 text-white font-bold py-2 px-4 rounded-xl hover:bg-emerald-700 transition text-center shadow-sm text-sm">
                                                Konfirmasi Klaim di Website
                                            </button>
                                        </form>

                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $dn->contact_number);
                                            if (strpos($cleanPhone, '0') === 0) {
                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                            }
                                            $waMessage = rawurlencode(
                                                'Halo ' .
                                                    $dn->restaurant_name .
                                                    ', kami dari ' .
                                                    Auth::user()->name .
                                                    ' berniat menjemput donasi makanan sebanyak ' .
                                                    $dn->portions .
                                                    ' porsi yang dijadwalkan sebelum ' .
                                                    $dn->pickup_time .
                                                    '. Apakah posisi penjemputan benar di: ' .
                                                    $dn->pickup_address .
                                                    '?',
                                            );
                                        @endphp

                                        <a href="https://api.whatsapp.com/send?phone={{ $cleanPhone }}&text={{ $waMessage }}"
                                            target="_blank"
                                            class="w-full bg-white text-emerald-600 border border-emerald-300 font-bold py-2 px-4 rounded-xl hover:bg-emerald-50 transition text-center flex items-center justify-center gap-2 text-sm shadow-sm">
                                            <i class="fab fa-whatsapp text-lg"></i> Hubungi via WhatsApp
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-gray-100 text-center py-8 rounded-xl text-gray-500">Belum ada donasi
                            makanan yang tersedia saat ini.</div>
                    @endforelse
                </div>
            </div>
        @endauth

    </main>

    <footer class="bg-white border-t mt-24 py-8 text-center text-gray-500 text-sm">
        <p>&copy; 2026 PanganKita. Ditujukan untuk Manajemen Distribusi Makanan Berlebih Indonesia.</p>
    </footer>

    <script>
        function showPage(pageId) {
            document.querySelectorAll('.page-section').forEach(section => {
                section.classList.add('hidden');
            });
            const targetPage = document.getElementById(pageId);
            if (targetPage) targetPage.classList.remove('hidden');
        }

        function togglePassword(inputId, eyeId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(eyeId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // JAVASCRIPT UNTUK DROPDOWN LONCENG
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            dropdown.classList.toggle('hidden');
        }

        window.onclick = function(event) {
            if (!event.target.closest('.relative') && !event.target.matches('.fa-bell')) {
                const dropdown = document.getElementById('notification-dropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }
    </script>
</body>

</html>
