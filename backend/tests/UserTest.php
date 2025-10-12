<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;

class UserTest extends ApiTestCase
{
    private Client $client;
    
    // protected function setUp(): void
    // {
    //     $this->client = static::createClient();
    // }

    // public function testGetAllUsers(): void
    // {
    //     $response = $this->client->request('GET', '/api/users');
    //     $this->assertResponseIsSuccessful();
    //     $this->assertMatchesResourceCollectionJsonSchema(\App\Entity\User::class);
    // }

    // public function testCreateUser(): void
    // {
    //     // Créer un rôle
    //     $roleResponse = $this->client->request('POST', '/api/roles', [
    //         'headers' => ['Content-Type' => 'application/ld+json'],
    //         'json' => ['label' => 'ROLE_EMPLOYE'],
    //     ]);
        
    //     $this->assertResponseIsSuccessful();
    //     $roleData = $roleResponse->toArray();
        
    //     // Créer un utilisateur
    //     $userResponse = $this->client->request('POST', '/api/users', [
    //         'headers' => ['Content-Type' => 'application/ld+json'],
    //         'json' => [
    //             'name' => 'TestName',
    //             'firstname' => 'TestFirstname',
    //             'email' => 'test@example.com',
    //             'password' => 'SecurePassword123',
    //             'role' => $roleData['@id']
    //         ],
    //     ]);
        
    //     $this->assertResponseIsSuccessful();
    //     $this->assertJsonContains(['name' => 'TestName']);
    // }

    // public function testGetUser(): void
    // {
    //     // Créer les données de test
    //     $userData = $this->createTestUser();
        
    //     $response = $this->client->request('GET', $userData['@id']);
    //     $this->assertResponseIsSuccessful();
    //     $this->assertMatchesResourceItemJsonSchema(\App\Entity\User::class);
    // }

    // public function testUpdateUser(): void
    // {
    //     $userData = $this->createTestUser();
        
    //     $response = $this->client->request('PATCH', $userData['@id'], [
    //         'headers' => ['Content-Type' => 'application/merge-patch+json'],
    //         'json' => ['email' => 'updated@example.com'],
    //     ]);
        
    //     $this->assertResponseIsSuccessful();
    //     $this->assertJsonContains(['email' => 'updated@example.com']);
    // }

    // public function testDeleteUser(): void
    // {
    //     $userData = $this->createTestUser();
        
    //     $response = $this->client->request('DELETE', $userData['@id']);
    //     $this->assertResponseStatusCodeSame(204);
    // }

    // private function createTestUser(): array
    // {
    //     // Créer un rôle
    //     $roleResponse = $this->client->request('POST', '/api/roles', [
    //         'headers' => ['Content-Type' => 'application/ld+json'],
    //         'json' => ['label' => 'ROLE_TEST_' . uniqid()],
    //     ]);
        
    //     $roleData = $roleResponse->toArray();
        
    //     // Créer un utilisateur
    //     $userResponse = $this->client->request('POST', '/api/users', [
    //         'headers' => ['Content-Type' => 'application/ld+json'],
    //         'json' => [
    //             'name' => 'TestUser',
    //             'firstname' => 'TestFirstname',
    //             'email' => 'user_' . uniqid() . '@example.com',
    //             'password' => 'password123',
    //             'role' => $roleData['@id']
    //         ],
    //     ]);
        
    //     return $userResponse->toArray();
    // }
}