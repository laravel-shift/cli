<?php

namespace Shift\Cli\Parsers\Finders;

use PhpParser\Node;

class FormRequestStringRulesFinder
{
    public function search(Node $node)
    {
        if (! $node instanceof Node\Stmt\Return_) {
            return false;
        }

        if (! $node->expr instanceof Node\Expr\Array_) {
            return false;
        }

        return true;
    }

    public function process(array $instances)
    {
        $output = [];

        foreach ($instances as $instance) {
            $output[] = [
                'line' => ['start' => $instance->getAttribute('startLine'), 'end' => $instance->getAttribute('endLine')],
                'values' => $this->getValues($instance->expr),
            ];
        }

        return $output;
    }

    private function getValues(Node\Expr\Array_ $node)
    {
        $one = collect($node->items)
            ->filter(function ($item) {
                return $item->key instanceof Node\Scalar\String_
                    && $item->value instanceof Node\Expr\BinaryOp\Concat;
            })
            ->map(function ($item) {
                return [
                    'line' => ['start' => $item->getAttribute('startLine'), 'end' => $item->getAttribute('endLine')],
                    'key' => $item->key->value,
                    'value' => $this->expandConcat($item->value),
                ];
            })
            ->all();

        $two = collect($node->items)
            ->filter(function ($item) {
                return $item->key instanceof Node\Scalar\String_
                    && $item->value instanceof Node\Scalar\String_;
            })
            ->map(function ($item) {
                return [
                    'line' => ['start' => $item->getAttribute('startLine'), 'end' => $item->getAttribute('endLine')],
                    'key' => $item->key->value,
                    'value' => $item->value->value,
                ];
            })
            ->all();

        return $one + $two;
    }

    private function expandConcat(Node\Expr\BinaryOp\Concat $node): array
    {
        $parts = [];

        if ($node->left instanceof Node\Scalar\String_) {
            $parts[] = $node->left->value;
        } elseif ($node->left instanceof Node\Expr\BinaryOp\Concat) {
            $parts += $this->expandConcat($node->left);
        }

        if ($node->right instanceof Node\Scalar\String_) {
            $parts[] = $node->right->value;
        } elseif ($node->right instanceof Node\Expr\BinaryOp\Concat) {
            $parts += $this->expandConcat($node->right);
        }

        return $parts;
    }
}
