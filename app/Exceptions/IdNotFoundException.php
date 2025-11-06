<?php
declare(strict_types=1);

class IdNotFoundException extends Exception {

    public function __construct($message = '', $count = 0, ?Throwable $previous= null) {
        parent::__construct($message, $count,$previous);
    }
}