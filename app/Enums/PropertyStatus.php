<?php

namespace App\Enums;

enum PropertyStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Sold = 'sold';
    case Rented = 'rented';
}
