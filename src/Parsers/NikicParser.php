<?php

namespace Shift\Cli\Parsers;

use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

class NikicParser
{
    private $finder;

    public function __construct($finder)
    {
        $this->finder = $finder;
    }

    public function parse($code)
    {
        $lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine',
                'endLine',
                'startFilePos',
                'endFilePos',
            ],
        ]);
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7, $lexer);
        $ast = $parser->parse($code);

        $nameResolver = new NameResolver(null, ['replaceNodes' => false]);
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor($nameResolver);
        $ast = $nodeTraverser->traverse($ast);

        $nodeFinder = new \PhpParser\NodeFinder();
        $instances = $nodeFinder->find($ast, [$this->finder, 'search']);

        return $this->finder->process($instances);
    }
}
