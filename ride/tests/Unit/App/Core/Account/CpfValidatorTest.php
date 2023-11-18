<?php

declare(strict_types=1);

namespace Tests\Unit\App\Core\Account;

use App\Core\Account\CpfValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CpfValidatorTest extends TestCase
{
    public static function cpfProvider(): array
    {
        return [
            ["97456321558"],
            ["71428793860"],
            ["87748248800"]
        ];
    }

    public static function invalidCpfProvider(): array
    {
        return [
            [""],
            ["11111111111"],
            ["111"],
            ["11111111111111"]
        ];
    }

    #[Test]
    #[DataProvider('cpfProvider')]
    public function shouldBeValidCpf(string $cpf): void
    {
        $this->assertTrue(CpfValidator::validate($cpf));
    }

    #[Test]
    #[DataProvider('invalidCpfProvider')]
    public function shouldBeInvalidCpf(string $cpf): void
    {
        $this->assertFalse(CpfValidator::validate($cpf));
    }
}
