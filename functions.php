<?php

function enquiry_sample()
{
    header('Content-type: application/json; charset=UTF-8');
    $result = [];
    $result['namae'] = $_POST['namae'];
    $result['message'] = $_POST['message'];
    $result['stamp'] = $_POST['stamp'];
    echo json_encode($result);
    exit;
}
add_action('wp_ajax_enquiry_sample', 'enquiry_sample');
add_action('wp_ajax_nopriv_enquiry_sample', 'enquiry_sample');

add_action('admin_menu', 'bbs_approval_page');
function bbs_approval_page()
{
    add_menu_page('掲示板承認', '掲示板承認', 'manage_options', 'custom-setting', 'bbs_approval_file_path');
}

function bbs_approval_file_path()
{
    $return_url = '../wp-content/themes/sample_theme/bbs_approval_page.php';
    require $return_url;
}

class MAX_LENGTH
{
    public const NAME = 50;
    public const MESSAGE = 500;
}

function bbs_quest_input()
{
    session_start();
    $message = $_POST['message'];
    $namae = $_POST['namae'];
    $stamp = $_POST['stamp'];
    $namae = Chk_StrMode($namae);
    $message = Chk_StrMode($message);
    Chk_ngword($namae, '・NGワードが入力されています。', $error);
    Chk_ngword($message, '・NGワードが入力されています。', $error);
    Chk_InputMode($namae, '・お名前をご記入ください。', $error);
    Chk_InputMode($message, '・お問い合わせ内容をご記入ください。', $error);
    Chk_InputMode($stamp, '・スタンプを選択してください。', $error);
    CheckUrl($namae, '・お名前にＵＲＬは記入できません。'); // 追加
    CheckUrl($message, '・お問い合わせ内容にＵＲＬは記入できません。'); // 追加
    $result = [];
    if (empty($error)) {
        $result['error'] = '';
        $result['namae'] = $namae;
        $result['message'] = $message;
        $_SESSION['namae'] = $namae;
        $_SESSION['message'] = $message;
        $_SESSION['stamp'] = $stamp;
        $_SESSION['attach'] = $_FILES['attach'];
        foreach ($_FILES['attach']['tmp_name'] as $i => $tmp_name) {
            if (!empty($tmp_name)) {
                $_SESSION['attach']['data'][$i] = file_get_contents($tmp_name);
            }
        }
    } else {
        $result['error'] = $error;
        $_SESSION['namae'] = '';
        $_SESSION['message'] = '';
        $_SESSION['stamp'] = '';
        $_SESSION['attach'] = null;
    }
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($result);
    exit;
}
add_action('wp_ajax_bbs_quest_input', 'bbs_quest_input');
add_action('wp_ajax_nopriv_bbs_quest_input', 'bbs_quest_input');

function Chk_ngword($str, $mes, &$error)
{
    // NGワードリスト配列の定義
    $ng_words = ['死ね', 'アホ', '殺す', 'バカ'];
    foreach ($ng_words as $ngWordsVal) {
        // 対象文字列にキーワードが含まれるか
        if (false !== mb_strpos($str, $ngWordsVal)) {
            $error[] = $mes;
        }
    }
}
function Chk_StrMode($str)
{
    // タグを除去
    $str = strip_tags($str);
    // 連続する空白をひとつにする
    $str = preg_replace('/[\x20\xC2\xA0]++/u', "\x20", $str);
    // 連続する改行をひとつにする
    $str = preg_replace("/(\x20*[\r\n]\x20*)++/", "\n", $str);
    // 前後の空白を除去
    $str = mb_ereg_replace('^(　){0,}', '', $str);
    $str = mb_ereg_replace('(　){0,}$', '', $str);
    $str = trim($str);
    // 特殊文字を HTML エンティティに変換する
    $str = htmlspecialchars($str);

    return $str;
}
/* 未入力チェックファンクション */
function Chk_InputMode($str, $mes, &$error)
{
    if ('' == $str) {
        $error[] = $mes;
    }
}

/* 以下追加 */
function CheckUrl($checkurl, $mes)
{
    global $errors;
    if (preg_match("/[\.,:;]/u", $checkurl)) {
        $errors[] = $mes;
    }
}

function bbs_quest_confirm()
{
    // 新しいセッションを開始、あるいは既存のセッションを再開する
    session_start();
    // 何もせず終わる処理
    if (empty($_SESSION['message']) || empty($_SESSION['namae']) || empty($_SESSION['stamp'])) {
        exit;
    }
    // $wpdbでSQLを実行
    global $wpdb;
    // どのようなデータをどのテーブルに登録するか
    $sql = 'INSERT INTO sortable(message,namae,stamp,ip) VALUES(%s,%s,%d,%s)';
    // セッション変数に登録
    $message = $_SESSION['message'];
    $namae = $_SESSION['namae'];
    $stamp = $_SESSION['stamp'];
    // ipアドレスを取得する
    $ip = $_SERVER['REMOTE_ADDR'];
    $query = $wpdb->prepare($sql, $message, $namae, $stamp, $ip);
    // プリペアードステートメントを用意してから、下記のようにresultsで値を取得
    $query_result = $wpdb->query($query);
    // アップロードディレクトリ（パス名）を取得する
    $upload_dir = wp_upload_dir();
    // 『filenames』を記述して配列名を記述し、それに『[]』を代入すればそれは配列として扱われます
    $filenames = [];
    foreach ($_SESSION['attach']['tmp_name'] as $i => $tmp_name) {
        if (empty($tmp_name)) {
            $filenames[$i] = '';
        } else {
            $type = explode('/', $_SESSION['attach']['type'][$i]);
            $ext = $type[1];
            $filenames[$i] = "{$wpdb->insert_id}_{$i}.{$ext}";
            $attach_path = $upload_dir['basedir'].'/attach/'.$filenames[$i];
            // 文字列をファイルに書き込む、文字列データを書き込むファイル名を指定
            file_put_contents($attach_path, $_SESSION['attach']['data'][$i]);
        }
    }
    $result = [];
    // 条件式が成り立った場合処理を実行
    if (false === $query_result) {
        $result['error'] = '登録できませんでした';
    // 条件式が成り立たなければ処理を実行
    } else {
        $result['error'] = '';
    }

    // セッション変数の削除
    $_SESSION = [];
    session_destroy();

    // 結果画面へ行く信号が届いた時に、何もせず終わる
    if (empty($_SESSION['message']) || empty($_SESSION['namae']) || empty($_SESSION['stamp'])) {
        exit;
    }

    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($result);
    exit;
}
add_action('wp_ajax_bbs_quest_confirm', 'bbs_quest_confirm');
add_action('wp_ajax_nopriv_bbs_quest_confirm', 'bbs_quest_confirm');
