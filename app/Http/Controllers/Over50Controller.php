<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Over50Controller extends Controller
{
    /* ─────────────────────────────
       画面を表示するだけのメソッド
       ───────────────────────────── */
    public function show(Request $request)
    {
        // ★ セッションにゲーム状態が無ければ newGame() で作成
        $state = $request->session()->get('over50') ?? $this->newGame($request);

        // ★ $state（配列）をそのままビューへ渡す
        return view('over50', $state);
    }

    /* ─────────────────────────────
       カードを 1 枚引くメソッド
       ───────────────────────────── */
    public function draw(Request $request)
    {

        $verifySsl = app()->isProduction();   // 本番 → true, 開発 → false
        // ★ いま進行中のゲーム状態をセッションから取り出す
        $state = $request->session()->get('over50');
        if (!$state) {                          // ← 何も無ければ表示へ戻す
            return redirect()->route('over50.show');
        }

        // ★ Deck of Cards API に「1枚引いて」とお願い
        $draw = Http::withOptions(['verify' => $verifySsl])
            ->get("https://deckofcardsapi.com/api/deck/{$state['deck_id']}/draw/",
            ['count' => 1]);

        $card = $draw['cards'][0];              // 1 枚だけなので 0 番目

        // ★ 絵札→数値変換（J=11, Q=12, K=13, A=14）
        $value = $this->toPoint($card['value']);

        // ★ 合計点を更新
        $state['total'] += $value;

        // $cardの中身
        // ['code'=>'7H','value'=>'7','suit'=>'HEARTS','image'=>'…']
        $state['history'][] = $card + ['point' => $value];

        // ★ 50 を超えたらゲーム終了フラグ
        if ($state['total'] > 50) {
            $state['game_over'] = true;
        }elseif($state['total'] >= 48 && $state['total'] <= 50){
            $state['win'] = true;
        }elseif($state['total'] > 35){
            $state['danger'] = true;
        }else{
            $state['danger'] = false;
        }
        // ★ 更新した状態をセッションへ保存
        $request->session()->put('over50', $state);

        return redirect()->route('over50.show');  // 画面を再読み込み
    }

    /* ─────────────────────────────
       リセットボタン用メソッド
       ───────────────────────────── */
    public function restart(Request $request)
    {
        $this->newGame($request);                 // ★ 新規ゲームを作成
        return redirect()->route('over50.show');
    }

    public function minus(Request $request)
    {
        $state = $request->session()->get('over50');
        if (!$state) {
            return redirect()->route('over50.show');
        }
        
        if($state['total'] <= 10){
            return redirect()->route('over50.show');
        }

        $state['total'] -= 10;

        if($state['total'] <= 35){
            $state['danger'] = false;
        }
    
    
        $request->session()->put('over50', $state);
        return redirect()->route('over50.show');
    }
    /* ─────────────────────────────
       新しいゲーム状態を作る共通処理
       ───────────────────────────── */
    private function newGame(Request $request): array
    {

        $verifySsl = app()->isProduction();   // 本番 → true, 開発 → false

        $deckId = Http::withOptions(['verify' => $verifySsl])
            ->get('https://deckofcardsapi.com/api/deck/new/shuffle/')['deck_id'];
        

        // ★ 最初の状態を配列で作成
        $state = [
            'deck_id'   => $deckId,   // デッキ ID
            'total'     => 0,         // 合計点
            'history'   => [],        // 引いたカードの履歴
            'game_over' => false,     // ゲームオーバーか？,
            'win' => false,
        ];

        // ★ セッションに保存
        $request->session()->put('over50', $state);

        return $state;
    }

    /* ─────────────────────────────
       カードの文字列を点数に変換
       ───────────────────────────── */
    private function toPoint(string $v): int
    {
        // match は switch の進化版：見た目がスッキリ
        return match ($v) {
            'ACE'   => 14,
            'KING'  => 13,
            'QUEEN' => 12,
            'JACK'  => 11,
            default => (int) $v,   // '2'〜'10' はそのまま数字に
        };
    }
}


