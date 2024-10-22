<?php

namespace App\Enums;

enum FileStatusEnum: int
{
    case QUEUED = 0;
    case PROCESSING = 1;
    case COMPLETED = 2;
    case ERROR = 3;
}
