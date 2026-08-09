<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function un_etudiant_peut_se_connecter()
    {
        $user = User::factory()->create([
            'email'    => 'student@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'student',
        ]);

        $response = $this->post('/login', [
            'email'    => 'student@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/mon-espace');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function un_instructeur_peut_se_connecter()
    {
        $user = User::factory()->create([
            'email'    => 'instructor@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'instructor',
        ]);

        $response = $this->post('/login', [
            'email'    => 'instructor@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/espace-instructeur');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function un_administrateur_peut_se_connecter()
    {
        $user = User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function la_connexion_echoue_si_lemail_nexiste_pas()
    {
        $response = $this->post('/login', [
            'email'    => 'inexistant@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function la_connexion_echoue_si_le_mot_de_passe_est_incorrect()
    {
        User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function la_connexion_echoue_si_le_compte_est_desactive()
    {
        // Si vous avez un champ `active` sur votre modèle User
        $user = User::factory()->create([
            'email'    => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'active'   => false,
        ]);

        $response = $this->post('/login', [
            'email'    => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function la_redirection_apres_connexion_utilise_la_destination_initiale()
    {
        $user = User::factory()->create([
            'email'    => 'user@example.com',
            'password' => bcrypt('password123'),
            'role'     => 'student',
        ]);

        $this->get('/some-protected-page')->assertRedirect('/login');

        $response = $this->post('/login', [
            'email'    => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/some-protected-page');
    }
}