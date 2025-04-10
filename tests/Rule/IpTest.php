<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Yiisoft\Validator\Rule\Ip;
use Yiisoft\Validator\Rule\IpHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\RuleWithOptionsTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

final class IpTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use RuleWithOptionsTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Ip();
        $this->assertSame(Ip::class, $rule->getName());
    }

    public static function getNetworksData(): array
    {
        return [
            'default' => [
                [],
                [
                    '*' => ['any'],
                    'any' => ['0.0.0.0/0', '::/0'],
                    'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                    'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                    'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                    'localhost' => ['127.0.0.0/8', '::1'],
                    'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                    'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                ],
            ],
            'custom' => [
                ['custom' => ['1.1.1.1/1', '2.2.2.2/2']],
                [
                    '*' => ['any'],
                    'any' => ['0.0.0.0/0', '::/0'],
                    'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                    'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                    'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                    'localhost' => ['127.0.0.0/8', '::1'],
                    'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                    'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    'custom' => ['1.1.1.1/1', '2.2.2.2/2'],
                ],
            ],
        ];
    }

    #[DataProvider('getNetworksData')]
    public function testGetNetworks(array $networksArgument, array $expectedNetworks): void
    {
        $rule = new Ip(networks: $networksArgument);
        $this->assertSame($expectedNetworks, $rule->getNetworks());
    }

    public static function dataOptions(): array
    {
        return [
            'disallow IPv4' => [
                new Ip(allowIpv4: false),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => false,
                    'allowIpv6' => true,
                    'allowSubnet' => false,
                    'requireSubnet' => false,
                    'allowNegation' => false,
                    'incorrectInputMessage' => [
                        'template' => '{Property} must be a string. {type} given.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => '{Property} must be a valid IP address.',
                        'parameters' => [],
                    ],
                    'ipv4NotAllowedMessage' => [
                        'template' => '{Property} must not be an IPv4 address.',
                        'parameters' => [],
                    ],
                    'ipv6NotAllowedMessage' => [
                        'template' => '{Property} must not be an IPv6 address.',
                        'parameters' => [],
                    ],
                    'wrongCidrMessage' => [
                        'template' => '{Property} contains wrong subnet mask.',
                        'parameters' => [],
                    ],
                    'noSubnetMessage' => [
                        'template' => '{Property} must be an IP address with specified subnet.',
                        'parameters' => [],
                    ],
                    'hasSubnetMessage' => [
                        'template' => '{Property} must not be a subnet.',
                        'parameters' => [],
                    ],
                    'notInRangeMessage' => [
                        'template' => '{Property} is not in the allowed range.',
                        'parameters' => [],
                    ],
                    'ranges' => [],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            'disallow IPv6, custom' => [
                new Ip(
                    allowIpv6: false,
                    allowSubnet: true,
                    requireSubnet: true,
                    allowNegation: true,
                    incorrectInputMessage: 'Custom message 1.',
                    message: 'Custom message 2.',
                    ipv4NotAllowedMessage: 'Custom message 3.',
                    ipv6NotAllowedMessage: 'Custom message 4.',
                    wrongCidrMessage: 'Custom message 5.',
                    noSubnetMessage: 'Custom message 6.',
                    hasSubnetMessage: 'Custom message 7.',
                    notInRangeMessage: 'Custom message 8.',
                    ranges: ['private'],
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'networks' => [
                        '*' => ['any'],
                        'any' => ['0.0.0.0/0', '::/0'],
                        'private' => ['10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', 'fd00::/8'],
                        'multicast' => ['224.0.0.0/4', 'ff00::/8'],
                        'linklocal' => ['169.254.0.0/16', 'fe80::/10'],
                        'localhost' => ['127.0.0.0/8', '::1'],
                        'documentation' => ['192.0.2.0/24', '198.51.100.0/24', '203.0.113.0/24', '2001:db8::/32'],
                        'system' => ['multicast', 'linklocal', 'localhost', 'documentation'],
                    ],
                    'allowIpv4' => true,
                    'allowIpv6' => false,
                    'allowSubnet' => true,
                    'requireSubnet' => true,
                    'allowNegation' => true,
                    'incorrectInputMessage' => [
                        'template' => 'Custom message 1.',
                        'parameters' => [],
                    ],
                    'message' => [
                        'template' => 'Custom message 2.',
                        'parameters' => [],
                    ],
                    'ipv4NotAllowedMessage' => [
                        'template' => 'Custom message 3.',
                        'parameters' => [],
                    ],
                    'ipv6NotAllowedMessage' => [
                        'template' => 'Custom message 4.',
                        'parameters' => [],
                    ],
                    'wrongCidrMessage' => [
                        'template' => 'Custom message 5.',
                        'parameters' => [],
                    ],
                    'noSubnetMessage' => [
                        'template' => 'Custom message 6.',
                        'parameters' => [],
                    ],
                    'hasSubnetMessage' => [
                        'template' => 'Custom message 7.',
                        'parameters' => [],
                    ],
                    'notInRangeMessage' => [
                        'template' => 'Custom message 8.',
                        'parameters' => [],
                    ],
                    'ranges' => [
                        '10.0.0.0/8',
                        '172.16.0.0/12',
                        '192.168.0.0/16',
                        'fd00::/8',
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    public static function dataValidationPassed(): array
    {
        return [
            ['192.168.10.11', [new Ip()]],

            ['10.0.0.1', [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])]],
            ['192.168.5.101', [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])]],
            ['cafe::babe', [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])]],

            ['192.168.5.32/11', [new Ip(allowSubnet: true)]],
            ['192.168.5.32/32', [new Ip(allowSubnet: true)]],
            ['0.0.0.0/0', [new Ip(allowSubnet: true)]],

            ['10.0.0.1/24', [new Ip(requireSubnet: true)]],
            ['10.0.0.1/0', [new Ip(requireSubnet: true)]],
            ['!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)]],

            ['2008:fa::1', [new Ip()]],
            ['2008:00fa::0001', [new Ip()]],
            ['2008:fa::1', [new Ip(allowIpv4: false)]],
            ['2008:fa::0:1/64', [new Ip(allowIpv4: false, allowSubnet: true)]],
            ['2008:fa::0:1/128', [new Ip(allowIpv4: false, allowSubnet: true)]],
            ['2008:fa::0:1/0', [new Ip(allowIpv4: false, allowSubnet: true)]],
            ['2008:db0::1/64', [new Ip(allowIpv4: false, requireSubnet: true)]],
            ['!2008:fa::0:1/64', [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true)]],

            ['192.168.10.11', [new Ip()]],
            ['2008:fa::1', [new Ip()]],
            ['2008:00fa::0001', [new Ip()]],

            ['2008:fa::1', [new Ip(allowIpv4: false)]],
            ['192.168.10.11', [new Ip(allowIpv6: false)]],

            ['192.168.5.32/11', [new Ip(requireSubnet: true)]],
            ['192.168.5.32/32', [new Ip(requireSubnet: true)]],
            ['0.0.0.0/0', [new Ip(requireSubnet: true)]],
            ['2008:fa::0:1/64', [new Ip(requireSubnet: true)]],
            ['2008:fa::0:1/128', [new Ip(requireSubnet: true)]],
            ['2008:fa::0:1/0', [new Ip(requireSubnet: true)]],
            ['10.0.0.1/24', [new Ip(requireSubnet: true)]],
            ['10.0.0.1/0', [new Ip(requireSubnet: true)]],
            ['2008:db0::1/64', [new Ip(requireSubnet: true)]],

            ['!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)]],
            ['!2008:fa::0:1/64', [new Ip(requireSubnet: true, allowNegation: true)]],

            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24'])]],
            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24'])]],
            ['127.0.0.1', [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost'])]],
            ['10.0.1.2', [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])]],
            ['127.0.0.1', [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])]],
            ['10.0.1.28/28', [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])]],

            ['2001:db0:1:1::6', [new Ip(ranges: ['2001:db0:1:1::/64'])]],
            ['2001:db0:1:2::7', [new Ip(ranges: ['2001:db0:1:2::/64'])]],
            ['2001:db0:1:2::7', [new Ip(allowSubnet: true, ranges: ['2001:db0:1:2::/64', '!2001:db0::/32'])]],

            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24'])]],
            ['2001:db0:1:2::7', [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1'])]],
            ['10.0.1.2', [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1'])]],
            ['8.8.8.8', [new Ip(ranges: ['!system', 'any'])]],
            [
                '10.0.1.2',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],
            [
                '2001:db0:1:2::7',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],
            [
                '127.0.0.1',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],
            [
                '10.0.1.28/28',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
            ],

            ['1.2.3.4', [new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu'])]],
            ['5.6.7.8', [new Ip(networks: ['myNetworkEu' => ['1.2.3.4/10', '5.6.7.8']], ranges: ['myNetworkEu'])]],
        ];
    }

    public static function dataValidationFailed(): array
    {
        $message = 'Value must be a valid IP address.';
        $hasSubnetMessage = 'Value must not be a subnet.';
        $notInRangeMessage = 'Value is not in the allowed range.';
        $ipv4NotAllowedMessage = 'Value must not be an IPv4 address.';
        $wrongCidrMessage = 'Value contains wrong subnet mask.';
        $noSubnetMessage = 'Value must be an IP address with specified subnet.';
        $ipv6NotAllowedMessage = 'Value must not be an IPv6 address.';

        return [
            'incorrect input, array' => [
                ['what an array', '??'],
                [new Ip()],
                ['' => ['Value must be a string. array given.']],
            ],
            'incorrect input, integer' => [123456, [new Ip()], ['' => ['Value must be a string. int given.']]],
            'incorrect input, boolean (true)' => [true, [new Ip()], ['' => ['Value must be a string. bool given.']]],
            'incorrect input, boolean (false)' => [false, [new Ip()], ['' => ['Value must be a string. bool given.']],
            ],
            'incorrect input, null' => [null, [new Ip()], ['' => ['Value must be a string. null given.']]],
            'custom incorrect input message' => [
                1,
                [new Ip(incorrectInputMessage: 'Custom incorrect input message.')],
                ['' => ['Custom incorrect input message.']],
            ],
            'custom incorrect input message with parameters' => [
                1,
                [new Ip(incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['' => ['Property - value, type - int.']],
            ],
            'custom incorrect input message with parameters, property set' => [
                ['data' => 1],
                ['data' => new Ip(incorrectInputMessage: 'Property - {property}, type - {type}.')],
                ['data' => ['Property - data, type - int.']],
            ],

            // Small length
            ['1', [new Ip()], ['' => [$message]]],
            ['1.1.1.', [new Ip()], ['' => [$message]]],
            ['1.1.1', [new Ip()], ['' => [$message]]],

            ['not.an.ip', [new Ip()], ['' => [$message]]],
            ['bad:forSure', [new Ip()], ['' => [$message]]],

            ['2008:fz::0', [new Ip()], ['' => [$message]]],
            ['2008:fa::0::1', [new Ip()], ['' => [$message]]],
            ['!2008:fa::0::1', [new Ip()], ['' => [$message]]],
            ['2008:fa::0:1/64', [new Ip()], ['' => [$hasSubnetMessage]]],
            'custom has subnet message' => [
                '2008:fa::0:1/64',
                [new Ip(hasSubnetMessage: 'Custom has subnet message.')],
                ['' => ['Custom has subnet message.']],
            ],
            'custom has subnet message with parameters' => [
                '2008:fa::0:1/64',
                [new Ip(hasSubnetMessage: 'Property - {property}, value - {value}.')],
                ['' => ['Property - value, value - 2008:fa::0:1/64.']],
            ],
            'custom has subnet message with parameters, property set' => [
                ['data' => '2008:fa::0:1/64'],
                ['data' => new Ip(hasSubnetMessage: 'Property - {property}, value - {value}.')],
                ['data' => ['Property - data, value - 2008:fa::0:1/64.']],
            ],

            [
                'babe::cafe',
                [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])],
                ['' => [$notInRangeMessage]],
            ],
            [
                '10.0.0.2',
                [new Ip(ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'])],
                ['' => [$notInRangeMessage]],
            ],
            'custom not in range message' => [
                '10.0.0.2',
                [
                    new Ip(
                        notInRangeMessage: 'Custom not in range message.',
                        ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'],
                    ),
                ],
                ['' => ['Custom not in range message.']],
            ],
            'custom not in range message with parameters' => [
                '10.0.0.2',
                [
                    new Ip(
                        notInRangeMessage: 'Property - {Property}, value - {value}.',
                        ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'],
                    ),
                ],
                ['' => ['Property - Value, value - 10.0.0.2.']],
            ],
            'custom not in range message with parameters, property set' => [
                ['data' => '10.0.0.2'],
                [
                    'data' => new Ip(
                        notInRangeMessage: 'Property - {property}, value - {value}.',
                        ranges: ['10.0.0.1', '!10.0.0.0/8', '!babe::/8', 'any'],
                    ),
                ],
                ['data' => ['Property - data, value - 10.0.0.2.']],
            ],

            'leading zeroes' => ['192.168.005.001', [new Ip()], ['' => [$message]]],
            ['192.168.5.321', [new Ip()], ['' => [$message]]],
            ['!192.168.5.32', [new Ip()], ['' => [$message]]],
            ['192.168.5.32/11', [new Ip()], ['' => [$hasSubnetMessage]]],
            ['192.168.10.11', [new Ip(allowIpv4: false)], ['' => [$ipv4NotAllowedMessage]]],
            'custom IPv4 not allowed message' => [
                '192.168.10.11',
                [new Ip(allowIpv4: false, ipv4NotAllowedMessage: 'Custom IPv4 not allowed message.')],
                ['' => ['Custom IPv4 not allowed message.']],
            ],
            'custom IPv4 not allowed message with parameters' => [
                '192.168.10.11',
                [new Ip(allowIpv4: false, ipv4NotAllowedMessage: 'Property - {property}, value - {value}.')],
                ['' => ['Property - value, value - 192.168.10.11.']],
            ],
            'custom IPv4 not allowed message with parameters, property set' => [
                ['data' => '192.168.10.11'],
                [
                    'data' => new Ip(
                        allowIpv4: false,
                        ipv4NotAllowedMessage: 'Property - {property}, value - {value}.',
                    ),
                ],
                ['data' => ['Property - data, value - 192.168.10.11.']],
            ],

            ['192.168.5.32/33', [new Ip(allowSubnet: true)], ['' => [$wrongCidrMessage]]],
            'custom wrong CIDR message' => [
                '192.168.5.32/33',
                [new Ip(allowSubnet: true, wrongCidrMessage: 'Custom wrong CIDR message.')],
                ['' => ['Custom wrong CIDR message.']],
            ],
            'custom wrong CIDR message with parameters' => [
                '192.168.5.32/33',
                [new Ip(allowSubnet: true, wrongCidrMessage: 'Property - {property}, value - {value}.')],
                ['' => ['Property - value, value - 192.168.5.32/33.']],
            ],
            'custom wrong CIDR message with parameters, property set' => [
                ['data' => '192.168.5.32/33'],
                ['data' => new Ip(allowSubnet: true, wrongCidrMessage: 'Property - {property}, value - {value}.')],
                ['data' => ['Property - data, value - 192.168.5.32/33.']],
            ],

            ['192.168.5.32/af', [new Ip(allowSubnet: true)], ['' => [$message]]],
            ['192.168.5.32/11/12', [new Ip(allowSubnet: true)], ['' => [$message]]],
            ['10.0.0.1', [new Ip(requireSubnet: true)], ['' => [$noSubnetMessage]]],
            'custom no subnet message' => [
                '10.0.0.1',
                [new Ip(requireSubnet: true, noSubnetMessage: 'Custom no subnet message.')],
                ['' => ['Custom no subnet message.']],
            ],
            'custom no subnet message with parameters' => [
                '10.0.0.1',
                [new Ip(requireSubnet: true, noSubnetMessage: 'Property - {property}, value - {value}.')],
                ['' => ['Property - value, value - 10.0.0.1.']],
            ],
            'custom no subnet message with parameters, property set' => [
                ['data' => '10.0.0.1'],
                ['data' => new Ip(requireSubnet: true, noSubnetMessage: 'Property - {property}, value - {value}.')],
                ['data' => ['Property - data, value - 10.0.0.1.']],
            ],

            ['!!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)], ['' => [$message]]],

            ['!2008:fa::0:1/0', [new Ip(allowIpv4: false, allowSubnet: true)], ['' => [$message]]],
            ['2008:fz::0/129', [new Ip(allowIpv4: false, allowSubnet: true)], ['' => [$message]]],
            ['2008:db0::1', [new Ip(allowIpv4: false, requireSubnet: true)], ['' => [$noSubnetMessage]]],
            [
                '!!2008:fa::0:1/64',
                [new Ip(allowIpv4: false, requireSubnet: true, allowNegation: true)],
                ['' => [$message]],
            ],

            ['2008:fa::1', [new Ip(allowIpv6: false)], ['' => [$ipv6NotAllowedMessage]]],
            'custom IPv6 not allowed message' => [
                '2008:fa::1',
                [new Ip(allowIpv6: false, ipv6NotAllowedMessage: 'Custom IPv6 not allowed message.')],
                ['' => ['Custom IPv6 not allowed message.']],
            ],
            'custom IPv6 not allowed message with parameters' => [
                '2008:fa::1',
                [new Ip(allowIpv6: false, ipv6NotAllowedMessage: 'Property - {property}, value - {value}.')],
                ['' => ['Property - value, value - 2008:fa::1.']],
            ],
            'custom IPv6 not allowed message with parameters, property set' => [
                ['data' => '2008:fa::1'],
                [
                    'data' => new Ip(
                        allowIpv6: false,
                        ipv6NotAllowedMessage: 'Property - {property}, value - {value}.',
                    ),
                ],
                ['data' => ['Property - data, value - 2008:fa::1.']],
            ],

            ['!2008:fa::0:1/0', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['2008:fz::0/129', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['192.168.5.32/33', [new Ip(requireSubnet: true)], ['' => [$wrongCidrMessage]]],
            ['192.168.5.32/af', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['192.168.5.32/11/12', [new Ip(requireSubnet: true)], ['' => [$message]]],
            ['2008:db0::1', [new Ip(requireSubnet: true)], ['' => [$noSubnetMessage]]],
            ['10.0.0.1', [new Ip(requireSubnet: true)], ['' => [$noSubnetMessage]]],
            ['!!192.168.5.32/32', [new Ip(requireSubnet: true, allowNegation: true)], ['' => [$message]]],
            ['!!2008:fa::0:1/64', [new Ip(requireSubnet: true, allowNegation: true)], ['' => [$message]]],

            ['192.5.1.1', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            ['10.0.3.2', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            ['10.0.1.2', [new Ip(ranges: ['!10.0.1.0/24', '10.0.0.0/8', 'localhost'])], ['' => [$notInRangeMessage]]],
            [
                '10.2.2.2',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])],
                ['' => [$notInRangeMessage]],
            ],
            [
                '10.0.1.1/22',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '!10.0.0.0/8', 'localhost'])],
                ['' => [$notInRangeMessage]],
            ],
            ['2001:db0:1:2::7', [new Ip(ranges: ['2001:db0:1:1::/64'])], ['' => [$notInRangeMessage]]],
            [
                '2001:db0:1:2::7',
                [new Ip(ranges: ['!2001:db0::/32', '2001:db0:1:2::/64'])],
                ['' => [$notInRangeMessage]],
            ],

            ['192.5.1.1', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            ['2001:db0:1:2::7', [new Ip(ranges: ['10.0.1.0/24'])], ['' => [$notInRangeMessage]]],
            [
                '10.0.3.2',
                [new Ip(ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', '127.0.0.1'])],
                ['' => [$notInRangeMessage]],
            ],
            ['127.0.0.1', [new Ip(ranges: ['!system', 'any'])], ['' => [$notInRangeMessage]]],
            ['fe80::face', [new Ip(ranges: ['!system', 'any'])], ['' => [$notInRangeMessage]]],

            [
                '10.2.2.2',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
                ['' => [$notInRangeMessage]],
            ],
            [
                '10.0.1.1/22',
                [new Ip(allowSubnet: true, ranges: ['10.0.1.0/24', '2001:db0:1:2::/64', 'localhost', '!any'])],
                ['' => [$notInRangeMessage]],
            ],

            ['01.01.01.01', [new Ip()], ['' => [$message]]],
            ['010.010.010.010', [new Ip()], ['' => [$message]]],
            ['001.001.001.001', [new Ip()], ['' => [$message]]],

            'custom message' => [
                '192.168.5.32/af',
                [new Ip(allowSubnet: true, message: 'Custom message.')],
                ['' => ['Custom message.']],
            ],
            'custom message with parameters' => [
                '192.168.5.32/af',
                [new Ip(allowSubnet: true, message: 'Property - {Property}, value - {value}.')],
                ['' => ['Property - Value, value - 192.168.5.32/af.']],
            ],
            'custom message with parameters, property set' => [
                ['data' => '192.168.5.32/af'],
                ['data' => new Ip(allowSubnet: true, message: 'Property - {property}, value - {value}.')],
                ['data' => ['Property - data, value - 192.168.5.32/af.']],
            ],
        ];
    }

    public function testNetworkAliasException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Network alias "*" already set as default');
        new Ip(networks: ['*' => ['wrong']], ranges: ['*']);
    }

    public static function dataRangesForSubstitution(): array
    {
        return [
            'ipv4' => [['10.0.0.1'], ['10.0.0.1']],
            'any' => [['192.168.0.32', 'fa::/32', 'any'], ['192.168.0.32', 'fa::/32', '0.0.0.0/0', '::/0']],
            'ipv4+!private' => [
                ['10.0.0.1', '!private'],
                ['10.0.0.1', '!10.0.0.0/8', '!172.16.0.0/12', '!192.168.0.0/16', '!fd00::/8'],
            ],
            'private+!system' => [
                ['private', '!system'],
                [
                    '10.0.0.0/8',
                    '172.16.0.0/12',
                    '192.168.0.0/16',
                    'fd00::/8',
                    '!224.0.0.0/4',
                    '!ff00::/8',
                    '!169.254.0.0/16',
                    '!fe80::/10',
                    '!127.0.0.0/8',
                    '!::1',
                    '!192.0.2.0/24',
                    '!198.51.100.0/24',
                    '!203.0.113.0/24',
                    '!2001:db8::/32',
                ],
            ],
            'containing duplicates' => [
                ['10.0.0.1', '10.0.0.2', '10.0.0.2', '10.0.0.3'],
                ['10.0.0.1', '10.0.0.2', 3 => '10.0.0.3'],
            ],
        ];
    }

    /**
     * @param string[] $ranges
     * @param string[] $expectedRanges
     */
    #[DataProvider('dataRangesForSubstitution')]
    public function testRangesForSubstitution(array $ranges, array $expectedRanges): void
    {
        $rule = new Ip(ranges: $ranges);
        $this->assertSame($expectedRanges, $rule->getRanges());
    }

    public function testDisableBothIpv4AndIpv6(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Both IPv4 and IPv6 checks can not be disabled at the same time');
        new Ip(allowIpv4: false, allowIpv6: false);
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Ip(), new Ip(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Ip(), new Ip(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Ip::class, IpHandler::class];
    }
}
