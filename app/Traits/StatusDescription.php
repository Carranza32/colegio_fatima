<?php

namespace  App\Traits;

trait StatusDescription
{
    public static $_STATUS_ACTIVE = 1;

    public static $_STATUS_INACTIVE = 0;

    public function getStatusDescriptionAttribute()
    {
        switch ($this->is_active) {
        case static::$_STATUS_ACTIVE:
            return 'Activo';
            break;
        case static::$_STATUS_INACTIVE:
            return 'Inactivo';
            break;
        default:
            break;
        }
    }
}
