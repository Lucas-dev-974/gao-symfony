<?php

namespace App\Controller;

use App\Entity\Attribution;
use App\Repository\AttributionRepository;
use App\Repository\ComputerRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class AttributionController extends ApiController
{
    /**
     * @Route("/api/attributions/", name="CreateAttribution", methods={"POST", "HEAD"})
     */
    public function CreateAttribution(Request $request, ComputerRepository $computerRepositiry, UserRepository $userRepository)
    {
        $inputs    = $this->transformJsonBody($request);

        $validator = $this->validator->startContext()
                    ->atPath('computerID')->validate($inputs->get('computerID'), [new NotBlank(), new Assert\Type('string')])
                    ->atPath('clientID')->validate($inputs->get('clientID'),    [new NotBlank(), new Assert\Type("string")])
                    ->atPath('date')->validate($inputs->get('date'),            [new NotBlank(), new Assert\DateTime()])
                    ->atPath('horraire')->validate($inputs->get('horraire'),    [new NotBlank(), new Assert\Type("string")])
                    ->getViolations();

        $errors = $this->returnValidationErrors($validator);
        if($errors !== true) $this->json(['message' => $errors]);

        $computer = $computerRepositiry->find($inputs->get('computerID'));
        $client   = $userRepository->find($inputs->get('clientID'));
        $date     = new DateTime($inputs->get('date'));

        $attribution = new Attribution();
        $attribution->setComputer($computer);
        $attribution->setUser($client);
        $attribution->setHorraire($inputs->get('horraire'));
        $attribution->setDate($date);

        $this->GetEntityManager()->persist($attribution);
        $this->GetEntityManager()->flush();

        return new JsonResponse(['success' => true, 'attribution' => $attribution]);
    }

    /**
     * @Route("/api/attributions/{id}", name="deleteAttribution", methods={"DELETE", "HEAD"})
     */
    public function delete($id, AttributionRepository $attributionRepository){
        if(empty($id) || !is_numeric($id)) return $this->json(['message' => 'Veuillez renseigné un nombre pour le champs id'], 403);
        $attribution = $attributionRepository->find($id);
        if(!$attribution) return $this->json(['message' => 'Une erreur est survenue'], 403);
        $this->GetEntityManager()->remove($attribution);
        $state = $this->GetEntityManager()->flush();
        if($attribution->getId() === null) return $this->json([], 200);
        else return $this->json(['message' => 'Une erreur est survenu, il s\'emblerait que cet attribution n\'existe pas !'], 304);
    }
}
