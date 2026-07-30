<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_etudiant_peut_sinscrire()
    {
        $response = $this->post('/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => 'student',
        ]);

        $response->assertRedirect('/mon-espace');
        $this->assertDatabaseHas('users', [
            'email' => 'jean@example.com',
            'role'  => 'student',
        ]);
        $user = User::where('email', 'jean@example.com')->first();
        $this->assertTrue(password_verify('Password123!', $user->password));
    }

    /** @test */
    public function un_instructeur_peut_sinscrire()
    {
        $response = $this->post('/register', [
            'name'                  => 'Marie Curie',
            'email'                 => 'marie@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => 'instructor',
        ]);

        $response->assertRedirect('/espace-instructeur');
        $this->assertDatabaseHas('users', [
            'email' => 'marie@example.com',
            'role'  => 'instructor',
        ]);
    }

    /** @test */
    public function lemail_doit_etre_unique()
    {
        User::factory()->create(['email' => 'existant@example.com']);

        $response = $this->post('/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'existant@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => 'student',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    /** @test */
    public function les_champs_obligatoires_sont_requis()
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    /** @test */
    public function le_mot_de_passe_doit_avoir_au_moins_8_caracteres()
    {
        $response = $this->post('/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'short',
            'password_confirmation' => 'short',
            'role'                  => 'student',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function lemail_doit_etre_valide()
    {
        $response = $this->post('/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'email-invalide',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => 'student',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function la_confirmation_du_mot_de_passe_doit_correspondre()
    {
        $response = $this->post('/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Different123!',
            'role'                  => 'student',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function le_role_doit_etre_soit_student_soit_instructor()
    {
        $response = $this->post('/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role'                  => 'admin', // non autorisé à l’inscription
        ]);

        $response->assertSessionHasErrors('role');
    }
}