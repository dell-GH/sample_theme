<?php
/*
Template Name: 投稿ページ
Template Post Type: post
*/
?>
<?php

if (is_single() && !is_user_logged_in() && !isBot()) { // 個別記事 かつ ログインしていない かつ 非ボット
    set_post_views_week(); // 週間アクセスをカウントする
    set_post_views_month(); // 月間アクセスをカウントする
}

//ここから追加
$rss_table_name = get_rss_table_name(4);//テーブル接続
var_dump($rss_table_name);

$block_per_page = 3; /* ページ当たりブロック数 */
$limitSect1 = 5; /* ひとつ目のRSS件数 */
$limitSect2 = 4; /* ふたつ目のRSS件数 */
$limitSect3 = 4; /* みっつ目のRSS件数 */
$rss_per_block = $limitSect1 + $limitSect2 + $limitSect3; /* ブロックあたりRSS件数 */
$rss_per_page = $block_per_page * $rss_per_block; /* ページ当たりRSS件数 */
$rss_offset = 0;
$sql = "SELECT * FROM {$rss_table_name} ORDER BY date DESC LIMIT %d,%d";
$query = $wpdb->prepare($sql, $rss_offset, $rss_per_page);
$rss_items = $wpdb->get_results($query);

$trisect_rss_feed = array();
for ($i = 0; $i < $block_per_page; ++$i) {
    $contentA = '';
    $contentB = '';
    $contentC = '';
    for ($j = 0; $j < $rss_per_block; ++$j) {
        $item_index = $i * $rss_per_block + $j;
        if ($item_index >= count($rss_items)) {
            break;
        }
        $item = $rss_items[$item_index];
        $title = "<strong><a href=\"{$item->link}\">{$item->title}</a></strong>";
        if (empty($item->img)) {
            $img = 'http://www.gsdfgsdgs.cfbx.jp/wp-content/uploads/2022/07/1-19.jpg';
        } else {
            $img = $item->img;
        }
        $image = "<a href=\"{$item->link}\"><img src=\"{$img}\" width=\"100\"></a>";
        $subject = "<a href=\"{$item->link}\">{$item->subject}</a>";
        if ($j < $limitSect1) {
            $contentA .= "<li class=\"sitelink\">{$title}</li>"; // タイトルのみ
        } elseif ($j < $limitSect1 + $limitSect2) {
            $contentB .= "<li class=\"sitelink2\"><figure class=\"snip\"><figcaption>{$image}<br>{$title}<p class=\"btn\">{$subject}</p></figcaption></figure></li>"; // 画像と画像の下にタイトル
        } else {
            $contentC .= "<li class=\"sitelink3\">{$image}{$title}</li>"; // 画像と画像の右にタイトル
        }
    }
    $content = '<div class="rssBlock">';
    $content .= "<ul class=\"wiget-rss\">{$contentA}</ul>";
    $content .= "<ul class=\"wiget-rss\">{$contentB}</ul>";
    $content .= "<ul class=\"wiget-rss\">{$contentC}</ul>";
    $content .= '</div>';
    $trisect_rss_feed[] = $content;
}

/* RSS1 */
echo $trisect_rss_feed[0];

/* 広告 */
echo '<span class="banner-1"><a href="//af.moshimo.com/af/c/click?a_id=3493027&p_id=2312&pc_id=4967&pl_id=38392&guid=ON" rel="nofollow" referrerpolicy="no-referrer-when-downgrade"><img src="//image.moshimo.com/af-img/1762/000000038392.png" width="200" height="200" style="border:none;"></a><img src="//i.moshimo.com/af/i/impression?a_id=3493027&p_id=2312&pc_id=4967&pl_id=38392" width="1" height="1" style="border:none;"></span>';

/* 記事コンテンツ */
/* タイトルない場合は非表示 */
echo the_title( '<h2 class="postpage-title">', '</h2>',true);

/* 日付け */
$date = get_the_time('Y/m/d');
echo "<span class=\"article-date\">{$date}</span>";

/* カテゴリーない場合は非表示 */
echo '<span class="category">';
the_category(' ');
echo '<span class="fa-folder"></span>';
echo '</span>';

/* コメント数　ない場合は0表示 */
echo '<span class="single-comments"><a href="#comments">';
echo '<span class="fa fa-comments-o"></span>';
echo'</span>';
echo comments_number( '0', '1', '%' );
echo '</a>';
echo '</span>';

/* 記事前半 */
echo '<span class="first-content">';
echo get_extended( $post->post_content )['main'];
echo '</span>';

/* RSS2 */
echo $trisect_rss_feed[1];

/* 広告 */
echo '<span class="banner-2"></span>';

/* 記事後半 */
echo '<span class="secound-content">';
echo get_extended( $post->post_content )['extended'];
echo '</span>';

