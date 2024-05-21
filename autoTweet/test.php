<?php

require 'vendor/autoload.php';
require 'config.php';
use Symfony\Component\HttpClient\HttpClient;
use Abraham\TwitterOAuth\TwitterOAuth;


// 結果をログファイルに書き込む
$logFile = '/var/www/html/tool/autoTweet/log/log.txt';
$logMessage = date('Y-m-d H:i:s') . ': ' . "start" . PHP_EOL;
file_put_contents($logFile, $logMessage, FILE_APPEND);

$client = HttpClient::create();

//1から2の数値をランダムで取得
$randomNumber = rand(1,2);
//メインプロンプト取得
$mainPronpt = file_get_contents("/var/www/html/tool/autoTweet/prompWordList/mainPrompt{$randomNumber}.csv");

//テーマプロンプト取得
$jsonData = file_get_contents("/var/www/html/tool/autoTweet/promptThemeList/promptList.json");
$themePrompts = json_decode($jsonData, true);
//ランダムに取得
$randomPrompt = $themePrompts[array_rand($themePrompts)];

//置換処理
$mainPronpt = str_replace('--Theme--', $randomPrompt, $mainPronpt);

//実施するプロンプトを出力
// var_dump($mainPronpt);

$response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
  'headers' => [
      'Authorization' => 'Bearer ' . $apiKey,
      'Content-Type' => 'application/json',
  ],
  'json' => [
      'model' => 'gpt-3.5-turbo',
      'messages' => [
          [
              'role' => 'system',
              'content' => 'You are a helpful assistant.'
          ],
          [
              'role' => 'user',
              'content' => $mainPronpt
          ]
      ],
      'max_tokens' => 500,
  ],
]);

//結果を格納
$result = $response->toArray();


//promptの文言を出力
$responseOpenAIWord = $result['choices'][0]['message']['content'];
// $responseOpenAIWord = "";
var_dump($responseOpenAIWord);


// 結果をログファイルに書き込む
$logFile = '/var/www/html/tool/autoTweet/log/log.txt';
$text = mb_convert_encoding(json_encode($result),"utf-8","sjis-win");
$logMessage = date('Y-m-d H:i:s') . ': ' . $text . PHP_EOL;
file_put_contents($logFile, $logMessage, FILE_APPEND);


// 文字数を取得
$wordCount = mb_strlen($responseOpenAIWord);

// 結果を出力
echo "文字数: " . $wordCount;

$hashTags = file_get_contents("/var/www/html/tool/autoTweet/hashTagList/hashTagList.json");
$hashTags = json_decode($hashTags, true);

$counter = 0;
while ($wordCount <= 144 && $counter < 20) {
    // hashTagListを読み込む

    $randomHashTag = "#" . $hashTags[array_rand($hashTags)];

    if (mb_strlen($responseOpenAIWord . " " . $randomHashTag) > 144) {
        break;
    } else {
        // ツイートにハッシュタグを追加
        $responseOpenAIWord .= " " . $randomHashTag;
    }

    // 文字数を再計算
    $wordCount = mb_strlen($responseOpenAIWord);
    $counter++;
}

// TwitterOAuthオブジェクトを作成
$connection = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

//v2使用を宣言
$connection->setApiVersion('2');

// ツイートを送信
$result = $connection->post('tweets', ['text' => $responseOpenAIWord], true);

var_dump($result);

// 結果を確認
if ($connection->getLastHttpCode() == 200) {
    echo "ツイート成功！";
}

// 結果をログファイルに書き込む
$logFile = '/var/www/html/tool/autoTweet/log/log.txt';
$logMessage = date('Y-m-d H:i:s') . ': ' . json_encode($result) . PHP_EOL;
file_put_contents($logFile, $logMessage, FILE_APPEND);

