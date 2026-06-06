<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PanganController extends Controller
{
    // 1. Menampilkan halaman utama, data donasi, dan data notifikasi
    // 1. Menampilkan halaman utama, data donasi, dan data notifikasi
    public function index()
    {
        // 1. Ambil semua data donasi untuk admin/umum
        $allDonations = Donation::with('user')->latest()->get();

        // 2. Ambil donasi yang statusnya masih 'Tersedia' saja (untuk Komunitas)
        $availableDonations = Donation::query()->where('status', 'Tersedia')->latest()->get();

        // ==========================================
        // TAMBAHKAN KODE HITUNG STATISTIK DI SINI:
        // ==========================================

        // Hitung total porsi makanan dari donasi yang sudah tidak 'Tersedia' (artinya sudah diklaim/selesai)
        // Jika ingin menghitung SEMUA donasi tanpa peduli status, hapus bagian ->where(...) nya.
        $totalPorsi = Donation::query()->where('status', '!=', 'Tersedia')->sum('portions');

        // Hitung jumlah akun dengan role 'horeka' yang terdaftar
        $totalHoreka = User::query()->where('role', 'horeka')->count();

        // Hitung jumlah akun dengan role 'komunitas' yang terdaftar
        $totalKomunitas = User::query()->where('role', 'komunitas')->count();

        // ==========================================

        // Menyiapkan variabel default untuk notifikasi
        $notifications = [];
        $unreadCount = 0;

        // Jika user sudah masuk (login), ambil data notifikasi milik dia dengan query()
        if (Auth::check()) {
            $notifications = Notification::query()->where('user_id', Auth::id())
                ->latest()
                ->get();

            // Hitung jumlah notifikasi yang belum dibaca
            $unreadCount = Notification::query()->where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();
        }

        // Masukkan tiga variabel baru tadi (totalPorsi, totalHoreka, totalKomunitas) ke dalam compact()
        return view('pangan', compact(
            'allDonations',
            'availableDonations',
            'notifications',
            'unreadCount',
            'totalPorsi',
            'totalHoreka',
            'totalKomunitas'
        ));
    }

    // 2. Logika Registrasi Pengguna Baru
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:horeka,komunitas',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status_verifikasi' => $request->role === 'admin' ? 'disetujui' : 'menunggu',
        ]);

        return redirect()->back()->with('success', 'Registrasi berhasil! Akun Anda menunggu verifikasi Admin.');
    }

    // 3. Logika Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Cek status verifikasi terlebih dahulu
            if (Auth::user()->status_verifikasi === 'menunggu') {
                Auth::logout();
                return redirect()->back()->with('error', 'Akun Anda belum disetujui oleh Admin.');
            } elseif (Auth::user()->status_verifikasi === 'ditolak') {
                Auth::logout();
                return redirect()->back()->with('error', 'Maaf, pendaftaran akun Anda ditolak.');
            }

            return redirect()->back()->with('success', 'Selamat datang kembali, ' . Auth::user()->name);
        }

        return redirect()->back()->with('error', 'Email atau password salah.');
    }

    // 4. Logika Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Berhasil keluar sistem.');
    }

    // 5. HOREKA: Membuat Donasi Makanan Baru + Otomatis Kirim Notifikasi
    public function storeDonation(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string',
            'portions' => 'required|integer|min:1',
            'pickup_address' => 'required|string',
            'contact_number' => 'required|string',
            'pickup_time' => 'required|string',
        ]);

        // Simpan data donasi makanan
        Donation::create([
            'user_id' => Auth::id(),
            'restaurant_name' => $request->restaurant_name,
            'portions' => $request->portions,
            'pickup_address' => $request->pickup_address,
            'contact_number' => $request->contact_number,
            'pickup_time' => $request->pickup_time,
            'status' => 'Tersedia'
        ]);

        // --- CODE BARU: Sistem Notifikasi Real-Time ---
        // 1. Cari semua data user yang rolenya 'komunitas'
        $komunitasUsers = User::query()->where('role', 'komunitas')->get();
        // 2. Loop/buat baris notifikasi baru untuk masing-masing user komunitas tersebut
        foreach ($komunitasUsers as $komunitas) {
            Notification::create([
                'user_id' => $komunitas->id,
                'message' => "Donasi Baru! " . $request->restaurant_name . " membagikan " . $request->portions . " porsi makanan.",
                'is_read' => false
            ]);
        }
        // ----------------------------------------------

        return redirect()->back()->with('success', 'Donasi makanan berhasil dipublikasikan!');
    }

    // 6. KOMUNITAS: Mengklaim Donasi Makanan
    public function claimDonation($id)
    {
        $donation = Donation::findOrFail($id);

        if ($donation->status !== 'Tersedia') {
            return redirect()->back()->with('error', 'Maaf, makanan sudah diklaim pihak lain.');
        }

        $donation->update([
            'status' => 'Diklaim',
            'claimed_by_user_id' => Auth::id()
        ]);

        // OOTOMATIS HILANGKAN ANGKA LONCENG KHUSUS UNTUK DONASI DARI RESTORAN INI
        \App\Models\Notification::query()
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->where('message', 'like', '%' . $donation->restaurant_name . '%')
            ->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Donasi berhasil diklaim! Silakan ambil ke lokasi.');
    }
}
