<?php

namespace App\Controller;

use App\Config\Types;
use App\Repository\MetaConfigsRepository;
use App\Service\Share\CustomResponse;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Navax\MetaConfig\MetaConfigs2;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MetaConfigController extends AbstractController
{

    private MetaConfigs2 $metaConfig;
    public function __construct(private readonly CustomResponse $customResponse , private readonly MetaConfigsRepository $metaConfigsRepository , private readonly EntityManagerInterface $entityManager , private readonly Types $types, RequestStack $requestStack){
        $this->metaConfig = new MetaConfigs2($this->entityManager , $this->customResponse , $this->metaConfigsRepository , $this->types , $requestStack);
    }

    /**
     * @throws Exception
     */
    public function index(): JsonResponse
    {
        $result = $this->metaConfig->getMetaConfigs();
        return $this->customResponse->success(message: "اطلاعات متا دریافت شد" , data: $result);
    }


    public function store(Request $request): JsonResponse
    {
        $request = $request->request->all();
        return $this->metaConfig->store($request);
    }


    public function edit(Request $request , $metaConfig_id): JsonResponse
    {
        $request = $request->request->all();
        $metaConfig = $this->metaConfigsRepository->findById($metaConfig_id);
        if(empty($metaConfig))
            return $this->customResponse->fail(message: "متا وجود ندارد");

        return $this->metaConfig->edit($metaConfig , $request);
    }

    public function delete($metaConfig_id): JsonResponse
    {
        $metaConfig = $this->metaConfigsRepository->findById($metaConfig_id);
        if(empty($metaConfig))
            return $this->customResponse->fail(message: "متا وجود ندارد");

        $metaConfig->setDeletedAt();
        $this->entityManager->flush();
        return $this->customResponse->success(message: "متا با موفقیت حذف شد");
    }
}
