<?php

namespace Navax\MetaConfig;

use App\Config\Types;
use App\Entity\MetaConfigs;
use App\Repository\MetaConfigsRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Share\CustomResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class MetaConfigs2
{
    private $request;
    private $data;
    public function  __construct(private readonly EntityManagerInterface $entityManager , private readonly CustomResponse $customResponse , private readonly MetaConfigsRepository $metaConfigsRepository , private readonly Types $types , RequestStack $requestStack){
        $this->request = $requestStack->getCurrentRequest();
        if(str_contains($this->request->headers->get('content-type') , 'multipart/form-data')) {
            $this->data = $_POST;
        }else {
            $this->data = $this->request->request->all();
        }
    }

    /**
     * @throws Exception
     */
    public function getMetaConfigs(): array
    {
        return $this->metaConfigsRepository->OrderAndSearch();
    }


    public function store($data): JsonResponse
    {
        $MetaConfig = $this->metaConfigsRepository->findByNameAndReference($data['name'] , $data['reference']);
        if(!empty($MetaConfig))
            return $this->customResponse->fail(message: "با این نام و reference قبلا ثبت شده است" , data: ['metaConfig_id' => $MetaConfig->getId()]);

        $metaConfig = new MetaConfigs();
        $metaConfig->setName($data['name']);
        $metaConfig->setReference($data['reference']);
        $metaConfig->setAppCode($data['app_code']);
        $metaConfig->setType($data['type']);
        $metaConfig->setRequired($data['required']);
        $params = $this->generateParams($data['type'] , $data);
        $metaConfig->setParams(json_encode($params));
        $metaConfig->setCreatedAt();

        $this->entityManager->persist($metaConfig);
        $this->entityManager->flush();

        return $this->customResponse->success(message: "metaConfig با موفقیت ثیت شد" , data: ['metaConfig_id' => $metaConfig->getId()]);
    }


    public function edit(MetaConfigs $metaConfig , $data): JsonResponse
    {
        if(!$this->validateEdit($metaConfig , $data))
            return $this->customResponse->fail("این نام با این reference قابلیت ویرایش ندارد");

        $metaConfig->setName($data['name'] ?? $metaConfig->getName());
        $metaConfig->setReference($data['reference'] ?? $metaConfig->getReference());
        $metaConfig->setAppCode($data['app_code'] ?? $metaConfig->getAppCode());
        $metaConfig->setType($data['type'] ?? $metaConfig->getType());
        $metaConfig->setRequired($data['required'] ?? $metaConfig->isRequired());
        $params = $this->editParams($metaConfig , $data);
        $metaConfig->setParams(json_encode($params));
        $metaConfig->setUpdatedAt();

        $this->entityManager->persist($metaConfig);
        $this->entityManager->flush();
        return $this->customResponse->success(message: "metaConfig با موفقیت ویرایش شد" , data: ['metaConfig_id' => $metaConfig->getId()]);
    }

    public function validateEdit(MetaConfigs $metaConfig , $data): bool
    {

        if(!empty($data['name']) && !empty($data['reference'])){
            $name = $data['name'];
            $reference = $data['reference'];
        }elseif (isset($data['name'])){
            $name = $data['name'];
            $reference = $metaConfig->getReference();
        }elseif (isset($data['reference'])){
            $name = $metaConfig->getName();
            $reference = $data['reference'];
        }else{
            return true;
        }

        $MetaConfig = $this->metaConfigsRepository->findByNameAndReference($name , $reference);
        if(!empty($MetaConfig) && $MetaConfig->getId() != $metaConfig->getId())
            return false;
        return true;
    }

    public function generateParams($type , $data): array|string
    {
        $params = [];$fields = [];
        if($type == 'number'){
            $fields = array_keys($this->types->getNumber());
        }elseif ($type == 'selectBox'){
            $fields = array_keys($this->types->getSelectBox());
        }elseif ($type == 'text'){
            $fields = array_keys($this->types->getText());
        }elseif($type == 'file'){
            $fields = array_keys($this->types->getFile());
        }
        foreach ($fields as $field){
            if(isset($data[$field]) && !empty($data[$field]))
                $params[$field] = $data[$field];
        }
        return $params;
    }

    private function editParams(MetaConfigs $metaConfig , $data): array
    {

        $type = $data['type'] ?? $metaConfig->getType();
        $old_params = json_decode($metaConfig->getParams() , true);

        $params = [];$fields = [];
        if($type == 'number'){
            $fields = array_keys($this->types->getNumber());
        }elseif ($type == 'selectBox'){
            $fields = array_keys($this->types->getSelectBox());
        }elseif ($type == 'text'){
            $fields = array_keys($this->types->getText());
        }elseif($type == 'file'){
            $fields = array_keys($this->types->getFile());
        }

        foreach ($fields as $field){
            if(isset($data[$field]) && !empty($data[$field]))
                $params[$field] = $data[$field];
            elseif (isset($old_params[$field]))
                $params[$field] = $old_params[$field];
        }
        return $params;
    }

    public function generateMetaValidation($reference , $type_validation = null , $form_data = false): array
    {
        $meta = $this->metaConfigsRepository->findAllByReference($reference);
        $meta_validation = [];
        foreach ($meta as $item){

            ///////////required/////////////
            $required = $item->isRequired();
            $string = $required ? 'required' : 'nullable';
            if($type_validation == 'edit'){
                $string = ($string == 'required') ? 'not_emptyString' : 'nullable';
            }

            ////////////type/////////////////
            $type = $item->getType();
            if($type == "number") {
                $string .= '|numeric';
            }elseif ($type == 'phone') {
                $string .= '|phone:persian';
            }elseif ($type == 'email'){
                $string .= '|email';
            }elseif ($type == 'file'){
                $string .= '|numeric|meta_file';
            }

            ////////////params///////////////
            $params = json_decode($item->getParams() , true) ?? [];
            foreach ($params as $key => $value){
                if($key == 'values') {
                    $string .= "|in:$value";
                }elseif ($key == 'default') {
                    $this->setDefault($item , $value , $form_data);
                }else {
                    $string .= "|$key:$value";
                }
            }

            if($form_data)
                $meta_validation['meta_'.$item->getName()] = $string;
            else
                $meta_validation['meta.'.$item->getName()] = $string;
        }

        return $meta_validation;
    }

    private function setDefault($item , $value , $form_data): void
    {
        if($form_data){
            if (!in_array('meta_' . $item->getName(), array_keys($this->data))) {
                $this->data = array_merge($this->data, ['meta_' . $item->getName() => $value]);
                $_POST = $this->data;
            }
        }else {
            if (!in_array($item->getName(), array_keys($this->data['meta'] ?? []))) {
                $meta_data = array_merge($this->data['meta'] ?? [], [$item->getName() => $value]);
                $this->data['meta'] = $meta_data;
                $this->request->request->replace($this->data);
            }
        }
    }
}