<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Bizproc\Activity\BaseActivity;
use Bitrix\Bizproc\Activity\PropertiesDialog;
use Bitrix\Bizproc\FieldType;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use php_interface\classes\Dadata;

class CBPCreateCompanyByInnActivity extends BaseActivity
{
    /**
     * @see parent::_construct()
     * @param $name string Activity name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->arProperties = [
            'Inn' => '',

            // return
            'Companyname' => null,
        ];

        $this->SetPropertiesTypes([
            'Companyname' => ['Type' => FieldType::STRING],
        ]);
    }

    /**
     * Return activity file path
     * @return string
     */
    protected static function getFileName(): string
    {
        return __FILE__;
    }

    /**
     * @return ErrorCollection
     */
    protected function internalExecute(): ErrorCollection
    {
        $errors = parent::internalExecute();

        $token = "0c825d0906122684951a7a3d60ee8848289d4344";
        $secret = "db2700343995d8f5e1992e0fcbd81ded70267e71";

        $dadata = new Dadata($token, $secret);
        $dadata->init();

        $fields = array("query" => $this->Inn, "count" => 5);
        $response = $dadata->suggest("party", $fields);

        $companyName = 'Company not found!';
        if(!empty($response['suggestions'])){
            // если копания найдена
            // по ИНН возвращается массив в котором может бытьнесколько элементов (компаний)
            $company = $response['suggestions'][0];
            $companyName = $company['value'];

            $this->log($companyName);

            //create new company
            $companyFields = [
                'TITLE' => $companyName,
                'COMPANY_TYPE' => 'PARTNER',
                'OPENED' => 'Y',
                'ASSIGNED_BY_ID' => \Bitrix\Main\Engine\CurrentUser::get()->getId()
            ];

            if (!\Bitrix\Main\Loader::includeModule('crm')) {
                $this->log("CRM module failed to load!");
                return $errors;
            }

            $companyObj = new CCrmCompany(false);
            $check = $companyObj->CheckFields($companyFields);

            if ($check !== true) {
                $this->log("CheckFields validation failed:");
                foreach ($check as $error) {
                    if (is_object($error) && method_exists($error, 'GetString')) {
                        $this->log($error->GetString());
                    } else {
                        $this->log(print_r($error, true));
                    }
                }
                return $errors;
            }


            $companyId = $companyObj->Add($companyFields);
            if (!$companyId) {
                global $APPLICATION;
                $error = $APPLICATION->GetException();
                $this->log('CRM Company creation failed: ' . ($error ? $error->GetString() : 'Unknown error'));
                return $errors;
            }

            $this->log("CRM Company created successfully. ID: " . $companyId);

            $rootActivity = $this->GetRootActivity(); // получаем объект активити
            // сохранение полученных результатов работы активити в переменную бизнес процесса
             $rootActivity->SetVariable("CompanyId",  $companyId);
        }

        $this->preparedProperties['Companyname'] =  (string)($companyName ?: 'Not found');
        $this->log($this->preparedProperties['Companyname']);

        /*
        $rootActivity = $this->GetRootActivity(); // получаем объект активити
        // сохранение полученных результатов работы активити в переменную бизнес процесса
        // $rootActivity->SetVariable("TEST", $this->preparedProperties['Text']);

        // получение значения полей документа в активити
        $documentType = $rootActivity->getDocumentType(); // получаем тип документа
        $documentId = $rootActivity->getDocumentId(); // получаем ID документа
        // получаем объект документа над которым выполняется БП (элемент сущности Компания)
        $documentService = CBPRuntime::GetRuntime(true)->getDocumentService();
        // $documentService = $this->workflow->GetService("DocumentService");

        // поля документа
        $documentFields =  $documentService->GetDocumentFields($documentType);
        // $arDocumentFields = $documentService->GetDocument($documentId);

        foreach ($documentFields as $key => $value) {
            if($key == 'UF_CRM_1718872462762'){ // поле номер ИНН
                $fieldValue = $documentService->getFieldValue($documentId, $key, $documentType);
                $this->log('значение поля Инн:'.' '.$fieldValue);
            }

            if($key == 'UF_CRM_TEST'){ // поле TEST
                $fieldValue = $documentService->getFieldValue($documentId, $key, $documentType);
                $this->log('значение поля TEST:'.' '.$fieldValue);
            }
        }*/

        return $errors;
    }

    /**
     * @param PropertiesDialog|null $dialog
     * @return array[]
     */
    public static function getPropertiesDialogMap(?PropertiesDialog $dialog = null): array
    {
        $map = [
            'Inn' => [
                'Name' => Loc::getMessage('CREATECOMPANYBYINN_ACTIVITY_FIELD_SUBJECT'),
                'FieldName' => 'inn',
                'Type' => FieldType::STRING,
                'Required' => true,
                'Options' => [],
            ],
        ];
        return $map;
    }




}
