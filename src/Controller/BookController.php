<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'app_book',methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $booklist = $bookRepository ->findAll();

        $jsonBookList = $serializer->serialize($booklist, 'json');
        return new JsonResponse( $jsonBookList, Response::HTTP_OK, [], true );
    }
}
