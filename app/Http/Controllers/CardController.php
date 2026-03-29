<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class CardController extends Controller
{
    public function play()
    {
        /* ---------------------------------------------
         * 0. ここで “本番かどうか” を判定
         *    app()->isProduction() は   .env APP_ENV=production
         *    のとき true になります
         * --------------------------------------------- */
        $verifySsl = app()->isProduction();   // 本番 → true, 開発 → false

        /* 1. デッキをシャッフルして deck_id を取得
         *    withOptions(['verify'=>$verifySsl]) で
         *    SSL 検証の ON / OFF を切り替える
         */
        $shuffle = Http::withOptions(['verify' => $verifySsl])
                       ->get('https://deckofcardsapi.com/api/deck/new/shuffle/');
        $deckId = $shuffle['deck_id'];

        /* 2. そのデッキから 2 枚ドロー */
        $draw   = Http::withOptions(['verify' => $verifySsl])
                      ->get("https://deckofcardsapi.com/api/deck/{$deckId}/draw/",
                            ['count' => 4]);
                            
        $cards  = $draw['cards'];

        //dd($cards);

        $player1 = $cards[0];
        $player2 = $cards[1];
        $cpu1   = $cards[2];
        $cpu2   = $cards[3];

        /* 3. カードの値を数値化して勝敗判定
              2–10 → そのまま
              J=11, Q=12, K=13, A=14 */
        $toNum = fn (string $v) => match ($v) {
            'ACE'   => 1,
            'KING'  => 0,
            'QUEEN' => 0,
            'JACK'  => 0,
            default => (int) $v,   // 2–10 は数値に変換
        };

        $pVal1 = $toNum($player1['value']);
        $pVal2 = $toNum($player2['value']);
        $cVal1 = $toNum($cpu1['value']);
        $cVal2 = $toNum($cpu2['value']);

        $pVal3 = ($pVal1+$pVal2) % 10;
        $cVal3 = ($cVal1+$cVal2) % 10;     

        // ❹ 勝敗判定（if文でわかりやすく）
        if ($pVal3 === $cVal3){
            $winner = 'DRAW';          // 同点
        } elseif ($pVal3 > $cVal3){
            $winner = 'YOU WIN!';      // プレイヤーのほうが大きい
        } else{
            $winner = 'CPU WINS';      // CPU のほうが大きい
        }

        /* 4. ビューへデータを渡す */
        return view('battle', [
            'player1'  => $player1,
            'player2'  => $player2,
            'cpu1'     => $cpu1,
            'cpu2'     => $cpu2,
            'winner'  => $winner,
            'pVal3'    => $pVal3,
            'cVal3'    => $cVal3,
        ]);
    }
}