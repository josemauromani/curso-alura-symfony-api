<?php

namespace App\Controller;

use App\Entity\Medico;
use App\Services\MedicoFactory;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedicosController extends AbstractController
{
    private $entityManager;
    private $doctrine;
    private $medicoFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        ManagerRegistry        $doctrine,
        MedicoFactory          $medicoFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
        $this->medicoFactory = $medicoFactory;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/medicos", methods={"POST"})
     */
    public function novo(Request $request): Response
    {
        $corpoRequisicao = $request->getContent();
        $medico = $this->medicoFactory->criarMedico($corpoRequisicao);

        $this->entityManager->persist($medico);
        $this->entityManager->flush();

        return new JsonResponse($medico);
    }

    /**
     * @return JsonResponse
     * @Route("/medicos", methods={"GET"})
     */
    public function buscarTodos()
    {
        $repositorioDeMedicos = $this->doctrine->getRepository(Medico::class);
        $medicos = $repositorioDeMedicos->findAll();
        return new JsonResponse($medicos);
    }

    /**
     * @Route("medico/{id}", methods={"GET"})
     */
    public function buscarUm(int $id): Response
    {
        $medico = $this->buscaMedico($id);
        $codigoRetorno = (is_null($medico)) ? Response::HTTP_NO_CONTENT : 200;

        return new JsonResponse($medico, $codigoRetorno);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     * @Route("/medico/{id}", methods={"PUT"})
     */
    public function atualiza(int $id, Request $request): Response
    {
        $corpoDaRequisicao = $request->getContent();

        $medicoEnviado = $this->medicoFactory->criarMedico($corpoDaRequisicao);

        $medicoExistente = $this->buscaMedico($id);

        if (is_null($medicoExistente)) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        $medicoExistente
            ->setCrm($medicoEnviado->getCrm())
            ->setNome($medicoEnviado->getNome())
            ->setEspecialidade($medicoEnviado->getEspecialidade());

        $this->entityManager->flush();

        return new Response('Medico atualizado com sucesso', 200);
    }

    /**
     * @Route("/medico/{id}", methods={"DELETE"})
     */
    public function remove(int $id): Response
    {
        $medico = $this->buscaMedico($id);
        $this->entityManager->remove($medico);
        $this->entityManager->flush();

        return new Response('Medico excluido com sucesso', Response::HTTP_NO_CONTENT);
    }

    public function buscaMedico(int $id): Medico
    {
        $medicoRepositorio = $this->doctrine->getRepository(Medico::class);
        return $medicoRepositorio->find($id);
    }

}
