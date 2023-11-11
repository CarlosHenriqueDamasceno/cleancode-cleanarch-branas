<?php

declare(strict_types=1);

namespace Feature;

use App\GetAccount\GetAccount;
use App\Signup\Signup;
use App\Signup\SignupInput;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;


final class SignupTest extends TestCase
{
    public static function cpfProvider(): array
    {
        return [
            ["97456321558"],
            ["71428793860"],
            ["87748248800"]
        ];
    }

    #[Test]
    #[DataProvider('cpfProvider')]
    public function shouldCreatePassengersAccount(string $cpf): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: $cpf,
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup();
        $getAccount = new GetAccount();
        $outputSignup = $signup->execute($input);
        $outputGetAccount = $getAccount->execute($outputSignup->accountId);
        $this->assertEquals($input->name, $outputGetAccount->name);
        $this->assertEquals($input->email, $outputGetAccount->email);
    }
}