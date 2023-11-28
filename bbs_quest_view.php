<?php
/*
Template Name: bbs_quest_view
固定ページ: 質問みる
*/
get_header();
get_header('menu');
?>
<h1>質問みる</h1>
<?php
$url = substr($_SERVER['REQUEST_URI'], -36);
$sql = 'SELECT * FROM sortable WHERE unique_id=%s';
$query = $wpdb->prepare($sql, $url);
$rows = $wpdb->get_results($query);
if (empty($rows)) {
    echo '質問がみつかりません';
    exit;
}
$row = $rows[0];
$upload_dir = wp_upload_dir();
$pattern = $upload_dir['basedir'].'/attach/'.$row->ID.'_*.*';
$files = glob($pattern);
if (!empty($files)) {
    $view = '';
    foreach ($files as $file) {
        $info = pathinfo($file);
        $attach_url = $upload_dir['baseurl'].'/attach/'.$info['basename'];
        $ext = $info['extension'];
        switch ($ext) {
            case 'jpeg':
            case 'png':
                $view .= '<img style="max-height: 200px;max-width: 200px;" src="'.$attach_url.'">';
                break;
            case 'mp4':
                $view .= '<video style="max-height: 200px;max-width: 200px;" controls src="'.$attach_url.'#t=0.1"></video>';
                break;
            case 'pdf':
                $view .= '<iframe style="max-height: 200px;max-width: 200px;" src="'.$attach_url.'"></iframe>';
                break;
            default:
                break;
        }
    }
}
?>
<div>
    <h2>質問</h2>
    <div>名前：<?php echo $row->namae; ?></div>
    <div>コメント：<?php echo $row->message; ?></div>
    <div><input type="radio" value="<?php echo $row->stamp; ?>" id="stamp" disabled><label for="stamp"></label></div>
    <div><?php echo $view; ?></div>
</div>
<div>
    <button type="button" id="answer_button">回答する</button>
</div>
<div id="answer_area" style="display: none;">
    <div id="input_area">
        <h2>入力画面</h2>
        <form method="post" enctype="multipart/form-data" name="input_form">
            <div>名前<input type="text" name="namae"></div>
            <div>コメント<textarea name="message"></textarea></div>
            <div>
                <input type="radio" name="stamp" value="1" id="stamp_1"><label for="stamp_1"></label>
                <input type="radio" name="stamp" value="2" id="stamp_2"><label for="stamp_2"></label>
                <input type="radio" name="stamp" value="3" id="stamp_3"><label for="stamp_3"></label>
                <input type="radio" name="stamp" value="4" id="stamp_4"><label for="stamp_4"></label>
                <input type="radio" name="stamp" value="5" id="stamp_5"><label for="stamp_5"></label>
                <input type="radio" name="stamp" value="6" id="stamp_6"><label for="stamp_6"></label>
                <input type="radio" name="stamp" value="7" id="stamp_7"><label for="stamp_7"></label>
                <input type="radio" name="stamp" value="8" id="stamp_8"><label for="stamp_8"></label>
            </div>
            <div>
                <button type="button" id="cancel_button">キャンセル</button>
                <button type="button" id="input_button">確認画面へ進む</button>
            </div>
        </form>
    </div>
    <div id="confirm_area"></div>
    <div id="result_area"></div>
</div>
<h2>回答一覧</h2>
<div>
    <div>
        回答コメント：～～～～
        回答名前：～～～～
    </div>
    <div>
        回答コメント：～～～～
        回答名前：～～～～
    </div>
    <div>
        回答コメント：～～～～
        回答名前：～～～～
    </div>
</div>

<script>
    const answer_area = document.getElementById("answer_area");

    function answer_button_click() {
        answer_button.style.display = "none";
        answer_area.style.display = "block";
    }

    function cancel_button_click() {
        answer_area.style.display = "none";
        answer_button.style.display = "block";
    }

    function input_button_click() {
        alert("サンプルはここまで");
    }

    function init() {
        document.getElementById("answer_button").addEventListener("click", answer_button_click);
        document.getElementById("cancel_button").addEventListener("click", cancel_button_click);
        document.getElementById("input_button").addEventListener("click", input_button_click);
    }
    window.addEventListener("DOMContentLoaded", init);
</script>