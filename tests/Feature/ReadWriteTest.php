<?php

use Archetype\Facades\LaravelFile;
use Archetype\Support\Exceptions\FileParseError;
use Archetype\Tests\Support\Facades\TestablePHPFile as PHPFile;
use Illuminate\Support\Facades\Config;

it('wont see debug or output folders because they are removed at start up', function() {
	$this->assertFalse(
		is_dir(Config::get('archetype.roots.debug.root'))
	);

	$this->assertFalse(
		is_dir(Config::get('archetype.roots.output.root'))
	);
});

it('can load php files', function() {
	$file = PHPFile::load('public/index.php');

	$this->assertTrue(
		get_class($file) === \Archetype\Tests\Support\TestablePHPFile::class
	);
});

it('will accept forbidden directories when explicitly passed', function() {
	$file = PHPFile::in(
		'vendor/ajthinking/archetype/src/snippets'
	)->get()->first();

	$this->assertTrue(
		get_class($file) === \Archetype\Tests\Support\TestablePHPFile::class
	);
});

it('can also load laravel specific files', function() {
	$file = LaravelFile::load('app/Models/User.php');

	$this->assertInstanceOf(
		\Archetype\LaravelFile::class,
		$file
	);
});

it('can write to default location', function() {
	// default save location is in .output when in development mode
	LaravelFile::load('app/Models/User.php')->save();
	
	$this->assertTrue(
		is_file(Config::get('archetype.roots.output.root') . '/app/Models/User.php')
	);

	// debug
	LaravelFile::load('app/Models/User.php')->debug();

	$this->assertTrue(
		is_file(Config::get('archetype.roots.debug.root') . '/app/Models/User.php')
	);
});

it('will throw error if code cant be parsed', function() {
	$this->expectException(FileParseError::class);

	PHPFile::directive('addMissingTags', false) // this is the default when not testing
		->fromString("<?php bad code");
});

it('can ensure code is valid', function() {
	PHPFile::fromString('$ok = 1')
		->assertValidPhp();
});

it('can load files inside default root using an absolute path', function() {
	PHPFile::load(
		base_path('app/Models/User.php')
	)->assertValidPhp();
});

it('can load files outside default root using an absolute path', function() {
	PHPFile::load(
		__DIR__."/../TestCase.php"
	)->assertValidPhp();
});