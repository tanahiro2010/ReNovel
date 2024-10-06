<?php
/**
 * @param mixed $user
 * @param string $mode
 * @return void
 */
function echo_header($user)
{
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>ReNovel</title>
        <meta charset="UTF-8">

        <!-- metaタグゾーン -->
        <meta name="description" content="tanahiro2010が小説を読みたいがために作成した小説家の卵を発掘するための小説投稿サイト 名前はRelease Novelをもじった">
        <meta name="copyright" content="Copyright &copy; 2024 tanahiro2010. All rights reserved." />
        <meta property="og:title" content="ReNovel" />
        <meta property="og:site_name" content="syosetu.tanahiro2010.com">
        <meta property="og:locale" content="ja_JP" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="ReNovel" />
        <meta name="twitter:description" content="tanahiro2010が小説を読みたいがために作成した小説家の卵を発掘するための小説投稿サイト 名前はRelease Novelをもじった">
        <meta name="twitter:image:src" content />
        <meta name="twitter:site" content="syosetu.tanahiro2010.com" />
        <meta name="twitter:creator" content="@tanahiro2010" />
        <meta name="keywords" content="tanahiro2010,ReNovel,renovel,release_novel,novel,syosetu,小説,小説投稿サイト">

        <!-- CSSを以下に -->
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <meta name="viewport" content="width=device-width">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body>
        <header>
            <div class="header">
                <a href="/">
                    <h1 class="site-title default-text">ReNovel</h1>
                </a>
                <div class="site-description">
                    さぁ、貴方も内なる中二病を解放しましょう
                </div>
            </div>

            <div class="account">
                <a href="/search">
                    検索
                </a>
                <?php if ($user): // ログインしているなら
                    $user_name = $user['name'];
                    ?>
                <a href="/dashboard">
                    <?php
                    echo $user_name;
                    ?>
                </a>
                <?php else: ?>
                <a href="/login">
                    ログイン
                </a>
                <?php endif; ?>
            </div>
        </header>
    <?php
}
?>