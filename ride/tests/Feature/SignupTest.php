<?php

namespace Feature;

use App\Signup;
use App\SignupInput;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;


class SignupTest extends TestCase
{
    public static function cpfProvider(): array
    {
        return [
            "97456321558",
            "71428793860",
            "87748248800"
        ];
    }

    #[DataProvider('cpfProvider')]
    public function testShouldCreatePassengersAccount(string $cpf)
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
        $outputSignup = $signup->execute($input);
        $this->assertEquals($input->name, $outputSignup->name);
        $this->assertEquals($input->email, $outputSignup->email);
    }
}