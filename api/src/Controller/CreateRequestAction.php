<?php

namespace App\Controller;



use App\Entity\Request as EntityRequest;
use App\Repository\PropertyRepository;
use App\Repository\RequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


#[AsController]
class CreateRequestAction extends AbstractController
{
    public function __construct(private readonly Security $security){}
    public function __invoke(Request $request, PropertyRepository $propertyRepository, RequestRepository $requestRepository): EntityRequest
    {
        $property_id = filter_var($request->toArray()["property"], FILTER_SANITIZE_NUMBER_INT);
        $property = $propertyRepository->find($property_id);
        if (!$property) {
            throw new BadRequestHttpException('"property" is required');
        }
        $user = $this->security->getUser();
        $isAlreadyApplied = null !== $requestRepository->findOneBy([
                "lodger" => $user,
                "property" => $property
            ]);
        if($isAlreadyApplied){
            throw new ConflictHttpException('Already applied');
        }
        $appliedRequest = new EntityRequest();
        $appliedRequest->setLodger($this->security->getUser())
        ->setProperty($property);
        return $appliedRequest;
    }
}