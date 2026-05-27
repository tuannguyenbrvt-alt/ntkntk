<?php
class MenuHelper {
    public static function buildTree($elements, $parentId = null) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = self::buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

    public static function renderAdminTree($tree) {
        if (empty($tree)) return '';
        
        $html = '<ul class="list-group sortable-list">';
        foreach ($tree as $item) {
            $html .= '<li class="list-group-item" data-id="' . $item['id'] . '">';
            $html .= '<div class="d-flex justify-content-between align-items-center">';
            $html .= '<div><i class="bi bi-arrows-move me-2 text-muted handle" style="cursor: grab;"></i> <strong>' . htmlspecialchars($item['title']) . '</strong> <small class="text-muted ms-2">(' . htmlspecialchars($item['url']) . ')</small></div>';
            $html .= '<div>';
            $html .= '<button class="btn btn-sm btn-outline-primary me-2" onclick="editMenu(' . $item['id'] . ', \'' . htmlspecialchars(addslashes($item['title'])) . '\', \'' . htmlspecialchars(addslashes($item['url'])) . '\')"><i class="bi bi-pencil"></i></button>';
            $html .= '<form action="' . APP_URL . '/admin/menus/delete" method="POST" class="d-inline" onsubmit="return confirm(\'Bạn có chắc chắn muốn xóa menu này không?\')"><input type="hidden" name="id" value="' . $item['id'] . '"><button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>';
            $html .= '</div>';
            $html .= '</div>';
            
            if (isset($item['children'])) {
                $html .= self::renderAdminTree($item['children']);
            } else {
                $html .= '<ul class="list-group sortable-list empty-list" style="min-height: 20px;"></ul>';
            }
            
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public static function renderFrontendMenu($tree) {
        if (empty($tree)) return '';
        $html = '';
        foreach ($tree as $item) {
            $url = htmlspecialchars($item['url']);
            $title = htmlspecialchars($item['title']);
            // Nếu URL bắt đầu bằng http thì giữ nguyên, ngược lại thì gắn APP_URL phía trước
            $fullUrl = (strpos($url, 'http') === 0) ? $url : APP_URL . $url;
            
            // Intercept menu Học viên to dynamically inject Login/Register or Profile/Logout
            if ($title === 'Học viên') {
                $dynamicChildren = [];
                if (!isset($_SESSION['user_id'])) {
                    // Chưa đăng nhập
                    $dynamicChildren[] = ['title' => '<i class="bi bi-box-arrow-in-right me-2 text-success"></i>Đăng nhập', 'url' => '/login'];
                    $dynamicChildren[] = ['title' => '<i class="bi bi-person-plus me-2 text-success"></i>Đăng ký', 'url' => '/register'];
                } else {
                    // Đã đăng nhập
                    if (in_array($_SESSION['role'], ['super_admin', 'admin'])) {
                        $dynamicChildren[] = ['title' => '<i class="bi bi-speedometer2 me-2 text-primary"></i>Quản trị hệ thống', 'url' => '/admin/dashboard'];
                    }
                    $dynamicChildren[] = ['title' => '<i class="bi bi-graph-up-arrow me-2 text-success"></i>Kết quả học tập', 'url' => '/progress'];
                    $dynamicChildren[] = ['title' => '<i class="bi bi-person-badge me-2 text-success"></i>Hồ sơ học tập', 'url' => '/profile'];
                    $dynamicChildren[] = ['title' => '<i class="bi bi-box-arrow-right me-2 text-danger"></i>Đăng xuất', 'url' => '/logout', 'is_danger' => true];
                }
                
                // Add a divider if there are existing children
                if (isset($item['children']) && count($item['children']) > 0) {
                    $dynamicChildren[] = ['is_divider' => true];
                    foreach ($item['children'] as $child) {
                        $dynamicChildren[] = $child;
                    }
                } else {
                    // If no children in DB, add a divider and grade lookup as default
                    $dynamicChildren[] = ['is_divider' => true];
                    $dynamicChildren[] = ['title' => '<i class="bi bi-search me-2"></i>Tra cứu kết quả', 'url' => '/progress/lookup'];
                }
                $item['children'] = $dynamicChildren;
            }

            if (isset($item['children']) && count($item['children']) > 0) {
                $html .= '<li class="nav-item dropdown">';
                // If title is Học viên and user is logged in, we can optionally display their name
                $displayName = $title;
                if ($title === 'Học viên' && isset($_SESSION['user_id'])) {
                    $displayName = 'Học viên: <strong class="text-success-subtle ms-1">' . htmlspecialchars($_SESSION['full_name']) . '</strong>';
                }
                $html .= '<a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="' . $fullUrl . '" role="button" data-bs-toggle="dropdown">' . $displayName . '</a>';
                $html .= '<ul class="dropdown-menu dropdown-menu-dark shadow-sm border-0 dropdown-menu-end" style="border: 1px solid #333 !important;">';
                foreach ($item['children'] as $child) {
                    if (isset($child['is_divider']) && $child['is_divider']) {
                        $html .= '<li><hr class="dropdown-divider border-secondary border-opacity-25"></li>';
                        continue;
                    }
                    $childUrl = htmlspecialchars($child['url']);
                    $childTitle = $child['title']; // Do not use htmlspecialchars since it contains HTML icons
                    $childFullUrl = (strpos($childUrl, 'http') === 0) ? $childUrl : APP_URL . $childUrl;
                    
                    $class = "dropdown-item";
                    if (isset($child['is_danger']) && $child['is_danger']) {
                        $class .= " text-danger";
                    }
                    $html .= '<li><a class="' . $class . '" href="' . $childFullUrl . '">' . $childTitle . '</a></li>';
                    
                    if (isset($child['children'])) {
                        foreach ($child['children'] as $subchild) {
                            $subUrl = htmlspecialchars($subchild['url']);
                            $subFullUrl = (strpos($subUrl, 'http') === 0) ? $subUrl : APP_URL . $subUrl;
                            $html .= '<li><a class="dropdown-item ps-4 text-muted" href="' . $subFullUrl . '">- ' . htmlspecialchars($subchild['title']) . '</a></li>';
                        }
                    }
                }
                $html .= '</ul></li>';
            } else {
                $html .= '<li class="nav-item"><a class="nav-link" href="' . $fullUrl . '">' . $title . '</a></li>';
            }
        }
        return $html;
    }
}
