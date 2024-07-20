<?php

namespace App\Controllers;

use App\Entities\Post;
use App\Services\PostService;
use Web\Framework\Controller\AbstractController;
use Web\Framework\Http\RedirectResponce;
use Web\Framework\Http\Response;
use Web\Framework\Session\SessionInterface;

class PostController extends AbstractController
{

    public function __construct(
        private PostService $service,
    )
    {
    }
    public function show(int $id): Response
    {
        $post = $this->service->findOrFail($id);
        return $this->render('post.html.twig', [
            'post' => $post
        ]);
    }

    public function create(): Response
    {
        return $this->render('create_post.html.twig');
    }

    public function store(): Response
    {
        $post = Post::create(
            $this->request->input('title'),
            $this->request->input('body'),
        );

        $post = $this->service->save($post);
        $this->request->getSession()->setFlash('success', 'Пост був успішно створений!');
        return new RedirectResponce("/posts/{$post->getId()}");
    }
}