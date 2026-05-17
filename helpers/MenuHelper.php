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
            
            if (isset($item['children']) && count($item['children']) > 0) {
                $html .= '<li class="nav-item dropdown">';
                $html .= '<a class="nav-link dropdown-toggle" href="' . $fullUrl . '" role="button" data-bs-toggle="dropdown">' . $title . '</a>';
                $html .= '<ul class="dropdown-menu shadow-sm border-0">';
                foreach ($item['children'] as $child) {
                    $childUrl = htmlspecialchars($child['url']);
                    $childTitle = htmlspecialchars($child['title']);
                    $childFullUrl = (strpos($childUrl, 'http') === 0) ? $childUrl : APP_URL . $childUrl;
                    $html .= '<li><a class="dropdown-item" href="' . $childFullUrl . '">' . $childTitle . '</a></li>';
                    
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
