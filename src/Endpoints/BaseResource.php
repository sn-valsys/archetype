<?php

namespace PHPFileManipulator\Endpoints;

use PHPFileManipulator\PHPFile;
use PHPFileManipulator\Support\Endpoint;
use BadMethodCallException;
use Illuminate\Support\Str;

abstract class BaseResource extends Endpoint
{
    const NOT_IMPLEMENTED = 'Method not implemented for this resource';

    public function getResourceName()
    {
        return Str::replaceLast(
            'Resource', '', class_basename(static::class)
        );
    }

    public function __call($signature, $args)
    {        
        $handler = $this->getHandlerMethod($signature, $args);

        if(!$handler) {
            throw new BadMethodCallException("Endpoint " . static::class . " could not resolve method " . $signature);
        }

        return $this->$handler(...$args);
    }

    public function getHandlerMethod($signature, $args)
    {
        return collect(static::aliases())->map(function($alias) use($signature, $args) {
            // getters / setters
            if(preg_match("/^$alias\$/i", $signature)) {
                return $args ? 'set' : 'get';
            }

            // adders
            if(preg_match("/^add$alias\$/i", $signature)) {
                return 'add';
            }
            // removers
            if(preg_match("/^remove$alias\$/i", $signature)) {
                return 'remove';
            }

            // Could not handle the candidate
            return false;
        })->filter()->first();
    }    

    public function get()
    {
        throw new BadMethodCallException($this::NOT_IMPLEMENTED);
    }

    public function set($args)
    {
        throw new BadMethodCallException($this::NOT_IMPLEMENTED);
    }
    
    public function add($args)
    {
        throw new BadMethodCallException($this::NOT_IMPLEMENTED);
    }
    
    public function remove($args = null)
    {
        throw new BadMethodCallException($this::NOT_IMPLEMENTED);
    }
}