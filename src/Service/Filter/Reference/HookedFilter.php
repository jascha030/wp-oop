<?php

declare(strict_types=1);

namespace Jascha030\WP\OOPOR\Service\Filter\Reference;

use Closure;
use Exception;
use InvalidArgumentException;
use Jascha030\WP\OOPOR\Service\Filter\Manager\FilterService;
use Symfony\Component\Uid\Uuid;

use function add_action;
use function add_filter;
use function do_action;
use function do_filter;
use function remove_action;
use function remove_filter;

class HookedFilter
{
    private Uuid $id;

    private string $tag;

    private int $priority;

    private int $acceptedArguments;

    private Closure $closure;

    private int $type;

    private int $called;

    private bool $hooked = false;

    public function __construct(string $tag, int $priority, int $acceptedArguments)
    {
        $this->id                = Uuid::v1();
        $this->tag               = $tag;
        $this->priority          = $priority;
        $this->acceptedArguments = $acceptedArguments;
    }

    final public function getId(): string
    {
        return $this->id->toRfc4122();
    }

    final public function call(): void
    {
        $this->called++;
    }

    final public function countCalled(): int
    {
        return $this->called;
    }

    final public function getType(): int
    {
        return $this->type;
    }

    final public function hook(Closure $closure, int $type): void
    {
        $this->closure = $closure;
        $this->setContext($type);

        if ($type === FilterService::FILTER) {
            add_filter($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }

        if ($type === FilterService::ACTION) {
            add_action($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }

        $this->hooked = true;
    }

    final public function remove(): void
    {
        if ($this->type === FilterService::FILTER) {
            remove_filter($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }

        if ($this->type === FilterService::ACTION) {
            remove_action($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }
    }

    final public function test(): void
    {
        if (! $this->hooked) {
            throw new Exception('Request to test method before being hooked.');
        }

        if ($this->type === FilterService::FILTER) {
            do_filter($this->tag);
        }

        if ($this->type === FilterService::ACTION) {
            do_action($this->tag);
        }
    }

    private function setContext(int $type): void
    {
        if (! array_key_exists($type, FilterService::HOOK_TYPES)) {
            throw new InvalidArgumentException(
                "context can be either: 'action' or 'filter', string: '{$type}' was provided."
            );
        }

        $this->type = $type;
    }
}
