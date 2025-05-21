<?php
class Controller {
    protected $db;
    protected $data = [];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function view($template, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Include header
        if (file_exists("views/templates/header.php")) {
            require_once "views/templates/header.php";
        }
        
        // Include main template
        $templatePath = "views/{$template}.php";
        if (file_exists($templatePath)) {
            require_once $templatePath;
        } else {
            throw new Exception("View template not found: {$templatePath}");
        }
        
        // Include footer
        if (file_exists("views/templates/footer.php")) {
            require_once "views/templates/footer.php";
        }
    }

    protected function redirect($path) {
        header("Location: " . BASE_URL . $path);
        exit();
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function error404() {
        http_response_code(404);
        $this->view('errors/404');
        exit();
    }
} 