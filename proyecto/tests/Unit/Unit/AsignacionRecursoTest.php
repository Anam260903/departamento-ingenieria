<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Usuario;
use App\Models\recursos;
use App\Models\asignacion_recursos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;


// Pruebas unitarias para la asignación de recursos
class AsignacionRecursoTest extends TestCase
{
    use RefreshDatabase;

    // Configuramos la base de datos antes de cada prueba
    protected function setUp(): void
    {
        parent::setUp();
        // Insertar roles maestros en la DB de memoria
        DB::table('roles')->insert([
            ['id_rol' => 1, 'nombre_rol' => 'Administrador'],
            ['id_rol' => 2, 'nombre_rol' => 'Inspector'],
        ]);
    }

    
    // Prueba que solo el administrador pueda asignar recursos
    public function test_administrador_puede_asignar_recurso_exitosamente()
    {
        // Crear un usuario administrador, un técnico y un recurso
        $admin = Usuario::factory()->create(['id_rol' => 1]);
        $tecnico = Usuario::factory()->create(['nombre' => 'Carlos']);
        $recurso = recursos::factory()->create(['nombre_rec' => 'Metro']);

        $this->actingAs($admin);

        $response = $this->post(route('personal.assignRecurso', $tecnico->id_user), [
            'id_recurso' => $recurso->id_recurso
        ]);

        // Aserción de redirección y base de datos de asignación
        $response->assertStatus(302);
        $this->assertDatabaseHas('asignacion_recursos', [
            'id_user' => $tecnico->id_user,
            'id_recurso' => $recurso->id_recurso
        ]);

        // Lógica de notificacion
        $this->assertDatabaseHas('notificaciones', [
            'id_user' => $tecnico->id_user,
            'mensaje' => "Se te ha asignado el recurso 'Metro'. ¡Cuídalo bien!",
            'tipo' => 'recurso_asignado'
        ]);
    }

 
    // Prueba que un usuario no administrador es bloqueado
    public function test_usuario_no_admin_no_puede_asignar_recurso()
    {
        // 1. Arrange: Usuario con rol de Inspector
        $inspector = Usuario::factory()->create(['id_rol' => 2]);
        $tecnico = Usuario::factory()->create();
        $recurso = recursos::factory()->create();

        $this->actingAs($inspector);

        // 2. Act
        $response = $this->post(route('personal.assignRecurso', $tecnico->id_user), [
            'id_recurso' => $recurso->id_recurso
        ]);

        // 3. Assert: Debe devolver 403 Forbidden por la Policy
        $response->assertStatus(403);
    }

    
    // Prueba que no se permite asignar un recurso ya ocupado
    public function test_no_se_puede_asignar_recurso_ya_asignado()
    {
        $admin = Usuario::factory()->create(['id_rol' => 1]);
        $tecnicoA = Usuario::factory()->create();
        $tecnicoB = Usuario::factory()->create();
        $recurso = recursos::factory()->create();

        // Asignar previamente el recurso a técnico A
        asignacion_recursos::create([
            'id_user' => $tecnicoA->id_user,
            'id_recurso' => $recurso->id_recurso,
            'fecha_asignacion' => now(),
        ]);

        $this->actingAs($admin);

        // Intentar asignar el mismo recurso a técnico B
        $response = $this->post(route('personal.assignRecurso', $tecnicoB->id_user), [
            'id_recurso' => $recurso->id_recurso
        ]);

        // Assert: Redirección atrás con mensaje de error
        $response->assertSessionHas('error', 'El recurso seleccionado ya se encuentra asignado a otra persona.');
    }
}