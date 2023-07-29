<?php

namespace Shift\Cli\Parsers\Finders;

use PhpParser\Node;

class QueryOrderByFinder
{
    private bool $check_multiple_columns;

    private bool $requires_direction;

    public function __construct($check_multiple_columns = true, $requires_direction = false)
    {
        $this->check_multiple_columns = $check_multiple_columns;
        $this->requires_direction = $requires_direction;
    }

    public function search(Node $node)
    {
        if (! ($node instanceof Node\Expr\MethodCall || $node instanceof Node\Expr\StaticCall)) {
            return false;
        }

        if (! $this->isOrderByCall($node->name->name)) {
            return false;
        }

        if ($this->check_multiple_columns && ! $this->passesMultipleColumns($node)) {
            return false;
        }

        if (($this->requires_direction || count($node->args) > 1) && ! $this->passesExplicitDirection($node)) {
            return false;
        }

        return true;
    }

    public function process(array $instances)
    {
        $output = [];

        foreach ($instances as $instance) {
            $data = [
                'line' => ['start' => $instance->getAttribute('startLine'), 'end' => $instance->getAttribute('endLine')],
                'method' => strtolower($instance->name->name),
            ];

            if (! $this->check_multiple_columns && count($instance->args) > 1) {
                $data['direction'] = strtoupper($instance->args[1]->value->value);
            }

            $output[] = $data;
        }

        return $output;
    }

    private function passesExplicitDirection(Node $node)
    {
        if (! property_exists($node, 'args')) {
            return false;
        }

        if (count($node->args) < 2) {
            return false;
        }

        if ($node->args[1]->value->getType() === 'Scalar_String') {
            return in_array(strtolower($node->args[1]->value->value), ['asc', 'desc']);
        }

        return false;
    }

    private function passesMultipleColumns(Node $node)
    {
        if (! property_exists($node, 'args')) {
            return false;
        }

        if (count($node->args) < 2) {
            return false;
        }

        if ($node->args[1]->value->getType() === 'Scalar_String') {
            return ! in_array(strtolower($node->args[1]->value->value), ['asc', 'desc']);
        }

        return true;
    }

    private function isOrderByCall($name)
    {
        return in_array(strtolower($name), ['orderby', 'orderbydesc']);
    }
}
