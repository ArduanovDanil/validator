<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use DateTime;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\NotEqual;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class NotEqualTest extends RuleTestCase
{
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new NotEqual(1);
        $this->assertSame(NotEqual::class, $rule->getName());
    }

    public static function dataOptions(): array
    {
        return [
            'default' => [
                new NotEqual(1),
                [
                    'targetValue' => 1,
                    'targetProperty' => null,
                    'incorrectInputMessage' => [
                        'template' => 'The allowed types for {property} are integer, float, string, boolean, null ' .
                            'and object implementing \Stringable interface or \DateTimeInterface. {type} given.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetProperty' => null,
                            'targetValueOrProperty' => 1,
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => '{Property} returned from a custom data set must have one of the following ' .
                            'types: integer, float, string, boolean, null or an object implementing \Stringable ' .
                            'interface or \DateTimeInterface.',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetProperty' => null,
                            'targetValueOrProperty' => 1,
                        ],
                    ],
                    'message' => [
                        'template' => '{Property} must not be equal to "{targetValueOrProperty}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetProperty' => null,
                            'targetValueOrProperty' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '!=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'custom' => [
                new NotEqual(
                    new DateTime('2023-02-07 12:57:12'),
                    targetProperty: 'test',
                    incorrectInputMessage: 'Custom message 1.',
                    incorrectDataSetTypeMessage: 'Custom message 2.',
                    message: 'Custom message 3.',
                    type: CompareType::ORIGINAL,
                    strict: true,
                    skipOnEmpty: true,
                    skipOnError: true,
                    when: static fn (): bool => true,
                ),
                [
                    'targetProperty' => 'test',
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [
                            'targetProperty' => 'test',
                        ],
                    ],
                    'incorrectDataSetTypeMessage' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [
                            'targetProperty' => 'test',
                        ],
                    ],
                    'message' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [
                            'targetProperty' => 'test',
                        ],
                    ],
                    'type' => 'original',
                    'operator' => '!==',
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            [101, [new NotEqual(100)]],
            ['101', [new NotEqual(101, strict: true)]],
        ];
    }

    public static function dataValidationFailed(): array
    {
        return [
            [100, [new NotEqual(100)], ['' => ['Value must not be equal to "100".']]],
            [100, [new NotEqual(100, strict: true)], ['' => ['Value must not be strictly equal to "100".']]],
            'custom error' => [100, [new NotEqual(100, message: 'Custom error')], ['' => ['Custom error']]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new NotEqual(1), new NotEqual(1, skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new NotEqual(1), new NotEqual(1, when: $when));
    }
}
