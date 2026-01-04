<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Pruebas unitarias para el registro de inspecciones
class StoreInspeccionTest extends TestCase
{
    use RefreshDatabase;

    // Configuramos la base de datos antes de cada prueba
    protected function setUp(): void
    {
        parent::setUp();
        DB::table('roles')->insert([
            ['id_rol' => 1, 'nombre_rol' => 'Administrador'],
            ['id_rol' => 2, 'nombre_rol' => 'Usuario'],
        ]);
    }

    // Prueba que un administrador puede registrar una inspección correctamente
    public function test_administrador_puede_registrar_inspeccion()
    {
        $admin = Usuario::factory()->create(['id_rol' => 1]);
        $this->actingAs($admin);

        $response = $this->post(route('inspecciones.store'), [
            'fecha' => Carbon::now()->format('Y-m-d'),
            'propietario_nombre' => 'Juan',
            'propietario_apellido' => 'Perez',
            'propietario_cedula' => '25126458', 
            'propietario_telefono' => '04121274597',
            'direccion' => 'Calle Sucre 123',
            'estado' => 1,
        ]);

        $response->assertStatus(302); // Redirección tras éxito
        $response->assertRedirect(route('inspecciones.index'));
    }

    // Prueba que la validación de cédula funciona correctamente
    public function test_validacion_cedula_invalida()
    {
        $admin = Usuario::factory()->create(['id_rol' => 1]);
        $this->actingAs($admin);

        $response = $this->post(route('inspecciones.store'), [
            'propietario_cedula' => '123456', // Falla regex
        ]);

        $response->assertSessionHasErrors(['propietario_cedula']);
    }

    // Prueba que un usuario no administrador no puede registrar una inspección
    public function test_usuario_no_admin_no_puede_registrar_inspeccion()
    {
        $usuario = Usuario::factory()->create(['id_rol' => 2]);
        $this->actingAs($usuario);

        // Atacamos la ruta 'create' ya que tu Policy está definida allí según tu código
        $response = $this->get(route('inspecciones.create'));

        $response->assertStatus(403);
    }
}