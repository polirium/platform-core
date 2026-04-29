<?php

namespace Polirium\Core\Base\Commands;

use Exception;
use Illuminate\Console\Command;
use Polirium\Core\Base\Commands\Traits\ValidateCommandInput;
use Polirium\Core\Base\Http\Models\User;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('poli:user:create', 'Create a super user')]
class UserCreateCommand extends Command
{
    use ValidateCommandInput;

    public function handle(): int
    {
        $this->components->info('Creating a super user...');

        try {
            $user = new User();
            $user->first_name = $this->askWithValidate('Enter first name', 'required|min:2|max:60');
            $user->last_name = $this->askWithValidate('Enter last name', 'required|min:2|max:60');
            $user->email = $this->askWithValidate('Enter email address', 'required|email|unique:users,email');
            $user->username = $this->askWithValidate('Enter username', 'required|min:4|max:60|unique:users,username');
            $user->password = $this->askWithValidate('Enter password', 'required|min:6|max:60', true);
            $user->super_admin = 1;
            $user->email_verified_at = now();
            $user->save();

            $this->components->info('Super user is created.');

            return self::SUCCESS;
        } catch (Exception $exception) {
            $this->components->error('User could not be created.');
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
