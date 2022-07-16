<?php get_header(); ?>
<!--ここから自作-->
<div id="blog-box" class="clearfix">
  <!-- ブログ開始 -->
  <div id="main-box">
    <!-- メインボックス開始 -->
    <!--メイン左ボックス-->
    <div id="left-box">
      <!--週間カテゴリーランキング-->
      <?php
$sql = "
SELECT
    t.*,
    tt.*,
    cc.meta_value AS access_count
FROM
    wp_terms AS t
    INNER JOIN wp_term_taxonomy AS tt
        ON t.term_id = tt.term_id
    INNER JOIN (
        SELECT
            *
        FROM
            wp_termmeta
        WHERE
            meta_key = 'category_group'
            AND meta_value = 'single_rss_feed1'
    ) AS cg
        ON t.term_id = cg.term_id
    INNER JOIN (
        SELECT
            *
        FROM
            wp_termmeta
        WHERE
            meta_key = 'category_count_week'
            AND meta_value != 0
    ) AS cc
        ON t.term_id = cc.term_id
ORDER BY
    cc.meta_value DESC
LIMIT
    20
";

$query = $wpdb->prepare($sql);
$terms = $wpdb->get_results($query);?>
        <section class="category-box">
          <?php
if ($terms) : ?>
            <ul class="category-ranking clearfix">
              <?php foreach($terms as $term): ?>
              <li>
                <a href="<?php echo get_term_link($term); ?>" width: 97px;height: 130px;>
                  <div class="cat-genre-wrap">
                    <?php echo $term->name; // 名前 ?> </div>
                </a>
                <div class="Information">
                  <p>
                    <?php //　連番表示 $count = sprintf("%02d",$count); // 一桁を二桁に echo $count + 1; // 01を出力 $count++; ?> </p>
                </div>
                <?/*php echo getPostViewsMonth(get_the_ID()); // 記事閲覧回数表示 */?>
                  <?php endforeach; wp_reset_postdata(); ?> </li>
            </ul>
            <?php else : ?>
            <p>アクセスランキングはまだ集計されていません。</p>
            <?php endif; ?> </section>
        <!--30日間ランキング-->
        <div class="30day-ranking">
          <div class="side-title">30days ranking</div>
          <div class="AMvertical black" style="width: 300px;">
            <?php
if (is_category() && !is_user_logged_in() && !isBot()) : //個別記事 かつ ログインしていない かつ 非ボット
    category_views_week(); //アクセスをカウントする
endif;
?>
              <section class="popular-box">
                <?php
    $args = array(
        'post_type'     => 'post',
        'numberposts'   => 12,       //表示数
        'meta_key'      => 'pv_count',
        'orderby'       => 'meta_value_num',
        'order'         => 'DESC',
    );
    $posts = get_posts($args);
    if ($posts) : ?>
                  <ul class="ranking-box">
                    <?php foreach ($posts as $post) : setup_postdata($post); ?>
                    <li>
                      <a href="<?php the_permalink(); ?>" width: 97px;height: 130px;>
                        <div class="dairy-ranking">
                          <?php the_post_thumbnail('thumbnail'); ?> </div>
                        <h5>
                          <?php the_title(); ?> </h5>
                      </a>
                    </li>
                    <?php endforeach;
            wp_reset_postdata(); ?> </ul>
                  <?php else : ?>
                  <p>アクセスランキングはまだ集計されていません。</p>
                  <?php endif; ?> </section>
          </div>
        </div>
        <!--最近のコメント-->
        <?php
