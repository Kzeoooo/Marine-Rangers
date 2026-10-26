<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Marine Rangers</title>
</head>
<body style="font-family: system-ui, -apple-system, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; text-align: center; padding: 20px;">
    <h1>Marine Rangers</h1>
    <p>Platform pelaporan dan pemantauan kebersihan pesisir pantai untuk menjaga kelestarian lingkungan laut bersama.</p>
    <div style="margin-top: 1rem; display: flex; gap: 1rem; justify-content: center;">
        <a href="{{ route('login') }}" style="padding: 0.5rem 1rem; text-decoration: none; color: #fff; background-color: #007bff; border-radius: 4px;">Masuk</a>
        <a href="{{ route('register') }}" style="padding: 0.5rem 1rem; text-decoration: none; color: #333; border: 1px solid #ccc; border-radius: 4px;">Daftar</a>
    </div>
</body>
</html>
