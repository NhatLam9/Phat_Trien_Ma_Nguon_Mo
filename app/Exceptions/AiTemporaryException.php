<?php

namespace App\Exceptions;

use Exception;

class AiTemporaryException extends Exception
{
    // Lỗi tạm thời (timeout, 429, 5xx) để Queue Job retry
}

