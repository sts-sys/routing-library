<?php
namespace sts\routes;

class TrieNode
{
    public $children = [];
    public $isEndOfRoute = false;
    public $routeData = null;

    public function __construct()
    {
        $this->children = [];
        $this->isEndOfRoute = false;
        $this->routeData = null;
    }
}
