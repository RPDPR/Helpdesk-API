<?php

namespace App\Enums;

enum UserRole: string {
    case admin = "admin";
    case agent = "agent";
    case user = "user";
}