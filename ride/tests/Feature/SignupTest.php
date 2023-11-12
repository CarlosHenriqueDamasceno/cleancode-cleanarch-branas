<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ride\Account\GetAccount\GetAccount;
use Ride\Account\Signup\Signup;
use Ride\Account\Signup\SignupInput;


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

    #[Test]
    public function shouldNotCreateAccountWithInvalidName(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup();
        $this->expectExceptionMessage("Invalid name");
        $signup->execute($input);
    }

    #[Test]
    public function shouldNotCreateAccountWithInvalidEmail(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup();
        $this->expectExceptionMessage("Invalid email");
        $signup->execute($input);
    }


    #[Test]
    #[DataProvider("invalidCpfProvider")]
    public function shouldNotCreateAccountWithInvalidCpf(string $cpf): void
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
        $this->expectExceptionMessage("Invalid cpf");
        $signup->execute($input);
    }

    #[Test]
    public function shouldNotCreateAccountWithDuplicatedEmail(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup();
        $signup->execute($input);
        $this->expectExceptionMessage("Duplicated account");
        $signup->execute($input);
    }

    #[Test]
    public function shouldCreateDriverAccount(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: false,
            isDriver: true,
            carPlate: "AAA9999"
        );
        $signup = new Signup();
        $getAccount = new GetAccount();
        $outputSignup = $signup->execute($input);
        $outputGetAccount = $getAccount->execute($outputSignup->accountId);
        $this->assertEquals($input->name, $outputGetAccount->name);
        $this->assertEquals($input->email, $outputGetAccount->email);
    }

    #[Test]
    public function shouldNotCreateADriverAccountWithInvalidCarPlate(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: false,
            isDriver: true,
            carPlate: "AAA99A9"
        );
        $signup = new Signup();
        $this->expectExceptionMessage("Invalid car plate");
        $signup->execute($input);
    }
}