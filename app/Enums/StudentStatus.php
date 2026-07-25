<?php
namespace App\Enums;
enum StudentStatus: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
