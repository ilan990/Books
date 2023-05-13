<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class BookController extends AbstractController
{
    #[Route('/api/books', name: 'All_book',methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $booklist = $bookRepository ->findAll();

        $jsonBookList = $serializer->serialize($booklist, 'json',['groups'=> 'getBooks']);
        return new JsonResponse( $jsonBookList, Response::HTTP_OK, [], true );
    }

    #[Route('/api/books/{id}', name: 'detail_book',methods: ['GET'])]
    public function getDetailBook(int $id,Book $book, SerializerInterface $serializer): JsonResponse
    {
            $jsonBook = $serializer->serialize($book, 'json',['groups'=> 'getBooks']);
            return new JsonResponse( $jsonBook, Response::HTTP_OK, [], true );
    }

    #[Route('/api/books/{id}', name: 'delete_book',methods: ['DELETE'])]
    public function deleteBook(int $id,Book $book, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($book);
        $em->flush();
        return new JsonResponse( null, Response::HTTP_NO_CONTENT);
    }
}
