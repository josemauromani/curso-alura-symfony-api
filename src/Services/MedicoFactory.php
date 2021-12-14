<?php

namespace App\Services;

use App\Entity\Medico;
use App\Repository\EspecialidadeRepository;

class MedicoFactory
{
    /**
     * @var EspecialidadeRepository
     */
    private $especialidadeRepository;

    public function __construct(EspecialidadeRepository $especialidadeRepository)
    {
        $this->especialidadeRepository = $especialidadeRepository;
    }

    public function criarMedico(string $json): Medico
    {
        $jsonData = json_decode($json);

        $especialidadeId = $this->especialidadeRepository->find($jsonData->especialidadeId);

        $medico = new Medico();
        $medico
            ->setCrm($jsonData->crm)
            ->setNome($jsonData->nome)
            ->setEspecialidade($especialidadeId);

        return $medico;
    }
}
