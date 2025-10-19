<?php

namespace App\Controller;

use App\Service\TagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TagController extends AbstractController
{
    private $tagService;
    public function __construct(
        TagService  $tagService,
    )
    {
        $this->tagService = $tagService;
    }

    #[Route('/api/tag', name: 'api_tag')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $tag = $this->tagService->tag($user);

        return $this->json([
            'statut' => $tag,
        ]);
    }
}
