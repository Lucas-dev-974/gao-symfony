<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends ApiController
{
    /**
     * @Route("/api/clients/", name="user", methods={"PATCH", "HEAD"})
     */
    public function update(Request $request, UserRepository $userRepository)
    {
        $inputs = $this->transformJsonBody($request);
        
        $data = json_encode($request->getContent(), true);
        return $this->json($request->getContent());
        $validator = $this->validator->startContext()
                    ->atPath('clientID')->validate($inputs->get('clientID'), [new NotBlank(), new Assert\Type('integer')])
                    ->atPath('update?')->validate($inputs->get('validate'),  [new NotBlank(), new Assert\Type('string')])
                    ->atPath('value')->validate($inputs->get('value'),        [new NotBlank(), new Assert\Type('string')])
                    ->getViolations();
        $errors = $this->returnValidationErrors($validator);
        if($errors !== true) return $this->json(['message' => $errors]);

        $user = $userRepository->find($inputs->get('clientID'));
        if(!$user) return $this->json(['message' => 'Désolé il s\'emblerais que ce client n\'existe pas !']);
        
        switch($inputs->get('update?')){
            case 'password':
                break;
            case 'roles':
                $user->setRoles($inputs->get('value'));
            case 'email':
                $user->setEmail($inputs->get('value'));

        }
        $this->GetEntityManager()->flush();
    }
}
