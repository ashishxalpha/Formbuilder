<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\Builder\FieldRegistry::class, function ($app) {
            $registry = new \App\Services\Builder\FieldRegistry();
            
            $fields = [
                \App\Services\Builder\Fields\TextField::class,
                \App\Services\Builder\Fields\TextareaField::class,
                \App\Services\Builder\Fields\NumberField::class,
                \App\Services\Builder\Fields\EmailField::class,
                \App\Services\Builder\Fields\PhoneField::class,
                \App\Services\Builder\Fields\DateField::class,
                \App\Services\Builder\Fields\DropdownField::class,
                \App\Services\Builder\Fields\RadioField::class,
                \App\Services\Builder\Fields\CheckboxField::class,
                \App\Services\Builder\Fields\FileUploadField::class,
                \App\Services\Builder\Fields\RatingField::class,
                \App\Services\Builder\Fields\SectionHeadingField::class,
            ];

            foreach ($fields as $fieldClass) {
                $registry->register($fieldClass::getType(), $fieldClass);
            }

            return $registry;
        });
    }

    public function boot(): void
    {
        //
    }
}
