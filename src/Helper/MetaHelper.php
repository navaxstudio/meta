<?php

namespace App\Helper;

use App\Config\Types;
use App\Repository\MetaConfigsRepository;
use App\Service\Share\Config;
use App\Service\Share\CustomResponse;
use Doctrine\ORM\EntityManagerInterface;
use Navax\MetaConfig\MetaConfigs2;
use Symfony\Component\HttpFoundation\RequestStack;

class MetaHelper
{
    private MetaConfigs2 $metaConfig;
    public function __construct(private readonly CustomResponse $customResponse , private readonly MetaConfigsRepository $metaConfigsRepository, private readonly Config $config, private readonly EntityManagerInterface $entityManager , private readonly Types $types ,  RequestStack $requestStack){
        $this->metaConfig = new MetaConfigs2($this->entityManager , $this->customResponse , $this->metaConfigsRepository , $this->types , $requestStack);
    }

    public function generateValidation($reference , $config_name , $type_validation = null , $form_data = false): array
    {
        return array_merge($this->metaConfig->generateMetaValidation($reference , $type_validation , $form_data) , $this->config->getConfig(type: $config_name)['fields']);
    }
}