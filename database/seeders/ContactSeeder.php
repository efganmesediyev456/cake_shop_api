<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user=User::whereEmail("efganesc@mail.ru")->first();


        $operations = [
            "contacts view",
            "contact delete",
            "contact view"
        ];

        foreach ($operations as $operation) {

            $user->operations()
                ->create([
                    "name" => $operation
                ]);

        }
    }
}
