<?php

namespace Shift\Cli\Parsers\Finders;

use PhpParser\Node;

class DebugCalls
{
    public function search(Node $node)
    {
        if (! $node instanceof Node\Expr\FuncCall) {
            return false;
        }

        if (! $node->name instanceof Node\Name) {
            return false;
        }

        if (! in_array($node->name->toLowerString(), ['var_dump', 'var_export', 'dd', 'dump', 'print_r'])) {
            return false;
        }

        if (in_array($node->name->toLowerString(), ['var_export', 'print_r'])
            && count($node->args) === 2
            && $node->args[1]->value instanceof Node\Expr\ConstFetch
            && $node->args[1]->value->name->toLowerString() === 'true') {
            return false;
        }

        // TODO: filter special calls, e.g. `var_export($foo, true)`

        return true;
    }

    public function process(array $instances)
    {
        $output = [];

        foreach ($instances as $instance) {
            $output[] = [
                'line' => ['start' => $instance->getStartLine(), 'end' => $instance->getEndLine()],
                'offset' => ['start' => $instance->getStartFilePos(), 'end' => $instance->getEndFilePos()],
                'function' => $instance->name->toLowerString(),
            ];
        }

        return $output;
    }
}
