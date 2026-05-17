<?php
class YouTubeHelper {
    public static function getEmbedUrl($url) {
        $videoId = self::getVideoId($url);
        if ($videoId) {
            return "https://www.youtube.com/embed/" . $videoId;
        }
        return '';
    }

    public static function getVideoId($url) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $url, $match);
        return isset($match[1]) ? $match[1] : null;
    }
}
