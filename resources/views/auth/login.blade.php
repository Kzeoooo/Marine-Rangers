<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Marine Rangers</title>
</head>
<body>
    <!-- TODO frontend: ganti dengan desain dari mockup -->
    <div style="max-width: 400px; margin: 50px auto; font-family: sans-serif;">
        <h2>Masuk ke Marine Rangers</h2>

        @if ($errors->any())
            <div style="color: red; margin-bottom: 15px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="password">Kata Sandi:</label><br>
                <input type="password" id="password" name="password" required style="width: 100%; padding: 8px;">
            </div>

            <button type="submit" style="padding: 10px 15px;">Masuk</button>
        </form>

        <p style="margin-top: 15px;">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </p>
    </div>
</body>
</html>