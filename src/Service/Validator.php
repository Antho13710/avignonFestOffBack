<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validator
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validator($entity)
    {
        $errors = $this->validator->validate($entity);

        if ($errors) {

            foreach ($errors as $error) {

                $errorArray[$error->getpropertyPath()] = $error->getMessage();
            }

            if (isset($errorArray)) {
                return new JsonResponse($errorArray, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }
}
