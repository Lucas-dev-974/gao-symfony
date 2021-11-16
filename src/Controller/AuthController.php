<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AuthController extends ApiController
{

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function register(Request $request){
        $inputs  = $this->transformJsonBody($request);
        
        $meta = $this->validator->startContext()
                    ->atPath('email')->validate($inputs->get('email'), [new NotBlank(), new Email()])
                    ->atPath('password')->validate($inputs->get('password'), [new NotBlank(), new Assert\Type('string'), new Assert\Length(['min' => 6])])
                    ->getViolations();

        $errors = $this->returnValidationErrors($meta);
        if($errors !== true) return $this->json(['message' => $errors]);

        $user = new User($inputs->get('email'));
        $user->setPassword($this->encoder->encodePassword($user, $inputs->get('password')));
        $user->setEmail($inputs->get('email'));   

        $this->GetEntityManager()->persist($user);
        $this->GetEntityManager()->flush();
        
        $token = $this->JWTManager->create($user);
        $user =  $this->serializer->serialize(['user' => $user], JsonEncoder::FORMAT);
        return new JsonResponse(['token' => $token, 'user' => $user], 200);
    }
  
    public function test(Request $request){
        $data = $this->transformJsonBody($request);
        $meta = $this->validator->startContext()
                          ->atPath('email')->validate($data->get('email'), [new NotBlank(), new Email()])
                          ->getViolations();

        $validation = $this->returnValidationErrors($meta);
        return $this->json(['status' => $validation]);
    }

    public function login(Request $request, UserRepository $userRepo){
        $inputs = $this->transformJsonBody($request);
        $validator = $this->validator->startContext()
                          ->atPath('email')->validate($inputs->get('email'), [new NotBlank(), new Email()])
                          ->atPath('password')->validate(($inputs->get('password')), [new NotBlank(), new Assert\Type('string'), new Assert\Length(['min' => '6'])])
                          ->getViolations();

        $errors = $this->returnValidationErrors($validator);
        if($errors !== true) return $this->json(['message' => $errors], 403);
        $user = $userRepo->findOneBy(['email' => $inputs->get('email')]);
        
        if(!$user || !$this->encoder->isPasswordValid($user, $inputs->get('password'))){
            return $this->json(['message' => 'Email ou mot de passe incorrecte'], 403);
        }

        return $this->json(['state' => $this->JWTManager->create($user)]);
    }
}
