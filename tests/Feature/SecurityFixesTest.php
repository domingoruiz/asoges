<?php

namespace Tests\Feature;

use App\Http\Middleware\SelectAsoMiddleware;
use App\Models\Aso;
use App\Models\AsoUsr;
use App\Models\Ejercicio;
use App\Models\Entidad;
use App\Models\EstadoDocumento;
use App\Models\GestorDocumental;
use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\User;
use App\Pages\SelectAso;
use App\Resources\LibroSocios\Pages\CreateLibroSocios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SecurityFixesTest extends TestCase
{
    public function test_non_superadmin_cannot_escalate_to_superadmin_via_select_aso(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'password' => 'secret1234',
            'is_superadmin' => false,
            'alt_usr' => 1,
        ]);

        Auth::login($user);

        Livewire::test(SelectAso::class)
            ->set('aso_id', 'superadmin')
            ->call('submit')
            ->assertHasErrors(['aso_id']);

        $this->assertNotEquals('superadmin', session('rol_activo'));
    }

    public function test_user_cannot_select_another_users_asousr(): void
    {
        $userA = User::create([
            'name' => 'User A',
            'email' => 'usera@example.com',
            'password' => 'secret1234',
            'is_superadmin' => false,
            'alt_usr' => 1,
        ]);

        $userB = User::create([
            'name' => 'User B',
            'email' => 'userb@example.com',
            'password' => 'secret1234',
            'is_superadmin' => false,
            'alt_usr' => 1,
        ]);

        $aso = Aso::create([
            'nombre' => 'Aso Test',
            'cif' => 'B12345678',
            'domicilio_social' => 'Calle 1',
            'fch_constitucion' => now(),
            'alt_usr' => 1,
        ]);

        $rol = Rol::first();

        $asoUsrB = AsoUsr::create([
            'aso_id' => $aso->id,
            'usr_id' => $userB->id,
            'rol_id' => $rol->id,
            'alt_usr' => 1,
        ]);

        // User A attempts to select User B's association assignment
        Auth::login($userA);

        Livewire::test(SelectAso::class)
            ->set('aso_id', (string) $asoUsrB->id)
            ->call('submit')
            ->assertHasErrors(['aso_id']);

        $this->assertNull(session('aso_actual'));
    }

    public function test_select_aso_middleware_blocks_spoofed_superadmin_session(): void
    {
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'regular2@example.com',
            'password' => 'secret1234',
            'is_superadmin' => false,
            'alt_usr' => 1,
        ]);

        Auth::login($user);
        session(['rol_activo' => 'superadmin']);

        $middleware = new SelectAsoMiddleware();
        $request = Request::create('/', 'GET');

        $response = $middleware->handle($request, function () {
            return response('OK');
        });

        // Should redirect to select-aso and clear fake rol_activo
        $this->assertTrue($response->isRedirect());
        $this->assertNull(session('rol_activo'));
    }

    public function test_soft_deleting_document_preserves_physical_file(): void
    {
        Storage::fake('public');

        $aso = Aso::create([
            'nombre' => 'Aso Storage Test',
            'cif' => 'B99999999',
            'domicilio_social' => 'Calle Storage',
            'fch_constitucion' => now(),
            'alt_usr' => 1,
        ]);

        $filePath = 'gestor_documental/' . $aso->id . '/1/documento_test.pdf';
        Storage::disk('public')->put($filePath, 'dummy file content');

        $doc = GestorDocumental::create([
            'aso_id' => $aso->id,
            'tipo_documento_id' => TipoDocumento::create(['aso_id' => $aso->id, 'nombre' => 'Tipo 1', 'alt_usr' => 1])->id,
            'entidad_id' => Entidad::create(['aso_id' => $aso->id, 'nombre_fiscal' => 'Entidad 1', 'cif' => 'CIF1', 'direccion' => 'Dir', 'cp' => '29000', 'localidad' => 'Loc', 'provincia' => 'Prov', 'pais' => 1, 'continente' => 1, 'telefono' => '123', 'email' => 'a@b.com', 'alt_usr' => 1])->id,
            'ejercicio_id' => Ejercicio::create(['aso_id' => $aso->id, 'nombre' => 'Ej 1', 'fch_inicio' => now(), 'fch_fin' => now()->addYear(), 'alt_usr' => 1])->id,
            'estado_documento' => EstadoDocumento::create(['aso_id' => $aso->id, 'nombre' => 'Est 1', 'alt_usr' => 1])->id,
            'direccion_documento' => 'entrada',
            'fecha_documento' => now()->toDateString(),
            'numero_serie' => 'SEC-001',
            'nombre' => 'Documento con archivo',
            'archivo' => $filePath,
            'alt_usr' => 1,
        ]);

        // Soft delete the document
        $doc->delete();

        $this->assertSoftDeleted('gestor_documental', ['id' => $doc->id]);
        // The physical file MUST still exist after soft delete!
        $this->assertTrue(Storage::disk('public')->exists($filePath));

        // When force deleting, the file SHOULD be deleted
        $doc->forceDelete();
        $this->assertFalse(Storage::disk('public')->exists($filePath));
    }

    public function test_create_page_mutator_strictly_enforces_current_session_aso_id(): void
    {
        session(['aso_actual' => 42]);

        $createLibroSocios = new class extends CreateLibroSocios {
            public function testMutate(array $data): array
            {
                return $this->mutateFormDataBeforeCreate($data);
            }
        };

        // Client attempts to pass a malicious aso_id = 999
        $result = $createLibroSocios->testMutate([
            'aso_id' => 999,
            'nombre' => 'Test',
        ]);

        $this->assertEquals(42, $result['aso_id']);
    }

    public function test_crm_create_mutators_strictly_enforce_current_session_aso_id(): void
    {
        session(['aso_actual' => 55]);

        $createContacto = new class extends \App\Resources\Contactos\Pages\CreateContacto {
            public function testMutate(array $data): array { return $this->mutateFormDataBeforeCreate($data); }
        };
        $resContacto = $createContacto->testMutate(['aso_id' => 999, 'nombre_completo' => 'Hacker Contact']);
        $this->assertEquals(55, $resContacto['aso_id']);

        $createInteraccion = new class extends \App\Resources\LibroInteraccions\Pages\CreateLibroInteraccion {
            public function testMutate(array $data): array { return $this->mutateFormDataBeforeCreate($data); }
        };
        $resInteraccion = $createInteraccion->testMutate(['aso_id' => 999, 'interaccion' => 'Directo']);
        $this->assertEquals(55, $resInteraccion['aso_id']);

        $createTarea = new class extends \App\Resources\Tareas\Pages\CreateTarea {
            public function testMutate(array $data): array { return $this->mutateFormDataBeforeCreate($data); }
        };
        $resTarea = $createTarea->testMutate(['aso_id' => 999, 'nombre' => 'Hacker Task']);
        $this->assertEquals(55, $resTarea['aso_id']);
    }

    public function test_crm_resources_scope_queries_strictly_to_active_association(): void
    {
        $aso1 = Aso::create(['nombre' => 'Aso Uno', 'cif' => 'CIF000001', 'domicilio_social' => 'Dir', 'fch_constitucion' => now(), 'alt_usr' => 1]);
        $aso2 = Aso::create(['nombre' => 'Aso Dos', 'cif' => 'CIF000002', 'domicilio_social' => 'Dir', 'fch_constitucion' => now(), 'alt_usr' => 1]);

        \App\Models\Contacto::create(['aso_id' => $aso1->id, 'nombre_completo' => 'C1 Aso1', 'alt_usr' => 1]);
        \App\Models\Contacto::create(['aso_id' => $aso2->id, 'nombre_completo' => 'C2 Aso2', 'alt_usr' => 1]);

        \App\Models\Tarea::create(['aso_id' => $aso1->id, 'nombre' => 'T1 Aso1', 'fecha' => now()->toDateString(), 'frecuencia' => 'puntual', 'alt_usr' => 1]);
        \App\Models\Tarea::create(['aso_id' => $aso2->id, 'nombre' => 'T2 Aso2', 'fecha' => now()->toDateString(), 'frecuencia' => 'puntual', 'alt_usr' => 1]);

        session(['aso_actual' => $aso1->id, 'rol_activo' => 'presidente']);

        $contactosQuery = \App\Resources\Contactos\ContactoResource::getEloquentQuery()->get();
        $this->assertCount(1, $contactosQuery);
        $this->assertEquals('C1 Aso1', $contactosQuery->first()->nombre_completo);

        $tareasQuery = \App\Resources\Tareas\TareaResource::getEloquentQuery()->get();
        $this->assertCount(1, $tareasQuery);
        $this->assertEquals('T1 Aso1', $tareasQuery->first()->nombre);
    }
}
