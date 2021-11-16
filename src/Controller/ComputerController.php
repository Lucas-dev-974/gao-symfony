<?php

namespace App\Controller;

use App\Entity\Computer;
use App\Repository\ComputerRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class ComputerController extends ApiController
{

    /**
     * @Route("/api/computers/{date}", name="getAllComputers", methods={"GET", "HEAD"})
     */
    public function getComputers(ComputerRepository $ComputerRepository, $date)
    {
        $computers   = $ComputerRepository->findAllWithAttributions($date);
        return $this->json([$computers]);
        foreach($computers as $computer) $computer->getAttributions($date);
        $computersData = $this->get('serializer')->serialize($computers, 'json');
        return new JsonResponse(['success' => true, 'computers' => $computersData]);
    }

    /**
     * @Route("/api/computers/", methods={"POST", "HEAD"})
    */
    public function create(Request $request){
        $inputs = $this->transformJsonBody($request);
        $validator = $this->validator->startContext()
                          ->atPath('name')->validate($inputs->get('name'), [new NotBlank(), new Assert\Type('string')])
                          ->getViolations();

        $validated = $this->returnValidationErrors($validator);
        if($validated !== true) return $this->json(['message' => $validated], 403);
        $computer = new Computer();
        $computer->setName($request->get('name'));
        
        $this->GetEntityManager()->persist($computer);
        $this->GetEntityManager()->flush();
        $computerData = $this->get('serializer')->serialize($computer, 'json');
        return new JsonResponse(['computer' => $computerData]);
    }

    /**
     * @Route("/api/computer/update", name="UpdateComputer")
    */
    public function updateComputer(Request $request, ComputerRepository $ComputerRepository, SerializerInterface $serializer){
        $em = $this->getDoctrine()->getManager();
        $request  = $this->transformJsonBody($request);
        $id   = $request->get('id');
        $name = $request->get('name');

        if(empty($id)) return new JsonResponse(['success' => false, 'message' => 'Veuillez remplir touts les champs !']);

        $computer = $ComputerRepository->find($id);
        if(empty($computer)) return new JsonResponse(['success' => false, 'message' => 'L\'ordinateur n\'existe pas !']);
        
        // Update if param from request to computer is not empty
        if(!empty($request->get('name'))) $computer->setName($request->get('name'));

        $em->persist($computer);
        $em->flush();
        $data = $serializer->serialize($computer, JsonEncoder::FORMAT);
        return new JsonResponse(['success' => true, 'message' => $data]);
    }

    /**
     * @Route("/api/computer/{id}/delete", name="UpdateComputer")
    */
    public function deleteComputer($id, ComputerRepository $ComputerRepository){
        $em = $this->getDoctrine()->getManager();
        $computer = $ComputerRepository->find($id);
        $em->remove($computer);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }


    /**
     * @Route("/api/computer/test", name="ComputerTest")
    */
    public function Test(){
        return new JsonResponse(['success' => true]);
    }
}
