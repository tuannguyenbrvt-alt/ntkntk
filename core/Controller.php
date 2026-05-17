<?php
class Controller {
    protected function render($view, $data = [], $layout = 'main') {
        // Biến mảng $data thành các biến cục bộ
        extract($data);
        
        $viewFile = "views/{$view}.php";
        
        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            require "views/layouts/{$layout}.php";
        } else {
            die("View {$viewFile} không tồn tại.");
        }
    }

    protected function redirect($url) {
        header("Location: " . APP_URL . $url);
        exit;
    }
}
