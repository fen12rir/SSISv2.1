<?php
declare(strict_types=1);
//this class is used for passing echoed HTML elements as string literals
//this is a helper class for table template class
//If you use this class, use it on HTML elements so that the table template will not render it with htmlspecialchars method
namespace SSIS\Utils;
class SafeHtml {

    private $html;

    public function __construct(string $html) {
        $this->html = $html;
    }

    public function getHTML() : string {
        return $this->html;
    }
    public function __toString() : string {
        return $this->html;
    }
}