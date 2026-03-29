<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>Over-50 Game</title>
<style>
 /* ★ 見た目を整えるだけ（Tailwind なし版） */
 body{font-family:sans-serif;text-align:center;margin-top:40px}
 img{width:90px;margin:4px}
 .btn{display:inline-block;margin:8px;padding:.5rem 1rem;
      background:#0d6efd;color:#fff;text-decoration:none;border-radius:4px}
 .disabled{background:#aaa;pointer-events:none}
</style>
</head>
<body>
 <h1>50 を超えたらアウト！</h1>

 <!-- ★ 現在の合計点を表示 -->
 <h2>合計点：{{ $total }}</h2>
 @if (isset($danger) && $danger)
    <div style="color: orange;">
        <p>35↑</p>
    </div>
 @endif

 @if (isset($win) && $win)
    <div style="color: red;">
        <p>勝利</p>
    </div>
 @endif
 
 @if (!$game_over)
   <form method="POST" action="{{ route('over50.draw') }}">
     @csrf
     <button class="btn">カードを引く</button>
   </form>
 @else
   <h2 style="color:red">ゲームオーバー！</h2>
 @endif

 @if (!$game_over)
    <form method="POST" action="{{ route('over50.minus') }}">
        @csrf
        <button type="submit" class="btn">-10</button>
    </form>
 @endif

 <!-- ★ いつでもリスタート可能 -->
 <form method="POST" action="{{ route('over50.restart') }}">
   @csrf
   <button class="btn">リスタート</button>
 </form>


 <!-- ★ これまでに引いたカードを一覧表示 -->
 @if (count($history))
   <h3>引いたカード</h3>
   @foreach ($history as $c)
     <div style="display:inline-block;text-align:center">
       <img src="{{ $c['image'] }}" alt="{{ $c['code'] }}">
       <div>{{ $c['point'] }} 点</div>
     </div>
   @endforeach
 @endif
</body>
</html>