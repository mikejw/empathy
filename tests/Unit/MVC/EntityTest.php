<?php

declare(strict_types=1);

use Empathy\MVC\EntityManager;
use Empathy\MVC\EntityPopulator;
use Empathy\MVC\Util\Testing\Util\Config;
use Empathy\MVC\Util\Testing\Util\DB;
use Empathy\MVC\Util\Testing\Util\FakeEntity;
use Nelmio\Alice\Fixtures\Loader;

function loadMvcEntityFixtures(string $reset, string $file): void
{
    $populator = new EntityPopulator();
    DB::reset($reset);
    $objectManager = new EntityManager();

    $path = Config::get('util_dir').'/'.$file;
    $loader = new Loader();
    $loader->addPopulator($populator);

    /** @var list<object> $objects */
    $objects = array_values($loader->load($path));
    $objectManager->persist($objects);
}

beforeEach(function () {
    DB::loadDefDBCreds();
});

test('find throws for unknown entity class', function () {
    loadMvcEntityFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');

    $objectManager = new EntityManager();
    expect(fn () => $objectManager->find('Esuite\FakeEntityz', 1))->toThrow(\Exception::class);
});

test('find loads entity from fixtures', function () {
    loadMvcEntityFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');

    $objectManager = new EntityManager();
    $fake = $objectManager->find(FakeEntity::class, 1);
    assert($fake instanceof FakeEntity);
    expect($fake)->toBeInstanceOf(FakeEntity::class);
    expect($fake->foo)->toBe('bar');
});

test('save persists entity changes', function () {
    loadMvcEntityFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');

    $objectManager = new EntityManager();
    $fake = $objectManager->find(FakeEntity::class, 1);
    assert($fake instanceof FakeEntity);
    $fake->foo = 'new';
    $fake->save();
    $fake = $objectManager->find(FakeEntity::class, 1);
    assert($fake instanceof FakeEntity);
    expect($fake->foo)->toBe('new');
});

test('getAll returns expected row count', function () {
    loadMvcEntityFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');

    $objectManager = new EntityManager();
    $fake = $objectManager->find(FakeEntity::class, 1);
    assert($fake instanceof FakeEntity);
    expect(count($fake->getAll()))->toBe(10);
});

test('getAllCustom returns expected row count', function () {
    loadMvcEntityFixtures('fixtures/dd.sql', 'fixtures/fixtures1.yml');

    $objectManager = new EntityManager();
    $fake = $objectManager->find(FakeEntity::class, 1);
    assert($fake instanceof FakeEntity);
    expect(count($fake->getAllCustom(' where foo like \'bar\'')))->toBe(10);
});
