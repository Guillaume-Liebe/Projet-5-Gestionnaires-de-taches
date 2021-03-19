<?php

namespace App\Service;

Class StringUtils
{
    public function capitalize ($string) 
    {
        return ucfirst(mb_strtolower($string));
    }
}