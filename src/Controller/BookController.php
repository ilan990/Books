<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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

    #[Route('/api/books', name: 'create_book',methods: ['POST'])]
    public function createBook(Request $request,SerializerInterface $serializer, EntityManagerInterface $em,
                               UrlGeneratorInterface $urlGenerator,AuthorRepository $authorRepository): JsonResponse
    {
        $book = $serializer->deserialize($request->getContent(),Book::class,'json');
        $em->persist($book);
        $em->flush();

        $content = $request ->toArray();
        $idAuthor = $content['idAuthor'] ?? -1;

        $book->setAuthor($authorRepository->find($idAuthor));

        $jsonBook = $serializer->serialize($book, 'json',['groups'=>'getBooks']);
        $location = $urlGenerator->generate('detail_book',['id'=>$book->getId()],UrlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse( $jsonBook, Response::HTTP_CREATED,  ["Location"=>$location],true);
    }

    #[Route('/api/books/{id}', name: 'update_book',methods: ['PUT'])]
    public function UpdateBook(Request $request,SerializerInterface $serializer,Book $currentBook, EntityManagerInterface $em,
                               UrlGeneratorInterface $urlGenerator,AuthorRepository $authorRepository): JsonResponse
    {
        $updatedBook = $serializer->deserialize($request->getContent(),Book::class,'json',[AbstractNormalizer::OBJECT_TO_POPULATE => $currentBook]);

        $content = $request ->toArray();
        $idAuthor = $content['idAuthor'] ?? -1;

        $updatedBook->setAuthor($authorRepository->find($idAuthor));

        $em->persist($updatedBook);
        $em->flush();

        return new JsonResponse( null, Response::HTTP_NO_CONTENT,);
    }
}
