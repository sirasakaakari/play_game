<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>One-Card Battle</title>
    <style>
        body  { font-family:sans-serif;text-align:center;margin-top:40px }
        img   { width:110px;margin:0 20px }
        .btn  { display:inline-block;margin-top:30px;padding:.5rem 1rem;
                background:#0d6efd;color:#fff;text-decoration:none;border-radius:4px }
    </style>
</head>
<body>
    <h1>One-Card Battle</h1>

    <h2>You</h2>
    <img src="{{ $player1['image'] }}">
    <img src="{{ $player2['image'] }}">
    <p>{{ $pVal3 }}</p>

    <div style="margin:20px;font-size:1.5rem;">VS</div>

    <h2>CPU</h2>
    <img src="{{ $cpu1['image'] }}">
    <img src="{{ $cpu2['image'] }}">
    <p>{{ $cVal3 }}</p>

    <h2 style="margin-top:30px;">
        Winner: <span style="color:blue">{{ $winner }}</span>
    </h2>

    <a href="{{ route('battle') }}" class="btn">Play Again</a>
</body>
</html>