$args = array(
'author__not_in' => '1',
'number' => '5',
'status' => 'approve',
'type' => 'comment'
);
$comments_query = new WP_Comment_Query;
$comments = $comments_query->query( $args );
// Comment Loop
if ( $comments ) {
?>
          <!-- 表示部分 -->
          <div class="commentlist">
            <div class="side-title">最近のコメント(comments)</div>
            <?php
foreach ( $comments as $comment ) {
// 記述が長いので $pid に入れておく
$pid = $comment->comment_post_ID;
// 必要な文字列データの取得
$url = get_permalink($pid);
$img = get_the_post_thumbnail($pid , array('class' => 'myClass'));
$date = get_comment_date('(Y/n/d)', $comment->comment_ID);
$title = get_the_title($pid);
$text = get_comment_text($comment->comment_ID);
$user_id = $comment->comment_author;
// デフォルト値で初期化して
$user_id = '名無しさん(anonymous)';

if (!empty($comment->comment_author)) {
$user_id = $comment->comment_author;
} elseif (!empty($comment->user_id)) {
$user_id = $comment->user_id;
}
?>
              <ul class="mycomment">
                <li class="imgcomment">
                  <a class="commentheight" href="<?= $url ?>">
                    <?= $img?>
                  </a>
                  <a class="com_title" href="<?= $url ?>">
                    <?= $title ?>
                  </a>
                  <div class="commentnumber">
                    <p class="comment">
                      <?= mb_strimwidth($text, 0, 38, "･･･") ?>
                    </p>
                    <p class="my_author">
                      <?= $date ?>
                    </p><br> </div>
                </li>
              </ul>
              <?php
}
?> </div>
    </div>
    <?php
} else {
echo 'コメントなし';
}
?>
      <!-- ▼　RSS記事右 ▼ -->
      <div id="right-box">
        <!-- ▼　RSS記事上 ▼ -->
        <!-- ▼　検索欄 ▼ -->
        <?php get_search_form(); ?>
        <!--最近検索されたワード-->
        <?php
echo __FILE__;
echo __DIR__;
$categories = [];
foreach (get_categories() as $category) {
    $category->category_link = get_category_link($category->cat_ID);
    $categories[$category->cat_ID] = $category;
}
function set_other_data($post)
{
    // アイキャッチIDを取得
    $post_thumbnail_id = get_post_thumbnail_id($post);
    // アイキャッチ画像の確認
    if ($post_thumbnail_id) {
        // 存在する
        $image_src = wp_get_attachment_image_src($post_thumbnail_id);
        // サムネイルの画像URLを設定
        $post->thumbnail = $image_src[0];
    } else {
        // 存在しない
        $post->thumbnail = 'noimage.jpg';
    }
    // カテゴリーIDを取得
    $post->categories = wp_get_post_categories($post->ID);
    // コメントテキスト
    if (0 == $post->comment_count) {
        // コメントなし
        $post->comments = __('No Comments');
    } else {
        // コメントあり
        $post->comments = $post->comment_count.'件のコメント';
    }
    // コメントリンク
    $post->comments_link = get_comments_link($post->ID);
}

$rss_table_name = get_rss_table_name(1);

//表示設定
//ページ番号チェック
if (ctype_digit($_REQUEST['page'])) {
    $current_page = (int) $_REQUEST['page'];
} else {
    $current_page = 1;
}
if ($current_page > $wp_query->max_num_pages) {
    $current_page = $wp_query->max_num_pages;
}
if (empty($current_page)) {
    $current_page = 1;
}
$block_per_page = 2; //ページあたりブロック件数
$limitSect1 = 5; // タイトルのみの件数
$limitSect2 = 4; // 画像と画像の下にタイトルの件数
$limitSect3 = 4; // 画像と画像の右にタイトルの件数
$rss_per_block = $limitSect1 + $limitSect2 + $limitSect3; // ブロックあたりRSS件数

//RSS読み込み
$rss_per_page = $block_per_page * $rss_per_block;
$rss_offset = ($current_page - 1) * $rss_per_page;

//※テーブル名の変更
$sql = "SELECT * FROM {$rss_table_name} ORDER BY date DESC LIMIT %d,%d";
$query = $wpdb->prepare($sql, $rss_offset, $rss_per_page);
//SQL分実行と結果取得
$rss_items = $wpdb->get_results($query);
$group_per_block = 5; //ブロックあたり投稿グループ件数

//投稿読み込み
$posts_per_group = 1; // 投稿グループあたり投稿件数
$posts_per_page = $block_per_page * $group_per_block * $posts_per_group; // ページあたり投稿件数
$posts_offset = ($current_page - 1) * $posts_per_page; //投稿オフセット

if (is_category() && !is_user_logged_in() && !isBot()) : //個別記事 かつ ログインしていない かつ 非ボット
    category_views_week(); //アクセスをカウントする
endif;
/* ここから追加 */
$sql = "
SELECT
post.*
FROM
wp_posts AS post
INNER JOIN wp_term_relationships
ON post.id = wp_term_relationships.object_id
INNER JOIN wp_term_taxonomy
ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
WHERE wp_term_taxonomy.term_id = %d
AND post.post_type = 'post'
AND post.post_status = 'publish'
ORDER BY
post.post_date DESC
LIMIT %d,%d
";
$query = $wpdb->prepare($sql, $cat, $posts_offset, $posts_per_page);
$post_items = $wpdb->get_results($query);