/* RSS3 */
echo $trisect_rss_feed[2];


/* カスタムフィールドの取得 */
$team = get_post_meta($post->ID, 'team', true);
/* 投稿オブジェクトの取得 */
if ('red' === $team) {
    $post_red = $post; /* 赤（現在） */
    $post_blue = get_adjacent_post(true, '', false); /* 青（現在の次） */
    $post = $post_blue; /* 現在を青に置きかえる */
    $post_green = get_adjacent_post(true, '', false); /* 緑（現在の次：青の次） */
    $post = $post_red; /* 現在を赤に戻す */
} elseif ('blue' === $team) {
    $post_blue = $post; /* 青（現在） */
    $post_red = get_adjacent_post(true, '', true); /* 赤（現在の前） */
    $post_green = get_adjacent_post(true, '', false); /* 緑（現在の次） */
} elseif ('green' === $team) {
    $post_green = $post; /* 緑（現在） */
    $post_blue = get_adjacent_post(true, '', true); /* 青（現在の前） */
    $post = $post_blue; /* 現在を青に置きかえる */
    $post_red = get_adjacent_post(true, '', true); /* 赤（現在の前：青の前） */
    $post = $post_green; /* 現在を緑に戻す */
}
/* コメントオブジェクトの取得 */
$args = [
'author__not_in' => '1', /* 管理者を除く */
'status' => 'approve', /* 承認済み */
'type' => 'comment', /* コメント */
'orderby' => '',/* 順番 */
];
$args['post_id'] = $post_red->ID; /* 赤のID */
$comments_red = get_comments($args); /* 赤のコメント */
$args['post_id'] = $post_blue->ID; /* 青のID */
$comments_blue = get_comments($args); /* 青のコメント */
$args['post_id'] = $post_green->ID; /* 緑のID */
$comments_green = get_comments($args); /* 緑のコメント */
/* コメントの表示 */
echo "<p>{$post_red->post_title}（{$post_red->post_date})</p>";
if (empty($comments_red)) {
    echo '<p>コメントなし</p>';
} else {
    echo '<ol>';
    foreach ($comments_red as $comment) {
        if (empty($comment->comment_author)) {
            $comment_author = '匿名';
        } else {
            $comment_author = $comment->comment_author;
        }
        echo '<li>';
        echo "<article id=\"div-comment-{$comment->comment_ID}\">";
		echo "<p>{$comment_author}</p>";
        echo "<p>{$comment->comment_content}</p>";
        echo "<a class=\"comment-reply-link\" href=\"\" data-commentid=\"{$comment->comment_ID}\" data-postid=\"{$comment->comment_post_ID}\" data-belowelement=\"div-comment-{$comment->comment_ID}\" data-respondelement=\"respond\">返信</a>";
        echo '</article>';
        echo '</li>';
    }
    echo '</ol>';
}
echo "<p>{$post_blue->post_title}（{$post_blue->post_date})</p>";
if (empty($comments_blue)) {
    echo '<p>コメントなし</p>';
} else {
    echo '<ol>';
    foreach ($comments_blue as $comment) {
        if (empty($comment->comment_author)) {
            $comment_author = '匿名';
        } else {
            $comment_author = $comment->comment_author;
        }
        echo '<li>';
        echo "<article id=\"div-comment-{$comment->comment_ID}\">";
		echo "<p>{$comment_author}</p>";
        echo "<p>{$comment->comment_content}</p>";
        echo "<a class=\"comment-reply-link\" href=\"\" data-commentid=\"{$comment->comment_ID}\" data-postid=\"{$comment->comment_post_ID}\" data-belowelement=\"div-comment-{$comment->comment_ID}\" data-respondelement=\"respond\">返信</a>";
        echo '</article>';
        echo '</li>';
    }
    echo '</ol>';
}
echo "<p>{$post_green->post_title}（{$post_green->post_date})</p>";
if (empty($comments_green)) {
    echo '<p>コメントなし</p>';
} else {
    echo '<ol>';
    foreach ($comments_green as $comment) {
        if (empty($comment->comment_author)) {
            $comment_author = '匿名';
        } else {
            $comment_author = $comment->comment_author;
        }
        echo '<li>';
        echo "<article id=\"div-comment-{$comment->comment_ID}\">";
		echo "<p>{$comment_author}</p>";
        echo "<p>{$comment->comment_content}</p>";
        echo "<a class=\"comment-reply-link\" href=\"\" data-commentid=\"{$comment->comment_ID}\" data-postid=\"{$comment->comment_post_ID}\" data-belowelement=\"div-comment-{$comment->comment_ID}\" data-respondelement=\"respond\">返信</a>";
        echo '</article>';
        echo '</li>';
    }
    echo '</ol>';
}
comment_form();
?>