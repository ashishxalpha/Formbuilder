<?php

use App\Services\Builder\FieldRegistry;
use App\Services\Builder\Fields\TextField;

test('it can register and retrieve a field class', function () {
    $registry = new FieldRegistry();
    $registry->register('text', TextField::class);

    $class = $registry->getFieldClass('text');
    expect($class)->toBe(TextField::class);
});

test('it throws exception for invalid field class', function () {
    $registry = new FieldRegistry();
    $registry->register('invalid', \stdClass::class);
})->throws(\InvalidArgumentException::class);