/* ここまで追加 */
//表示
for ($i = 0; $i < $block_per_page; ++$i) {
    echo '<h3>RSS</h3>';
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
            $img = 'http://www.gdsgdsgsd.cfbx.jp/wp-content/uploads/2022/07/1-19.jpg';
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
    echo '<div class="rssBlock">';
    echo "<ul class=\"wiget-rss\">{$contentA}</ul>";
    echo "<ul class=\"wiget-rss\">{$contentB}</ul>";
    echo "<ul class=\"wiget-rss\">{$contentC}</ul>";
    echo '</div>';

    echo '<h3>投稿</h3>';
    echo '<div id="entry-content">'; // 記事全体のid
    for ($k = 0; $k < $group_per_block; ++$k) {
        // ここから画像とタイトルの処理
        for ($j = 0; $j < $posts_per_group; ++$j) {
            $item_index = $i * $group_per_block * $posts_per_group + $k * $posts_per_group + $j;
            if ($item_index >= count($post_items)) {
                break;
            }
            $item = $post_items[$item_index];
            set_other_data($item);
            // タイトルの保存は省略
            // ここから追加
            echo '<div class="entry-post">'; // 記事1つ1つ
            echo "<figure class=\"entry-thumnail\"><a href=\"{$item->guid}\"><img src=\"{$item->thumbnail}\"></a></figure>"; // サムネイル画像
            echo '<header class="entry-header">';
            echo "<h2 class=\"entry-title\"><a href=\"{$item->guid}\">{$item->post_title}</a></h2>"; // タイトル
            echo '<p class="post-meta">'; // 日付け、カテゴリー、コメント数
            echo '<span class="fa-clock"></span>'; // 日付けのマーク fontawesomeをbeforeで読み込む
            echo "<span class=\"published\">{$item->post_date}</span>"; // 日付け
            echo '<span class="fa-folder"></span>'; // カテゴリーのマーク fontawesomeをbeforeで読み込む
            echo '<span class="category-link">';
            if ($item->categories) {
                foreach ($item->categories as $cat_ID) {
                    $category = $categories[$cat_ID];
                    echo "<a href=\"{$category->category_link}\">{$category->cat_name}</a>";
                }
            }
            echo '</span>'; // カテゴリー
            echo '<span class="fa-comment"></span>'; // コメント数のマーク fontawesomeをbeforeで読み込む
            echo "<span class=\"comment-count\"><a href=\"{$item->guid}\">{$item->comments}</a></span>"; // コメント数
            echo '</p>';
            echo '</header>';
            echo "<p class=\"entry-snippet\">{$item->post_excerpt}</p>"; // 抜粋
		    echo '</div>';//記事1つ1つ
        }
    }
echo '</div>';//記事全体のid
}

//ページリンク
$display_pages = 5; //番号を表示したいページ数
$display_page_count = 0;
/* ここから削除
$pages = ceil($wp_query->found_posts / $posts_per_page);
ここまで削除 */
/* ここから追加 */
/* 検索全件件数取得 */
$sql = "
SELECT
COUNT(*) AS count
FROM
wp_posts AS post
INNER JOIN wp_term_relationships
ON post.ID = wp_term_relationships.object_id
INNER JOIN wp_term_taxonomy
ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
WHERE wp_term_taxonomy.term_id = %d
";
$query = $wpdb->prepare($sql, $cat);
$results = $wpdb->get_results($query);
$pages = ceil($results[0]->count / $posts_per_page);

$pages = ceil($results[0]->count / $posts_per_page);
/* ここまで追加 */
for ($i = 1; $i <= $pages; ++$i) {
    if (1 == $i) {
        $page_text = '<<';
        echo "<a href=\"?page={$i}\">{$page_text}</a> ";
        if ($current_page > 1) {
            $j = $current_page - 1;
        } else {
            $j = 1;
        }
        $page_text = '<';
        echo "<a href=\"?page={$j}\">{$page_text}</a> ";
    }
    if ($i >= $current_page && ++$display_page_count <= $display_pages) {
        $page_text = $i;
        echo "<a href=\"?page={$i}\">{$page_text}</a> ";
    }
    if ($i == $pages) {
        if ($current_page < $pages) {
            $j = $current_page + 1;
        } else {
            $j = $pages;
        }
        $page_text = '>';
        echo "<a href=\"?page={$j}\">{$page_text}</a> ";
        $page_text = '>>';
        echo "<a href=\"?page={$i}\">{$page_text}</a> ";
    }
}
  ?>
      </div>
  </div>
</div>
<!--ここまで-->