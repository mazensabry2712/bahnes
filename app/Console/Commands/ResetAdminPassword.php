<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:password-reset {email : Email address of the existing administrator}';

    protected $description = 'Reset an existing administrator password from the command line';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));

        $admin = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('is_admin', true)
            ->first();

        if (! $admin) {
            $this->error('No administrator account was found for that email. No changes were made.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('New password (minimum 12 characters)');

        if (mb_strlen($password) < 12) {
            $this->error('The password must be at least 12 characters long.');

            return self::FAILURE;
        }

        $confirmation = (string) $this->secret('Confirm new password');

        if (! hash_equals($password, $confirmation)) {
            $this->error('The passwords do not match. No changes were made.');

            return self::FAILURE;
        }

        $admin->forceFill(['password' => $password])->save();

        $this->info("Administrator password reset for {$email}.");

        return self::SUCCESS;
    }
}
