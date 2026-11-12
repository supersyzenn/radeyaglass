<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {--name= : Nama admin} {--email= : Email admin}';

    protected $description = 'Membuat akun admin melalui prompt kata sandi tersembunyi';

    public function handle(): int
    {
        $data = [
            'name' => $this->option('name') ?: $this->ask('Nama admin'),
            'email' => strtolower(trim($this->option('email') ?: $this->ask('Email admin'))),
        ];
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);
        if ($validator->fails()) {
            $this->error($validator->errors()->first());
            return self::FAILURE;
        }

        $data['password'] = $this->secret('Kata sandi (minimal 12 karakter)');
        $confirmation = $this->secret('Ulangi kata sandi');
        if (! is_string($data['password']) || strlen($data['password']) < 12 || $data['password'] !== $confirmation) {
            $this->error('Kata sandi harus minimal 12 karakter dan konfirmasi harus sama.');
            return self::FAILURE;
        }

        $data['password'] = Hash::make($data['password']);
        User::create($data);
        $this->info('Akun admin berhasil dibuat. Masuk melalui /admin/login.');

        return self::SUCCESS;
    }
}
