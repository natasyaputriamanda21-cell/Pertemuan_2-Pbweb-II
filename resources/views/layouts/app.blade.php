<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title')</title>
</head>
<body>

    <header>
        <h2>Perpustakaan Mini</h2>

        <nav>
            <a href="/">Home</a> |
            <a href="/tentang">Tentang</a> |
            <a href="/kontak">Kontak</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; 2026 Perpustakaan Mini</p>
    </footer>

</body>
</html>