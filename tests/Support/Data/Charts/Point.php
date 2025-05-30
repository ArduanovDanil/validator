<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\Charts;

use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class Point
{
    #[Each([new Nested(Coordinates::class, requirePropertyPath: true, noPropertyPathMessage: 'Custom message 4.')])]
    private readonly array $coordinates;
    #[Count(exactly: 3)]
    #[Each([new Number(min: 0, max: 255)], incorrectInputMessage: 'Custom message 5.')]
    private readonly array $rgb;
}
