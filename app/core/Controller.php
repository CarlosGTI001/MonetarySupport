<?php
declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.php';
        $layoutHeader = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'header.php';
        $layoutFooter = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . 'footer.php';

        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo 'Vista no encontrada.';
            return;
        }

        include $layoutHeader;
        include $viewPath;
        include $layoutFooter;
    }
}
