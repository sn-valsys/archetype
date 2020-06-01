<?php

namespace Archetype\Tests\Unit;

use Archetype\Tests\FileTestCase;
use PHPFile;
use LaravelFile;
use Archetype\Endpoints\PHP\PHPFileQueryBuilder;
use Archetype\Endpoints\Laravel\LaravelFileQueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PHPFileQueryBuilderTest extends FileTestCase
{
    /** @test */
    public function it_can_instanciate_via_php_or_laravel_file_with_in_method()
    {
        $this->assertInstanceOf(
            PHPFileQueryBuilder::class,
            PHPFile::in('app')
        );

        $this->assertInstanceOf(
            LaravelFileQueryBuilder::class,
            LaravelFile::in('app')
        );
    }

    /** @test */
    public function it_will_return_a_collection_on_get()
    {
        $this->assertInstanceOf(
            Collection::class,
            LaravelFile::in('app')->get()
        ); 
        
        $this->assertInstanceOf(
            Collection::class,
            LaravelFile::get()
        ); 
    }
    
    /** @test */
    public function it_can_filter_with_in_method()
    {
        $this->assertCount(
            1, LaravelFile::in('public')->get()
        );

        $this->assertCount(
            8, LaravelFile::in('app/Http/Middleware')->get()
        );
    }

    /** @test */
    public function it_can_filter_with_where_method()
    {
        $this->assertCount(
            0, LaravelFile::in('public')->where('className', 'User')->get()
        );
        
        $this->assertCount(
            1, LaravelFile::in('app')->where('className', '=', 'User')->get()
        );

        $this->assertCount(
            2, LaravelFile::in('database/migrations')->where('className', '!=', 'CreateUsersTable')->get()
        );        

        $this->assertCount(
            1, LaravelFile::in('app')->where('use', 'contains', 'Illuminate\Contracts\Auth\MustVerifyEmail')->get()
        );

        $this->assertCount(
            1, LaravelFile::in('app')->where('className', 'like', 'Controller')->get()
        );

        $this->assertCount(
            1, LaravelFile::in('app')->where('className', 'like', 'controller')->get()
        );        
        
        $this->assertCount(
            1, LaravelFile::in('app')->where('className', 'matches', '/^Controller/')->get()
        );
        
        $this->assertCount(
            1, LaravelFile::in('app')->where('className', 'in', ['Dog', 'User', 'Cat'])->get()
        );

        $this->assertCount(
            2, LaravelFile::in('app')->where('use', 'count', 4)->get()
        );        
    }

    /** @test */
    public function it_can_filter_with_where_method_using_an_array()
    {
        $this->assertCount(
            1, LaravelFile::in('app')->where([
                ['className', 'like', 'provider'],
                ['methodNames', 'contains', 'mapWebRoutes']
            ])->get()
        );        
    }

    /** @test */
    public function it_can_add_filters_with_andWhere()
    {
        $this->assertCount(
            1, LaravelFile::in('app')
                ->where('className', 'like', 'provider')
                ->andWhere('methodNames', 'contains', 'mapWebRoutes')
                ->get()
        );        
    }    

    /** @test */
    public function it_can_filter_with_closure()
    {
        dd(
            glob(
                base_path('database/migrations') . '/*'
            ),
            //LaravelFile::load('database/migrations/2014_10_12_100000_create_password_resets_table.php')->className()
        );

        $this->assertCount(
            3, LaravelFile::in('database/migrations')->where(function($file) {
                return preg_match('/^Create.*Table$/', $file->className()); 
            })->get()
        );
    }
    
    /** @test */
    public function it_can_query_non_class_files_and_files_missing_extend()
    {        
        $files = LaravelFile::where('extends', 'Authenticatable')->get();
        $this->assertTrue(
            $files->count() > 0
        );
    }
    
    /** @test */
    public function it_can_chain()
    {        
        $files = LaravelFile::where('extends', 'ServiceProvider')
            ->where('methodNames', 'contains', 'boot')
            ->where(function($file) {
                return $file->className() == 'AuthServiceProvider'; 
            })->get();

        $this->assertCount(
            1, $files
        );
    }
    
    /** @test */
    public function it_has_a_first_method()
    {        
        $this->assertInstanceOf(
            \Archetype\LaravelFile::class,
            LaravelFile::in('public')->first()
        );
    }     
}
