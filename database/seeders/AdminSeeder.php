<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = User::create([
            'name' => 'Efqan',
            'email' => 'efganesc@mail.ru',
            'password' => Hash::make('efgan1997'),
            "is_admin" => 1
        ]);

        $operations = [
            "cake view",
            "cake edit",
            "cake delete",
            "cakes view",
            "cake create",

            "category view",
            "category edit",
            "category delete",
            "categories view",
            "category create",

            "users view",
            "user edit",
            "user delete",
            "user view",
            "user create",

            "operation edit",
            "operation delete",
            "operations view",
            "operation create",

            "users view",
            "user store",
            "user delete",
            "user view",
            "user update",

            "orders view"


        ];

        foreach ($operations as $operation) {

            $user->operations()
                ->create([
                    "name" => $operation
                ]);

        }
    }
}
