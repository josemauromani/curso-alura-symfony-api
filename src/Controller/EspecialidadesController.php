<?php

namespace App\Controller;

use App\Entity\Especialidade;
use App\Repository\EspecialidadeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EspecialidadesController extends AbstractController
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager, EspecialidadeRepository $repository)
    {

        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/especialidades", methods={"POST"})
     */
    public function nova(Request $request): Response
    {
        $dadosRequest = $request->getContent();
        $json = json_decode($dadosRequest);

        $especialiade = new Especialidade();
        $especialiade->setDescricao($json->descricao);

        $this->entityManager->persist($especialiade);
        $this->entityManager->flush();

        return new JsonResponse($especialiade);
    }

    /**
     * @return Response
     * @Route("/especialidades", methods={"GET"})
     */
    public function buscarTodas(): Response
    {
        $especialidadeList = $this->repository->findAll();
        return new JsonResponse($especialidadeList);
    }

    /**
     * @return Response
     * @Route("/especialidade/{id}", methods={"GET"})
     */
    public function buscarUma(int $id): Response
    {
        $especialidade = $this->repository->find($id);
        return new JsonResponse($especialidade);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @Route("/especialidade/{id}", methods={"PUT"})
     */
    public function atualiza(int $id, Request $request)
    {
        $dadosRequest = $request->getContent();
        $dadosEmJson = json_decode($dadosRequest);

        $especialidade = $this->repository->find($id);
        $especialidade->setDescricao($dadosEmJson->descricao);

        $this->entityManager->flush();
        return new JsonResponse($especialidade);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Route("/especialidade/{id}", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {
        $especialidade = $this->repository->find($id);
        var_dump($especialidade->getDescricao());
        $this->entityManager->remove($especialidade);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

}
