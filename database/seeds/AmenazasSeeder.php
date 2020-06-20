<?php

use Illuminate\Database\Seeder;

class AmenazasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                $faker = Faker\Factory::create();

        for($i=0;$i<12;$i++) {
            DB::table('Amenazas')->insert([
                'nombre_amenaza' => $faker->randomElement(array('Datos de población','Erupción Volcanica',
                								'Incendio Forestal','Remoción en Masa','Sistema Frontal',
                								'Teremotos','Infraestructura Critica')),
                'variable_amenaza' => $faker->paragraph,
                'id_amenaza' => $faker->numberBetween(1,12),
                'created_at' => date("Y-m-d H:i:s"),
        		'updated_at' => date("Y-m-d H:i:s")
            ]);
        }
    }
}
