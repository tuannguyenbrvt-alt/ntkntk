<?php
class SitemapController extends Controller {
    public function index() {
        $db = Database::getInstance()->getConnection();
        
        $urls = [];
        // Add home
        $urls[] = ['loc' => APP_URL . '/', 'priority' => '1.0'];
        $urls[] = ['loc' => APP_URL . '/courses', 'priority' => '0.9'];
        $urls[] = ['loc' => APP_URL . '/blog', 'priority' => '0.8'];

        // Add Pages
        $stmtPages = $db->query("SELECT slug, updated_at FROM posts WHERE type = 'page' AND status = 'published'");
        while ($row = $stmtPages->fetch()) {
            $urls[] = ['loc' => APP_URL . '/page/' . $row['slug'], 'lastmod' => date('Y-m-d', strtotime($row['updated_at'])), 'priority' => '0.7'];
        }

        // Add Posts
        $stmtPosts = $db->query("SELECT slug, updated_at FROM posts WHERE type = 'blog' AND status = 'published'");
        while ($row = $stmtPosts->fetch()) {
            $urls[] = ['loc' => APP_URL . '/post?slug=' . $row['slug'], 'lastmod' => date('Y-m-d', strtotime($row['updated_at'])), 'priority' => '0.8'];
        }

        // Add Courses
        $stmtCourses = $db->query("SELECT slug, updated_at FROM courses WHERE status = 'published'");
        while ($row = $stmtCourses->fetch()) {
            $urls[] = ['loc' => APP_URL . '/course?slug=' . $row['slug'], 'lastmod' => date('Y-m-d', strtotime($row['updated_at'])), 'priority' => '0.9'];
        }

        header("Content-Type: application/xml; charset=utf-8");
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<?xml-stylesheet type="text/xsl" href="' . APP_URL . '/sitemap.xsl"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        foreach ($urls as $url) {
            echo '<url>';
            echo '<loc>' . htmlspecialchars($url['loc']) . '</loc>';
            if (isset($url['lastmod'])) {
                echo '<lastmod>' . $url['lastmod'] . '</lastmod>';
            }
            echo '<changefreq>weekly</changefreq>';
            if (isset($url['priority'])) {
                echo '<priority>' . $url['priority'] . '</priority>';
            }
            echo '</url>';
        }
        
        echo '</urlset>';
    }
}
