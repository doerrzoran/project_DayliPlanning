<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Role;

class UserTest extends ApiTestCase
{
    private Client $client;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);

        // Optionnel : nettoyer / préparer la DB de test ici si nécessaire
    }

    /**
     * Crée en base un Role ROLE_ADMIN et un User lié à ce rôle.
     * Retourne l'IRI de l'utilisateur (ex: /api/users/123)
     *
     * @param string|null $email
     * @param string $plainPassword
     * @return string
     */
    private function createAdminUserInDatabase(string $email, string $plainPassword = 'admin123'): string
    {
        $email = $email ?? 'admin_' . uniqid() . '@example.com';

        // Créer le rôle ADMIN
        $role = new Role();
        // Adapte si le champ s'appelle différemment (label/name)
        if (method_exists($role, 'setLabel')) {
            $role->setLabel('ROLE_ADMIN');
        } elseif (method_exists($role, 'setName')) {
            $role->setLabel('ROLE_ADMIN');
        }
        $this->em->persist($role);

        // Créer l'utilisateur
        $user = new User();
        if (method_exists($user, 'setName')) {
            $user->setName('AdminUser');
        }
        if (method_exists($user, 'setFirstname')) {
            $user->setFirstname('SuperUser');
        }
        if (method_exists($user, 'setEmail')) {
            $user->setEmail($email);
        } 

        // Hasher le mot de passe
        $hashed = $this->passwordHasher->hashPassword($user, $plainPassword);
        if (method_exists($user, 'setPassword')) {
            $user->setPassword($hashed);
        }

        // Assigner la relation Role -> User
        // On suppose ManyToOne: setRole(Role $role)
        if (method_exists($user, 'setRole')) {
            $user->setRole($role);
        } else {
            // Si la relation est ManyToMany et que la méthode d'ajout est addRole
            if (method_exists($user, 'addRole')) {
                $user->setRole($role);
            } elseif (method_exists($user, 'setRoles')) {
                // si setRoles attend un tableau de Role ou de string, essayons Role
                $user->setRole($role);
            }
        }

        $this->em->persist($user);
        $this->em->flush();
        $this->em->refresh($user);

        return '/api/users/' . $user->getId();
    }

    /**
     * Appelle /api/login_check et retourne le token JWT.
     *
     * @param string $email
     * @param string $plainPassword
     * @return string
     */
    private function getTokenForCredentials(string $email, string $plainPassword): string
    {
        $resp = $this->client->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => ['email' => $email, 'password' => $plainPassword],
        ]);

        $this->assertResponseIsSuccessful('Unable to get token from /api/login_check');
        $data = $resp->toArray();
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }

    /**
     * Helper pour faire une requête authentifiée.
     *
     * @param string $method
     * @param string $uri
     * @param string $token
     * @param array $options
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     */
    private function requestAuth(string $method, string $uri, string $token, array $options = [])
    {
        $headers = $options['headers'] ?? [];
        $headers['Authorization'] = 'Bearer ' . $token;
        $options['headers'] = $headers;

        return $this->client->request($method, $uri, $options);
    }

    public function testAdminCanAccessUsers(): void
    {
        $email = 'admin_test_' . uniqid() . '@example.com';
        $plainPassword = 'adminPass123';

        // créer l'admin en DB
        $this->createAdminUserInDatabase($email, $plainPassword);

        // obtenir token
        $token = $this->getTokenForCredentials($email, $plainPassword);

        // accès protégé
        $response = $this->requestAuth('GET', '/api/users', $token);
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCreateUserWithAdminToken(): void
    {
        // Préparer un admin
        $email = 'admin_creator_' . uniqid() . '@example.com';
        $plainPassword = 'adminCreator123';
        $this->createAdminUserInDatabase($email, $plainPassword);
        $token = $this->getTokenForCredentials($email, $plainPassword);

        // Créer un role via l'API (utilisant le token admin)
        $roleResponse = $this->requestAuth('POST', '/api/roles', $token, [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => ['label' => 'ROLE_EMPLOYE'],
        ]);
        $this->assertResponseIsSuccessful();
        $roleData = $roleResponse->toArray();

        // Créer un utilisateur via l'API
        $userEmail = 'api_created_' . uniqid() . '@example.com';
        $userResponse = $this->requestAuth('POST', '/api/users', $token, [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'name' => 'ApiCreated',
                'firstname' => 'ByTest',
                'email' => $userEmail,
                'password' => 'SecurePassword123',
                'role' => $roleData['@id'],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['name' => 'ApiCreated']);
    }

    public function testUpdateAndDeleteUserWithAdminToken(): void
    {
        // créer admin et obtenir token
        $email = 'admin_updel_' . uniqid() . '@example.com';
        $plainPassword = 'adminUpDel123';
        $this->createAdminUserInDatabase($email, $plainPassword);
        $token = $this->getTokenForCredentials($email, $plainPassword);

        // créer un user 'cible' en DB (utilitaire direct)
        $targetEmail = 'target_' . uniqid() . '@example.com';
        $targetPlain = 'targetPass123';
        $userIri = $this->createAdminUserInDatabase($targetEmail, $targetPlain); // crée un user lié à ROLE_ADMIN

        // PATCH l'utilisateur
        $patchResp = $this->requestAuth('PATCH', $userIri, $token, [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['email' => 'updated_' . uniqid() . '@example.com'],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@id' => $userIri]);

        // DELETE l'utilisateur
        $delResp = $this->requestAuth('DELETE', $userIri, $token);
        $this->assertResponseStatusCodeSame(204);
    }
}
