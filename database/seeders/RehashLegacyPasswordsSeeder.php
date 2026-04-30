<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Rehash password MD5 legacy di tabel users menjadi bcrypt.
 *
 * Deteksi MD5: string 32 karakter hexadecimal (tidak diawali '$2y$').
 * Password default pengganti: 'password123' (harus segera diganti oleh user).
 */
class RehashLegacyPasswordsSeeder extends Seeder
{
    /**
     * Password default untuk menggantikan hash MD5.
     * User yang terkena harus segera mengganti password mereka.
     */
    private const DEFAULT_PASSWORD = 'password123';

    public function run(): void
    {
        $users = DB::table('users')->get();
        $rehashed = 0;

        foreach ($users as $user) {
            // Skip jika sudah bcrypt ($2y$ atau $2a$ prefix)
            if (str_starts_with($user->password, '$2y$') || str_starts_with($user->password, '$2a$')) {
                $this->command->info("  ✓ User '{$user->username}' (id:{$user->id_user}) — sudah bcrypt, skip.");
                continue;
            }

            // Deteksi MD5: exactly 32 hex characters
            if (preg_match('/^[a-f0-9]{32}$/i', $user->password)) {
                DB::table('users')
                    ->where('id_user', $user->id_user)
                    ->update([
                        'password' => Hash::make(self::DEFAULT_PASSWORD),
                    ]);

                $rehashed++;
                $this->command->warn(
                    "  ⚠ User '{$user->username}' (id:{$user->id_user}, role:{$user->role}) " .
                    "— MD5 diganti bcrypt. Password default: '" . self::DEFAULT_PASSWORD . "'"
                );
            } else {
                $this->command->error(
                    "  ✗ User '{$user->username}' (id:{$user->id_user}) — format password tidak dikenali, skip."
                );
            }
        }

        $this->command->newLine();

        if ($rehashed > 0) {
            $this->command->info("Selesai! {$rehashed} user berhasil di-rehash ke bcrypt.");
            $this->command->warn("⚠ PENTING: Beritahu user yang terkena untuk mengganti password mereka segera.");
        } else {
            $this->command->info("Tidak ada password MD5 yang ditemukan. Semua user sudah menggunakan bcrypt.");
        }
    }
}
