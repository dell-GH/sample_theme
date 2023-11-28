<?php
/*
Template Name: bbs
固定ページ: 質問掲示板
*/
get_header();
get_header('menu');
?>
<h1>質問一覧</h1>
<?php
$sql = 'SELECT * FROM sortable ORDER BY TS DESC';
$query = $wpdb->prepare($sql);
$rows = $wpdb->get_results($query);
//現在のアップロードディレクトリ（パス名）を取得
$upload_dir = wp_upload_dir();
foreach ($rows as $i => $row) {
    $url = home_url('質問みる?'.$row->unique_id);
    echo '<div>';
    echo '<a href="'.$url.'">';
    echo '<div>名前：'.$row->namae.'</div>';
    echo '<div>コメント：'.$row->message.'</div>';
    echo '<div><input type="radio" value="'.$row->stamp.'" id="stamp_'.$i.'"><label for="stamp_'.$i.'"></label></div>';
    $pattern = $upload_dir['basedir'].'/attach/'.$row->ID.'_*.*';
    // pattern にマッチする全てのパス名を検索
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
        echo '<div>'.$view.'</div>';
    }
    echo '</a>';
    echo '<div>'.$row->TS.'</div>';
    echo '</div>';
}
?>