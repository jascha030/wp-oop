<?php

declare(strict_types=1);

namespace Jascha030\WP\OOPOR\Service\Hook\Reference;

use Closure;
use Exception;
use InvalidArgumentException;
use Jascha030\WP\OOPOR\Service\Hook\FilterManagerService;
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

    private string $context;

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

    final public function getType(): string
    {
    }

    final public function hook(Closure $closure, string $context): string
    {
        $this->closure = $closure;
        $this->setContext($context);

        if ($context === FilterManagerService::FILTER) {
            add_filter($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }

        if ($context === FilterManagerService::ACTION) {
            add_action($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }

        $this->hooked = true;

        return $this->getId();
    }

    final public function remove(): void
    {
        if ($this->context === FilterManagerService::FILTER) {
            remove_filter($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }

        if ($this->context === FilterManagerService::ACTION) {
            remove_action($this->tag, $this->closure, $this->priority, $this->acceptedArguments);
        }
    }

    final public function test(): void
    {
        if (! $this->hooked) {
            throw new Exception('Request to test method before being hooked.');
        }

        if ($this->context === FilterManagerService::FILTER) {
            do_filter($this->tag);
        }

        if ($this->context === FilterManagerService::ACTION) {
            do_action($this->tag);
        }
    }

    private function setContext(string $context): void
    {
        if (! in_array($context, FilterManagerService::HOOK_TYPES)) {
            throw new InvalidArgumentException(
                "context can be either: 'action' or 'filter', string: '{$context}' was provided."
            );
        }

        $this->context = $context;
    }
}
