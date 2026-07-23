<?php

namespace App\Enums;

enum NotificationAudience: string
{
    case Admin = 'admin';
    case Customer = 'customer';
}
