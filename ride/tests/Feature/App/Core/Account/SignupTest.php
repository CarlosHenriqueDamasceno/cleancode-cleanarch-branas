<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Account\Account;
use App\Core\Account\AccountDAO;
use App\Core\Account\AccountDAODatabase;
use PHPUnit\Framework\Attributes\Test;
use App\Core\Account\GetAccount\GetAccount;
use App\Core\Account\Signup\Signup;
use App\Core\Account\Signup\SignupInput;
use Mockery;
use Tests\TestCase;

final class SignupTest extends TestCase
{

    private AccountDAO $accountDAO;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountDAO = new AccountDAODatabase();
    }


    #[Test]
    public function shouldCreatePassengersAccount(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup($this->accountDAO);
        $getAccount = new GetAccount($this->accountDAO);
        $outputSignup = $signup->execute($input);
        $outputGetAccount = $getAccount->execute($outputSignup->accountId);
        $this->assertEquals($input->name, $outputGetAccount->name);
        $this->assertEquals($input->email, $outputGetAccount->email);
    }

    #[Test]
    public function shouldCreatePassengersAccountWithStub(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $accountDAOStub = Mockery::mock(AccountDAO::class);
        $accountDAOStub->shouldReceive("save")->andReturnUsing(fn ($param) => $param);
        $accountDAOStub->shouldReceive("getByEmail")->andReturn(null);
        $accountDAOStub->shouldReceive("getById")->andReturnUsing(
            fn ($id) => new Account(
                $id,
                $input->name,
                $input->email,
                $input->cpf,
                $input->password,
                $input->isPassenger,
                $input->isDriver,
                $input->carPlate
            )
        );
        $signup = new Signup($accountDAOStub);
        $getAccount = new GetAccount($accountDAOStub);
        $outputSignup = $signup->execute($input);
        $outputGetAccount = $getAccount->execute($outputSignup->accountId);
        $this->assertEquals($input->name, $outputGetAccount->name);
        $this->assertEquals($input->email, $outputGetAccount->email);
    }

    #[Test]
    public function shouldCreatePassengersAccountWithMock(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $accountDAOStub = Mockery::mock(AccountDAO::class);
        $accountDAOStub->shouldReceive("save")->withArgs(
            fn (Account $account) => $account->email === $input->email && $account->cpf === $input->cpf
        )->andReturnUsing(fn ($param) => $param)->once();
        $accountDAOStub->shouldReceive("getByEmail")->andReturn(null);
        $accountDAOStub->shouldReceive("getById")->andReturnUsing(
            fn ($id) => new Account(
                $id,
                $input->name,
                $input->email,
                $input->cpf,
                $input->password,
                $input->isPassenger,
                $input->isDriver,
                $input->carPlate
            )
        );
        $signup = new Signup($accountDAOStub);
        $getAccount = new GetAccount($accountDAOStub);
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
        $signup = new Signup($this->accountDAO);
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
        $signup = new Signup($this->accountDAO);
        $this->expectExceptionMessage("Invalid email");
        $signup->execute($input);
    }


    #[Test]
    public function shouldNotCreateAccountWithInvalidCpf(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "11111111111",
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup($this->accountDAO);
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
        $signup = new Signup($this->accountDAO);
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
        $signup = new Signup($this->accountDAO);
        $getAccount = new GetAccount($this->accountDAO);
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
        $signup = new Signup($this->accountDAO);
        $this->expectExceptionMessage("Invalid car plate");
        $signup->execute($input);
    }
}
