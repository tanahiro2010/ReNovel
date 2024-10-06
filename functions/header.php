<?php
/**
 * @param mixed $user
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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<!-- ヘッダー -->
<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-6 flex justify-between items-center">
        <!-- サイトタイトル -->
        <a href="/" class="text-3xl font-bold text-blue-600 hover:text-blue-500">
            ReNovel
        </a>
        <div class="text-gray-600">
            さぁ、貴方も内なる中二病を解放しましょう
        </div>

        <!-- アカウントと検索リンク -->
        <div class="flex space-x-4">
            <a href="/search" class="text-blue-600 hover:text-blue-500">
                検索
            </a>
            <?php if ($user): // ログインしている場合 ?>
                <a href="/dashboard" class="text-blue-600 hover:text-blue-500">
                    <?php echo $user['name']; ?>
                </a>
            <?php else: ?>
                <a href="/login" class="text-blue-600 hover:text-blue-500">
                    ログイン
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php
}
?>
