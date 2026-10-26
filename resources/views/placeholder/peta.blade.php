<!DOCTYPE html>
<html lang="id">
<head><title>Peta - Marine Rangers</title></head>
<body style="font-family: sans-serif; text-align: center; margin-top: 50px;">
    <!-- TODO frontend: ganti dengan desain dari mockup -->
    <h1>Placeholder Halaman Peta</h1>
    <p>Selamat datang, {{ Auth::user()->nama ?? 'Pengguna' }}!</p>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="padding: 8px 15px; background: red; color: white; border: none;">Logout</button>
    </form>
</body>
</html>