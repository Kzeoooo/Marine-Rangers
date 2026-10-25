<!DOCTYPE html>
<html lang="id">
<head><title>Admin - Marine Rangers</title></head>
<body style="font-family: sans-serif; text-align: center; margin-top: 50px;">
    <!-- TODO frontend: ganti dengan desain dari mockup -->
    <h1>Placeholder Halaman Panel Admin (Khusus Admin Sistem)</h1>
    <p>Selamat datang, {{ Auth::user()->nama ?? 'Admin' }}!</p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="padding: 8px 15px; background: red; color: white; border: none;">Logout</button>
    </form>
</body>
</html>