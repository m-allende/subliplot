<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Region;
use App\Models\Commune;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CommunesSeeder extends Seeder
{
    public function run(): void
    {
        $country = Country::firstWhere('iso2', 'CL');
        if (!$country) {
            $this->command?->warn('CommunesSeeder: Chile no existe. Ejecuta CountriesSeeder primero.');
            return;
        }

        // Si ya existen comunas para cualquier región de Chile, saltamos
        $regionIds = Region::where('country_id', $country->id)->pluck('id');
        if ($regionIds->isEmpty()) {
            $this->command?->warn('CommunesSeeder: No hay regiones para CL. Ejecuta RegionsSeeder primero.');
            return;
        }

        if (Commune::whereIn('region_id', $regionIds)->exists()) {
            $this->command?->info('CommunesSeeder: Comunas para CL ya existen. Saltando…');
            return;
        }

        $now = Carbon::now();

        // Mapa: Región => [comunas...]
        $data = [
            // XV
            'Arica y Parinacota' => [
                'Arica','Camarones','Putre','General Lagos',
            ],

            // I
            'Tarapacá' => [
                'Iquique','Alto Hospicio','Pozo Almonte','Camiña','Colchane','Huara','Pica',
            ],

            // II
            'Antofagasta' => [
                'Antofagasta','Mejillones','Sierra Gorda','Taltal',
                'Calama','Ollagüe','San Pedro de Atacama',
                'Tocopilla','María Elena',
            ],

            // III
            'Atacama' => [
                'Copiapó','Caldera','Tierra Amarilla',
                'Chañaral','Diego de Almagro',
                'Vallenar','Alto del Carmen','Freirina','Huasco',
            ],

            // IV
            'Coquimbo' => [
                'La Serena','Coquimbo','Andacollo','La Higuera','Paihuano','Vicuña',
                'Illapel','Canela','Los Vilos','Salamanca',
                'Ovalle','Combarbalá','Monte Patria','Punitaqui','Río Hurtado',
            ],

            // V
            'Valparaíso' => [
                'Valparaíso','Casablanca','Concón','Juan Fernández','Puchuncaví','Quintero',
                'Viña del Mar','Quilpué','Villa Alemana','Limache',
                'Quillota','La Calera','Hijuelas','La Cruz','Nogales',
                'San Antonio','Algarrobo','Cartagena','El Quisco','El Tabo','Santo Domingo',
                'San Felipe','Catemu','Llaillay','Panquehue','Putaendo','Santa María',
                'Los Andes','Calle Larga','Rinconada','San Esteban',
                'Isla de Pascua',
            ],

            // RM
            'Región Metropolitana de Santiago' => [
                'Santiago','Cerrillos','Cerro Navia','Conchalí','El Bosque','Estación Central',
                'Huechuraba','Independencia','La Cisterna','La Florida','La Granja','La Pintana',
                'La Reina','Las Condes','Lo Barnechea','Lo Espejo','Lo Prado','Macul','Maipú',
                'Ñuñoa','Pedro Aguirre Cerda','Peñalolén','Providencia','Pudahuel','Quilicura',
                'Quinta Normal','Recoleta','Renca','San Joaquín','San Miguel','San Ramón','Vitacura',
                'Puente Alto','Pirque','San José de Maipo',
                'San Bernardo','Buin','Calera de Tango','Paine',
                'Melipilla','Alhué','Curacaví','María Pinto','San Pedro',
                'Talagante','El Monte','Isla de Maipo','Padre Hurtado','Peñaflor',
                'Colina','Lampa','Tiltil',
            ],

            // VI
            "Libertador General Bernardo O'Higgins" => [
                'Rancagua','Codegua','Coinco','Coltauco','Doñihue','Graneros','Las Cabras','Machalí',
                'Malloa','Mostazal','Olivar','Peumo','Pichidegua','Quinta de Tilcoco','Rengo','Requínoa',
                'San Vicente',
                'Pichilemu','La Estrella','Litueche','Marchigüe','Navidad','Paredones',
                'San Fernando','Chépica','Chimbarongo','Lolol','Nancagua','Palmilla','Peralillo','Placilla','Pumanque','Santa Cruz',
            ],

            // VII
            'Maule' => [
                'Talca','Constitución','Curepto','Empedrado','Maule','Pelarco','Pencahue','Río Claro','San Clemente',
                'Linares','Colbún','Longaví','Parral','San Javier','Villa Alegre','Yerbas Buenas',
                'Cauquenes','Chanco','Pelluhue',
                'Curicó','Hualañé','Licantén','Molina','Rauco','Romeral','Sagrada Familia','Teno','Vichuquén',
            ],

            // XVI
            'Ñuble' => [
                'Chillán','Chillán Viejo',
                'Bulnes','Quillón','San Ignacio','El Carmen','Pemuco','Yungay',
                'Portezuelo','Ránquil','Coelemu','Trehuaco',
                'San Carlos','Ñiquén','San Fabián','Coihueco','San Nicolás',
            ],

            // VIII
            'Biobío' => [
                // Concepción
                'Concepción','Coronel','Chiguayante','Florida','Hualpén','Hualqui','Lota','Penco',
                'San Pedro de la Paz','Santa Juana','Talcahuano','Tomé',
                // Biobío
                'Cabrero','Laja','Los Ángeles','Mulchén','Nacimiento','Negrete','Quilaco','Quilleco',
                'San Rosendo','Santa Bárbara','Tucapel','Yumbel','Alto Biobío',
                // Arauco
                'Arauco','Cañete','Contulmo','Curanilahue','Lebu','Los Álamos','Tirúa',
            ],

            // IX
            'La Araucanía' => [
                // Cautín
                'Temuco','Carahue','Cholchol','Cunco','Curarrehue','Freire','Galvarino','Gorbea','Lautaro',
                'Loncoche','Melipeuco','Nueva Imperial','Padre Las Casas','Perquenco','Pitrufquén','Pucón',
                'Saavedra','Teodoro Schmidt','Toltén','Vilcún','Villarrica',
                // Malleco
                'Angol','Collipulli','Curacautín','Ercilla','Lonquimay','Los Sauces','Lumaco','Purén',
                'Renaico','Traiguén','Victoria',
            ],

            // XIV
            'Los Ríos' => [
                'Valdivia','Corral','Lanco','Los Lagos','Máfil','Mariquina','Paillaco','Panguipulli',
                'La Unión','Futrono','Lago Ranco','Río Bueno',
            ],

            // X
            'Los Lagos' => [
                // Llanquihue
                'Puerto Montt','Calbuco','Cochamó','Maullín','Puerto Varas','Frutillar','Fresia','Llanquihue','Los Muermos',
                // Osorno
                'Osorno','Puerto Octay','Purranque','Puyehue','Río Negro','San Juan de la Costa','San Pablo',
                // Chiloé
                'Ancud','Castro','Chonchi','Curaco de Vélez','Dalcahue','Puqueldón','Queilén','Quellón','Quemchi','Quinchao',
                // Palena
                'Hualaihué','Chaitén','Futaleufú','Palena',
            ],

            // XI
            'Aysén del General Carlos Ibáñez del Campo' => [
                'Coyhaique','Lago Verde',
                'Aysén','Cisnes','Guaitecas',
                'Cochrane',"O'Higgins",'Tortel',
                'Chile Chico','Río Ibáñez',
            ],

            // XII
            'Magallanes y de la Antártica Chilena' => [
                'Punta Arenas','Laguna Blanca','Río Verde','San Gregorio',
                'Cabo de Hornos','Antártica',
                'Porvenir','Primavera','Timaukel',
                'Natales','Torres del Paine',
            ],
        ];

        foreach ($data as $regionName => $communes) {
            $region = Region::where('country_id', $country->id)
                            ->where('name', $regionName)->first();

            if (!$region) {
                $this->command?->warn("CommunesSeeder: Región no encontrada: {$regionName}. Omitiendo…");
                continue;
            }

            $rows = array_map(fn($name) => [
                'region_id'  => $region->id,
                'name'       => $name,
                'code'       => null,
                'created_at' => $now,
                'updated_at' => $now,
            ], $communes);

            // Inserta por bloques (por si hay muchas)
            foreach (array_chunk($rows, 100) as $chunk) {
                Commune::insert($chunk);
            }
        }

        $this->command?->info('CommunesSeeder: Comunas de Chile insertadas.');
    }
}
