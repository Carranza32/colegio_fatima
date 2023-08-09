<?php

namespace Backpack\Pro;

use Illuminate\Support\ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider { boot as autoBoot; }

    protected $vendorName = 'backpack';
    protected $packageName = 'pro';
    protected $commands = [];

    public function boot(): void
    {
        $this->autoBoot();

        // tell Backpack to automatically check the FIELDS directory in this package
        app()->config['backpack.crud.view_namespaces.fields'] = (function () {
            $fieldNamespaces = config('backpack.crud.view_namespaces.fields');
            $fieldNamespaces[] = $this->vendorName.'.'.$this->packageName.'::fields';

            return $fieldNamespaces;
        })();

        // tell Backpack to automatically check the COLUMNS directory in this package
        app()->config['backpack.crud.view_namespaces.columns'] = (function () {
            $fieldNamespaces = config('backpack.crud.view_namespaces.columns');
            $fieldNamespaces[] = $this->vendorName.'.'.$this->packageName.'::columns';

            return $fieldNamespaces;
        })();

        // tell Backpack to automatically check the BUTTONS directory in this package
        app()->config['backpack.crud.view_namespaces.buttons'] = (function () {
            $fieldNamespaces = config('backpack.crud.view_namespaces.buttons');
            $fieldNamespaces[] = $this->vendorName.'.'.$this->packageName.'::buttons';

            return $fieldNamespaces;
        })();

        // tell Backpack to automatically check the FILTERS directory in this package
        app()->config['backpack.crud.view_namespaces.filters'] = (function () {
            $fieldNamespaces = config('backpack.crud.view_namespaces.filters');
            $fieldNamespaces[] = $this->vendorName.'.'.$this->packageName.'::filters';

            return $fieldNamespaces;
        })();

        // tell Backpack to automatically check the WIDGETS directory in this package
        app()->config['backpack.base.component_view_namespaces.widgets'] = (function () {
            $fieldNamespaces = config('backpack.base.component_view_namespaces.widgets');
            $fieldNamespaces[] = $this->vendorName.'.'.$this->packageName.'::widgets';

            return $fieldNamespaces;
        })();
    }
}
