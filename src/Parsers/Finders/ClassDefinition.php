<?php

namespace Shift\Cli\Parsers\Finders;

use PhpParser\Node;

class ClassDefinition
{
    public function search(Node $node)
    {
        return $node instanceof Node\Stmt\Class_;
    }

    public function process(array $instances)
    {
        return [
            'line' => ['start' => $instances[0]->getStartLine(), 'end' => $instances[0]->getEndLine()],
            'offset' => ['start' => $instances[0]->getStartFilePos(), 'end' => $instances[0]->getEndFilePos()],
            'constants' => $this->getConstants($instances[0]),
            'properties' => $this->getProperties($instances[0]),
            'methods' => $this->getMethods($instances[0]),
        ];
    }

    private function getComments($node)
    {
        $comments = $node->getComments();
        if (empty($comments)) {
            return null;
        }

        $last = array_key_last($comments);

        return [
            'line' => ['start' => $comments[0]->getStartLine(), 'end' => $comments[$last]->getEndLine()],
            'offset' => ['start' => $comments[0]->getStartFilePos(), 'end' => $comments[$last]->getEndFilePos()],
        ];
    }

    private function getConstants(Node\Stmt\Class_ $class): array
    {
        $constants = [];
        foreach ($class->getConstants() as $constant) {
            $name = $constant->consts[0]->name->toString();
            $constants[$name] = [
                'line' => ['start' => $constant->getStartLine(), 'end' => $constant->getEndLine()],
                'offset' => ['start' => $constant->getStartFilePos(), 'end' => $constant->getEndFilePos()],
                'comment' => $this->getComments($constant),
                'name' => $name,
                'visibility' => $this->getVisibility($constant),
            ];
        }

        return $constants;
    }

    private function getMethods(Node\Stmt\Class_ $class): array
    {
        $methods = [];
        foreach ($class->getMethods() as $method) {
            $name = $method->name->toString();
            $methods[$name] = [
                'line' => ['start' => $method->getStartLine(), 'end' => $method->getEndLine()],
                'offset' => ['start' => $method->getStartFilePos(), 'end' => $method->getEndFilePos()],
                'comment' => $this->getComments($method),
                'name' => $name,
                'visibility' => $this->getVisibility($method),
                'static' => $method->isStatic(),
            ];
        }

        return $methods;
    }

    private function getProperties(Node\Stmt\Class_ $class)
    {
        $properties = [];
        foreach ($class->getProperties() as $property) {
            $name = $property->props[0]->name->toString();
            $properties[$name] = [
                'line' => ['start' => $property->getStartLine(), 'end' => $property->getEndLine()],
                'offset' => ['start' => $property->getStartFilePos(), 'end' => $property->getEndFilePos()],
                'comment' => $this->getComments($property),
                'name' => $name,
                'visibility' => $this->getVisibility($property),
                'static' => $property->isStatic(),
            ];
        }

        return $properties;
    }

    private function getVisibility($node): string
    {
        if ($node->isPrivate()) {
            return 'private';
        } elseif ($node->isProtected()) {
            return 'protected';
        }

        return 'public';
    }
}
