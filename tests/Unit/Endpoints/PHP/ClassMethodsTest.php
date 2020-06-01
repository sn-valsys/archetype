<?php
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\Print_;
use PhpParser\Node\Expr\Variable;

class ClassMethodsTest extends Archetype\Tests\FileTestCase
{
    /** @test */
    public function it_can_retrieve_class_method_names()
    {
        $file = PHPFile::load('app/Console/Kernel.php');

        $this->assertTrue(
            $file->methodNames() === ['schedule', 'commands']
        );
    }

    /** @test */
    public function it_can_retrieve_class_method_asts()
    {
        $methods = PHPFile::load('app/Console/Kernel.php')->classMethod();

            collect($methods)->each(function($method) {
                $this->assertInstanceOf(ClassMethod::class, $method);
            });
    }
    
    /** @test */
    public function it_can_add_class_methods()
    {
        $factory = new BuilderFactory;
        $file = PHPFile::load('app/User.php');

        $file = $file->add()->classMethod([
            $factory->method('insertedMethod')
                ->makeProtected() // ->makePublic() [default], ->makePrivate()
                ->addParam($factory->param('someParam')->setDefault('test'))
                // it is possible to add manually created nodes
                ->addStmt(new Print_(new Variable('someParam')))
                ->getNode()            
        ]);

        $this->assertContains(
            'insertedMethod', $file->methodNames()
        );
    }    
}