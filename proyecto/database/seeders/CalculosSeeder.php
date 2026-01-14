<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Calculos;

class CalculosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Primer cálculo predeterminado
        Calculos::create([
            'nombre_calculo' => 'Vivienda con 3 Cuartos, 2 Baños, Cocina, Sala.',
            'contenido' => '- Alambre galvanizado CAL=18 (100.00 M/KG) = 6 ROLLOS
- Arena lavada = 10 M3
- Batea gravilla 0.82 X 0.5 X 0.18 M. ART 104-A = 1 PZA
- Bloque de concreto 15 X 20 X 40 CM PMVP DECRETO = 1710 PZA
- Breaker THQL 1 X 20 AMP G-E (ENCH) 120/240 = 4 PZA
- Cabilla estriada D=3/8" (4200 KG/CM2) X 6 MTS = 10 UND
- Cable TW 12 AWG = 2 ROLLOS
- Cajetín PVC octogonal 4" = 9 PZA
- Cajetín PVC rectangular 2" X 4" X 1/2"/ 1-1/2" = 17 PZA
- Cal hidratada = 6 SACOS
- Cemento blanco 21.25 KGS = 1 SACO
- Cemento gris portland TIPO I 42.5 KG-PMVP DECRETO = 85 SACOS
- Cerámica antideslizante nacional (20 CM X 20 CM) = 3 M2
- Cerradura pomo TºCON/NAT C/LLAVE = 1 PZA
- Codo PVC 90 grados A.B D=1/2" = 15 PZA
- Codo PVC 90 grados A.B D=3/4" = 3 PZA
- Codo PVC 90 grados A.N. D=2" = 8 PZA
- Codo PVC 90 grados A.N. D=4" = 4 PZA
- Correa simple 2" X 1" ACERO X 12 MTS = 8 PZA
- Corte de vidrio para macuto E= 4 MM = 130 PZA
- Curva PVC 3/4" E = 38 PZA
- Desagüe sencillo plástico 1 1/4" = 1 UND
- Fondo antióxidos gris = 4 GLN
- Ganchos para sostener lamina acerolit = 2 CAJAS
- Interruptor sencillo para empotrar = 8 PZA
- Cerchas 10 cm x 6 mts/rejas, marcos, puerta metalica (2), correas (20) = 1 SG
- Lavamanos p/colgar color claro una llave = 1 PZA
- Llave bola cromada 1/2" = 1 PZA
- Llave p/ducha = 1 UND
- Llave p/lavamanos crom. pomo cruz = 1 PZA
- Lamina climatizada 0.8 X 3.5 M = 10 LAMIN
- Lamina climatizada 0.8 X 6 M = 10 LAMIN
- Malla electrosoldada 4" X 4" X 120 M TRUCSON = 1 ROLLO
- Pegamento tubo PVC (PAVCO O SIMILAR) = 1 GLN
- Pego ceramica blanco = 10 SACOS
- Piedra picada T.MAX = 1" = 7 M3
- Pintura de caucho = 3 CUN
- Poceta wc blanco c/asiento (altima) = 1 UND
- Puerta madera entamborada 0.70 X 2.10 M = 1 PZA
- Regadera para ducha PVC c/brazo 4" = 1 PZA
- Rejilla bronce 2" = 4 PZA
- Sifones PVC A.N. 1 1/4" p/lavamanos = 1 PZA
- Sócate plástico = 9 PZA
- Tablero residencial, 4 CIRCUITOS 125 A. BIEMCA= 1 PZA
- Tanque piroceta cierraje blanco = 1 UND
- Tapa AB PVC D=1/2" = 9 PZA
- Tapa lisa A.N PVC D=4" = 2 PZA
- Tee PVC A.B D=1/2" X 1/2" = 9 PZA
- Tee PVC D=3/4"X 3/4" A.B = 4 PZA
- Tomacorriente universal 120B DOBLE = 9 PZA
- Tubo para viga 4" X 2" ACERO X 12 MTS = 6 PZA
- Tubo PVC E.E D= 1/2" PRESION AGUA FRIA ASTIM L=6 PESADO = 3 PZA
- Tubo PVC E.E D= 3/4" PRESION AGUA FRIA ASTIM L=6 PESADO = 5 PZA
- Tubo DE PVC DIAM = 51 MM (2") E= 1.8 MM X 3 M AN = 8 PZA
- TUBO DE PVC DIAM = 110 MM (4") E= 2.2 MM AN X 3 M = 7 PZA
- TUBO PVC 3/4 PULG. PARA UE. X 3 M = 38 PZA
- YEE A.N. P.V.C. 2" X 2" X 45º = 7 PZA
- YEE PVC A.N. 4" X 4" = 4 PZA',
        ]);

        // Segundo cálculo predeterminado
        Calculos::create([
            'nombre_calculo' => 'Vivienda con 1 Cuarto, 1 baño, Cocina, Sala',
            'contenido' => '- Alambre galvanizado CAL=18 (100.00 M/KG) = 3 ROLLOS
- Arena lavada = 5 M3
- Batea gravilla 0.82 X 0.5 X 0.18 M. ART 104-A = 1 PZA
- Bloque de concreto 15 X 20 X 40 CM PMVP DECRETO = 855 PZA
- Breaker THQL 1 X 20 AMP G-E (ENCH) 120/240 = 2 PZA
- Cabilla estriada D=3/8" (4200 KG/CM2) X 6 MTS = 5 UND
- Cable TW 12 AWG = 1 ROLLO
- Cajetín PVC octogonal 4" = 5 PZA
- Cajetín PVC rectangular 2" X 4" X 1/2"/ 1-1/2" = 9 PZA
- Cal hidratada = 3 SACOS
- Cemento blanco 21.25 KGS = 1 SACO
- Cemento gris portland TIPO I 42.5 KG-PMVP DECRETO = 43 SACOS
- Cerámica antideslizante nacional (20 CM X 20 CM) = 2 M2
- Cerradura pomo TºCON/NAT C/LLAVE = 1 PZA
- Codo PVC 90 grados A.B D=1/2" = 8 PZA
- Codo PVC 90 grados A.B D=3/4" = 2 PZA
- Codo PVC 90 grados A.N. D=2" = 4 PZA
- Codo PVC 90 grados A.N. D=4" = 2 PZA
- Correa simple 2" X 1" ACERO X 12 MTS = 4 PZA
- Corte de vidrio para macuto E= 4 MM = 65 PZA
- Curva PVC 3/4" E = 19 PZA
- Desagüe sencillo plástico 1 1/4" = 1 UND
- Fondo antióxidos gris = 2 GLN
- Ganchos para sostener lamina acerolit = 1 CAJA
- Interruptor sencillo para empotrar = 4 PZA
- Cerchas 10 cm x 6 mts/rejas, marcos, puerta metalica (2), correas (20) = 1 SG
- Lavamanos p/colgar color claro una llave = 1 PZA
- Llave bola cromada 1/2" = 1 PZA
- Llave p/ducha = 1 UND
- Llave p/lavamanos crom. pomo cruz = 1 PZA
- Lamina climatizada 0.8 X 3.5 M = 5 LAMIN
- Lamina climatizada 0.8 X 6 M = 5 LAMIN
- Malla electrosoldada 4" X 4" X 120 M TRUCSON = 1 ROLLO
- Pegamento tubo PVC (PAVCO O SIMILAR) = 1 GLN
- Pego ceramica blanco = 5 SACOS
- Piedra picada T.MAX = 1" = 4 M3
- Pintura de caucho = 2 CUN
- Poceta c blanco c/asiento (altima) = 1 UND
- Puerta madera entamborada 0.70 X 2.10 M = 1 PZA
- Regadera para ducha PVC c/brazo 4" = 1 PZA
- Rejilla bronce 2" = 2 PZA
- Sifones PVC A.N. 1 1/4" p/lavamanos = 1 PZA
- Sócate plástico = 5 PZA
- Tablero residencial, 4 CIRCUITOS 125 A. BIEMCA= 1 PZA
- Tanque piroceta cierraje blanco = 1 UND
- Tapa AB PVC D=1/2" = 5 PZA
- Tapa lisa A.N PVC D=4" = 1 PZA
- Tee PVC A.B D=1/2" X 1/2" = 5 PZA
- Tee PVC D=3/4"X 3/4" A.B = 2 PZA
- Tomacorriente universal 120B DOBLE = 5 PZA
- Tubo para viga 4" X 2" ACERO X 12 MTS = 3 PZA
- Tubo PVC E.E D= 1/2" PRESION AGUA FRIA ASTIM L=6 PESADO = 2 PZA
- Tubo PVC E.E D= 3/4" PRESION AGUA FRIA ASTIM L=6 PESADO = 3 PZA
- Tubo DE PVC DIAM = 51 MM (2") E= 1.8 MM X 3 M AN = 4 PZA
- TUBO DE PVC DIAM = 110 MM (4") E= 2.2 MM AN X 3 M = 4 PZA
- TUBO PVC 3/4 PULG. PARA UE. X 3 M = 19 PZA
- YEE A.N. P.V.C. 2" X 2" X 45º = 4 PZA
- YEE PVC A.N. 4" X 4" = 2 PZA',
        ]);

        Calculos::create([
            'nombre_calculo' => 'Vivienda con 2 Cuartos, 1 Baño, Cocina, Sala.',
            'contenido' => '- Alambre galvanizado CAL=18 (100.00 M/KG) = 2 ROLLOS
- Arena lavada = 3 M3
- Batea gravilla 0.82 X 0.5 X 0.18 M. ART 104-A = 1 PZA
- Bloque de concreto 15 X 20 X 40 CM PMVP DECRETO = 428 PZA
- Breaker THQL 1 X 20 AMP G-E (ENCH) 120/240 = 1 PZA
- Cabilla estriada D=3/8" (4200 KG/CM2) X 6 MTS = 3 UND
- Cable TW 12 AWG = 1 ROLLO
- Cajetín PVC octogonal 4" = 3 PZA
- Cajetín PVC rectangular 2" X 4" X 1/2"/ 1-1/2" = 5 PZA
- Cal hidratada = 2 SACOS
- Cemento blanco 21.25 KGS = 1 SACO
- Cemento gris portland TIPO I 42.5 KG-PMVP DECRETO = 22 SACOS
- Cerámica antideslizante nacional (20 CM X 20 CM) = 1 M2
- Cerradura pomo TºCON/NAT C/LLAVE = 1 PZA
- Codo PVC 90 grados A.B D=1/2" = 4 PZA
- Codo PVC 90 grados A.B D=3/4" = 1 PZA
- Codo PVC 90 grados A.N. D=2" = 2 PZA
- Codo PVC 90 grados A.N. D=4" = 1 PZA
- Correa simple 2" X 1" ACERO X 12 MTS = 2 PZA
- Corte de vidrio para macuto E= 4 MM = 33 PZA
- Curva PVC 3/4" E = 10 PZA
- Desagüe sencillo plástico 1 1/4" = 1 UND
- Fondo antióxidos gris = 1 GLN
- Ganchos para sostener lamina acerolit = 1 CAJA
- Interruptor sencillo para empotrar = 2 PZA
- Cerchas 10 cm x 6 mts/rejas, marcos, puerta metalica (2), correas (20) = 1 SG
- Lavamanos p/colgar color claro una llave = 1 PZA
- Llave bola cromada 1/2" = 1 PZA
- Llave p/ducha = 1 UND
- Llave p/lavamanos crom. pomo cruz = 1 PZA
- Lamina climatizada 0.8 X 3.5 M = 3 LAMIN
- Lamina climatizada 0.8 X 6 M = 3 LAMIN
- Malla electrosoldada 4" X 4" X 120 M TRUCSON = 1 ROLLO
- Pegamento tubo PVC (PAVCO O SIMILAR) = 1 GLN
- Pego ceramica blanco = 3 SACOS
- Piedra picada T.MAX = 1" = 2 M3
- Pintura de caucho = 1 CUN
- Poceta wc blanco c/asiento (altima) = 1 UND
- Puerta madera entamborada 0.70 X 2.10 M = 1 PZA
- Regadera para ducha PVC c/brazo 4" = 1 PZA
- Rejilla bronce 2" = 1 PZA
- Sifones PVC A.N. 1 1/4" p/lavamanos = 1 PZA
- Sócate plástico = 3 PZA
- Tablero residencial, 4 CIRCUITOS 125 A. BIEMCA= 1 PZA
- Tanque piroceta cierraje blanco = 1 UND
- Tapa AB PVC D=1/2" = 3 PZA
- Tapa lisa A.N PVC D=4" = 1 PZA
- Tee PVC A.B D=1/2" X 1/2" = 3 PZA
- Tee PVC D=3/4"X 3/4" A.B = 1 PZA
- Tomacorriente universal 120B DOBLE = 3 PZA
- Tubo para viga 4" X 2" ACERO X 12 MTS = 2 PZA
- Tubo PVC E.E D= 1/2" PRESION AGUA FRIA ASTIM L=6 PESADO = 1 PZA
- Tubo PVC E.E D= 3/4" PRESION AGUA FRIA ASTIM L=6 PESADO = 2 PZA
- Tubo DE PVC DIAM = 51 MM (2") E= 1.8 MM X 3 M AN = 2 PZA
- TUBO DE PVC DIAM = 110 MM (4") E= 2.2 MM AN X 3 M = 2 PZA
- TUBO PVC 3/4 PULG. PARA UE. X 3 M = 10 PZA
- YEE A.N. P.V.C. 2" X 2" X 45º = 2 PZA
- YEE PVC A.N. 4" X 4" = 1 PZA',
        ]);

    }
}