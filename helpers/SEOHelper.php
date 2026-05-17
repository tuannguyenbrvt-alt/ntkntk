<?php
// helpers/SEOHelper.php
class SEOHelper {
    public static function generateMetaTags($title, $description = '', $image = '', $url = '') {
        $siteName = APP_NAME;
        $description = $description ?: "Hệ thống học tập trực tuyến, cung cấp các khóa học chất lượng cao tại $siteName.";
        $url = $url ?: "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $image = $image ? (strpos($image, 'http') === 0 ? $image : APP_URL . '/' . ltrim($image, '/')) : APP_URL . '/assets/images/default-share.jpg';

        $titleEsc = htmlspecialchars($title);
        $descEsc = htmlspecialchars(strip_tags($description));
        
        $html = "
    <!-- SEO Meta Tags -->
    <title>{$titleEsc} - {$siteName}</title>
    <meta name=\"description\" content=\"{$descEsc}\">
    <link rel=\"canonical\" href=\"{$url}\">
    
    <!-- Open Graph / Facebook -->
    <meta property=\"og:type\" content=\"website\">
    <meta property=\"og:url\" content=\"{$url}\">
    <meta property=\"og:title\" content=\"{$titleEsc}\">
    <meta property=\"og:description\" content=\"{$descEsc}\">
    <meta property=\"og:image\" content=\"{$image}\">
    <meta property=\"og:site_name\" content=\"{$siteName}\">

    <!-- Twitter -->
    <meta name=\"twitter:card\" content=\"summary_large_image\">
    <meta name=\"twitter:url\" content=\"{$url}\">
    <meta name=\"twitter:title\" content=\"{$titleEsc}\">
    <meta name=\"twitter:description\" content=\"{$descEsc}\">
    <meta name=\"twitter:image\" content=\"{$image}\">
";
        return $html;
    }
}
