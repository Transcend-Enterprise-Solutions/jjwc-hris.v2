<?php

namespace Database\Seeders;

use App\Models\Ethnicity;
use Illuminate\Database\Seeder;

class EthnicitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ethnicities = [
            'Agta', 'Abaknon', 'Agutaynon', 'Aklanon', 'Alangan', 'Alta',
            'Amerasian', 'Ati', 'Atta', 'Ayta (Aeta)', 'B’laan', 'Badjao',
            'Bagobo', 'Balangao', 'Balangingi', 'Bangon', 'Bantoanon',
            'Banwaon', 'Batak', 'Bicolano', 'Binukid', 'Boholano', 'Bolinao',
            'Bontoc', 'Buhid', 'Butuanon', 'Caluyanon', 'Capiznon', 'Caviteño',
            'Cebuano', 'Cotabateño', 'Cuyonon', 'Chinese Filipinos',
            'Davaoeño', 'Ermiteño', 'Ga’dang', 'Gaddang', 'Hanunoo',
            'Higaonon', 'Ibaloi', 'Ibanag', 'Ifugao', 'Ikalahan', 'Illanun',
            'Ilocano', 'Ilonggo', 'Ilongot', 'Indian Filipinos', 'Inonhan',
            'Iraya', 'Isinai', 'Isneg', 'Itneg', 'Ivatan', 'Japanese Filipinos',
            'Kagayanen', 'Kalagan', 'Kalinga', 'Kamayo', 'Kankanaey',
            'Kapampangan', 'Karao', 'Kasiguranin', 'Kinamiguin', 'Kolibugan',
            'Kinaray-a', 'Korean Filipinos', 'Magahat', 'Maguindanaon',
            'Malaweg', 'Mamanwa', 'Mandaya', 'Mansaka', 'Manguwangan',
            'Manobo', 'Matigsalug', 'Maranao', 'Masbateño', 'Molbog', 'Negrense',
            'Palawano', 'Pangasinense', 'Paranan', 'Porohanon', 'Ratagnon',
            'Romblomanon', 'Sama', 'Sambal', 'Sangil', 'Spanish Filipinos',
            'Subanun', 'Sulod', 'Surigaonon', 'T’boli', 'Tadyawan', 'Tagabawa',
            'Tagakaulo', 'Tagalog', 'Tagbanwa', 'Talaandig', 'Tasaday',
            'Tau’t Bato', 'Tausug', 'Tawbuid', 'Ternateño', 'Tiruray', 'Waray',
            'Yakan', 'Yogad', 'Zamboangueño'
        ];

        foreach ($ethnicities as $name) {
            Ethnicity::firstOrCreate(['name' => $name]);
        }
    }
}
