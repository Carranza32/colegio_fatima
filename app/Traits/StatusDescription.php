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
            return $this->active_string ?? __('crud.status.active');
            break;
        case static::$_STATUS_INACTIVE:
            return $this->disabled_string ?? __('crud.status.inactive');
            break;
        default:
            break;
        }
    }
}
