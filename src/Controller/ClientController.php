<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use PhpOffice\PhpSpreadsheet\Reader\Csv;

use App\Service\AuthService;
use App\Entity\ClientEntity;
use App\Entity\ClientFamilyEntity;
use App\Entity\ClientCaseTypeEntity;
use App\Entity\ClientAssistanceTypeEntity;
use App\Entity\ClientLightingEntity;
use App\Entity\CityEntity;
use App\Entity\BarangayEntity;
use App\Entity\DeseaseEntity;
use App\Entity\BranchEntity;

use App\Form\ClientForm;

/**
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/ajax_form", name="client_ajax_form")
     */
    public function ajaxForm(Request $request, AuthService $authService): JsonResponse
    {

       $result = [ 'success' =>  true, 'msg' => ''];
       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}
       
       $em = $this->getDoctrine()->getManager();
       $formData = $request->request->all();
       $client = $em->getRepository(ClientEntity::class)->find(base64_decode($formData['id']));

       $formChoices = [
         'genders' => $authService->genders(),
         'patientRelationships' => $authService->patientRelationships(),
         'educationalAttainments' => $authService->educationalAttainments(),
         'civilStatuses' => $authService->civilStatuses(),
         'caseTypes' => $authService->caseTypes(),
         'assistantTypes' => $authService->assistantTypes(),
         'deseases' => $authService->deseases(),
         'treatments' => $authService->treatments()
       ];

       
       if(!$client) {
          $client = new ClientEntity();
       }
       $formOptions = array('action' => $formData['action'],'encoderId' => ($client->getEncoder() ? $client->getEncoder()->getId() : null), 'userData' =>  $this->get('session')->get('userData') ,'treatments' => $formChoices['treatments'] ,'deseases' => $formChoices['deseases'], 'genders' => $formChoices['genders'], 'assistantTypes' => $formChoices['assistantTypes'], 'caseTypes' => $formChoices['caseTypes'], 'civilStatuses' => $formChoices['civilStatuses'], 'educationalAttainments' => $formChoices['educationalAttainments'], 'patientRelationships' => $formChoices['patientRelationships'], 'branchId' => $authService->getUser()->getBranch()->getId());
       $form = $this->createForm(ClientForm::class, $client, $formOptions);
    
       $result['html'] = $this->renderView('Client/ajax_form.html.twig', [
            'page_title' => ($formData['action'] == 'n' ? 'New' : 'Update') . '  Client',
            'form' => $form->createView(),
            'action' => $formData['action'],
            'javascripts' => array('/js/client/ajax_form.js') ,
            'formChoices' => json_encode($formChoices),
            'client' => $client,
            'formChoicesInarray' => $formChoices
        ]);

       return new JsonResponse($result);
    }

    /**
     * @Route("/ajax_form_process", name="client_ajax_form_process")
     */
    public function ajaxFormProcess(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];


       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}

       if($request->getMethod() == "POST"){
         $formChoices = [
            'genders' => $authService->genders(),
            'patientRelationships' => $authService->patientRelationships(),
            'educationalAttainments' => $authService->educationalAttainments(),
            'civilStatuses' => $authService->civilStatuses(),
            'caseTypes' => $authService->caseTypes(),
            'assistantTypes' => $authService->assistantTypes(),
            'deseases' => $authService->deseases(),
            'treatments' => $authService->treatments()
          ];

         $clientForm = $request->request->get('client_form');
         
         $em = $this->getDoctrine()->getManager();
         $errors = $em->getRepository(ClientEntity::class)->validate($clientForm);

         if(!count($errors)){
            
            $client = $em->getRepository(ClientEntity::class)->find($clientForm['id']);
            
            if(!$client) {
               $client = new ClientEntity();
            }
     
            $formOptions = array('action' => $clientForm['action'],'encoderId' => ($client->getEncoder() ? $client->getEncoder()->getId() : null),  'userData' =>  $this->get('session')->get('userData'),'treatments' => $formChoices['treatments'] , 'deseases' => $formChoices['deseases'], 'genders' => $formChoices['genders'], 'assistantTypes' => $formChoices['assistantTypes'], 'caseTypes' => $formChoices['caseTypes'], 'civilStatuses' => $formChoices['civilStatuses'], 'educationalAttainments' => $formChoices['educationalAttainments'], 'patientRelationships' => $formChoices['patientRelationships'], 'branchId' => $authService->getUser()->getBranch()->getId());
            $form = $this->createForm(ClientForm::class, $client, $formOptions);


            switch($clientForm['action']) {
               case 'n': // new

                     $form->handleRequest($request);
      
                     if ($form->isValid()) {
                        
                        $em->persist($client);
                        $em->flush();

                        $this->processFamily($clientForm, $client, $em);
                        $this->processCaseTypes($clientForm, $client, $em);
                        $this->processAssistanceTypes($clientForm, $client, $em);
                        $this->processLightings($clientForm, $client, $em);

                        $result['msg'] = 'Record successfully added to record.';
                     } else {
      
                        $result['error'] = 'Ooops something went wrong please try again later.';
                    

                        
                       
                     }
      
                     break;
      
               case 'u': // update

                     $form->handleRequest($request);
                     if ($form->isValid()) {
                        
                        $em->persist($client);
                        $em->flush();

                        $this->processRemove($clientForm, $client, $em);
                        $this->processFamily($clientForm, $client, $em);
                        $this->processCaseTypes($clientForm, $client, $em);
                        $this->processAssistanceTypes($clientForm, $client, $em);
                        $this->processLightings($clientForm, $client, $em);

                        $result['msg'] = 'Record successfully updated.';
                     } else {
                       
                        $result['error'] = 'Ooops something went wrong please try again later.';

                     }
      
                     break;
      
               case 'd': // delete
                     $form->handleRequest($request);
                     if ($form->isValid()) {
                          
                        $client->setIsDeleted(true);
                        $em->flush();
      
                        $result['msg'] = 'Record successfully deleted.';
      
                     } else {
      
                        $result['error'] = 'Ooops something went wrong please try again later.';
                     }
      
                     break;
            }
        
         } else {

             $result['success'] = false;
             $result['msg'] = '';
             foreach ($errors as $error) {
                 
                 $result['msg'] .= $error;
             }
         }
     } else {

         $result['error'] = 'Ooops something went wrong please try again later.';
     }
    
       return new JsonResponse($result);
    }

   /**
    * @Route("/ajax_delete", name="client_ajax_delete")
    */
   public function ajaxDelete(Request $request, AuthService $authService): JsonResponse
   {

      $result = [ 'success' =>  true, 'msg' => ''];

      if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}
      
      if($request->getMethod() == "POST"){
        
         $id = $request->request->get('id');
         $em  = $this->getDoctrine()->getManager();
         $client = $em->getRepository(ClientEntity::class)->find(base64_decode($id));

         if(!is_null($client)){

            $client->setIsDeleted(true);
            $em->flush();

            $result['msg'] = 'Client successfully deleted';
 
         } else {

            $result['success'] = false;
            $result['msg'] = 'Something went wrong please try again.';

         }
      }

      return new JsonResponse($result);

   }

      /**
    * @Route("", name="client_index")
    */
   public function index(Request $request, AuthService $authService)
   {
      
      if(!$authService->isLoggedIn()) return $authService->redirectToLogin();
      if(!$authService->isUserHasAccesses(array('Client'))) return $authService->redirectToHome();
      
      $page_title = ' Client'; 
      return $this->render('Client/index.html.twig', [ 
         'page_title' => $page_title, 
         'javascripts' => array('plugins/datatables/jquery.dataTables.js','/js/client/index.js') ]
      );
   }



   /**
    * @Route("/autocomplete", name="client_autocomplete")
    */
   public function autocompleteAction(Request $request) {

      return new JsonResponse(array(
         'query' => 'clients',
         'suggestions' => $this->getDoctrine()->getManager()->getRepository(ClientEntity::class)->autocompleteSuggestions($request->query->all(), $this->get('session')->get('userData'))
      ));
   }

   /**
    * @Route("/ajax_list", name="client_ajax_list")
    */
    public function ajax_listAction(Request $request, AuthService $authService) {

      $get = $request->query->all();

      $result = array(
          "draw" => intval($get['draw']),
          "recordsTotal" => 0,
          "recordsFiltered" => 0,
          "data" => array()
      );

      if($authService->isLoggedIn()) {
          $result = $this->getDoctrine()->getManager()->getRepository(ClientEntity::class)->ajax_list($get, $this->get('session')->get('userData'));
      }

      return new JsonResponse($result);
   }

   
   private function processFamily($form, $client, $em){


      if(isset($form['familyMember'])){
         
         foreach ($form['familyMember'] as $key => $i) {

            if(isset($i['client_family_id'])){
               $clientFamily = $em->getRepository(ClientFamilyEntity::class)->find(base64_decode($i['client_family_id']));
            } else {

               $clientFamily =  new ClientFamilyEntity();
            }   

            $clientFamily->setFirstName($i['firstName']);
            $clientFamily->setMiddleName($i['middleName']);
            $clientFamily->setLastName($i['lastName']);
            $clientFamily->setGender($i['gender']);
            $clientFamily->setFamilyRole($i['familyRole']);
            $clientFamily->setAge($i['age']);
            $clientFamily->setCivilStatus($i['civilStatus']);
            $clientFamily->setEducationAttainment($i['educationalAttainment']);
            $clientFamily->setOccupation($i['occupation']);
            $clientFamily->setMonthlyIncome($i['monthlyIncome']);
            $clientFamily->setClient($client);
            $em->persist($clientFamily);
            $em->flush();
         }
         
      }
   }

   private function processCaseTypes($form, $client, $em){


      if(isset($form['caseType'])){
         foreach ($form['caseType'] as $key => $i) {

            $caseType = $em->getRepository(ClientCaseTypeEntity::class)->findOneBy(array(
               'client' => $client->getId(),
               'description' => $i,
               'isDeleted' => 0
            ));

            if(!$caseType){
               $caseType =  new ClientCaseTypeEntity();
               $caseType->setDescription($i);
               $caseType->setClient($client);
            }

            $em->persist($caseType);
            $em->flush();
         }
         
      }
   }

   private function processAssistanceTypes($form, $client, $em){

      if(isset($form['assistantType'])){
         foreach ($form['assistantType'] as $key => $i) {

            $assistanceType = $em->getRepository(ClientAssistanceTypeEntity::class)->findOneBy(array(
               'client' => $client->getId(),
               'description' => $i,
               'isDeleted' => 0
            ));

            if(!$assistanceType){
               $assistanceType =  new ClientAssistanceTypeEntity();
               $assistanceType->setDescription($i);
               $assistanceType->setClient($client);
            }

            $em->persist($assistanceType);
            $em->flush();
         }
         
      }
   }

   private function processLightings($form, $client, $em){

      if(isset($form['lightning'])){
         foreach ($form['lightning'] as $key => $i) {

            $lighting = $em->getRepository(ClientLightingEntity::class)->findOneBy(array(
               'client' => $client->getId(),
               'description' => $i,
               'isDeleted' => 0
            ));

            if(!$lighting){
               $lighting =  new ClientLightingEntity();
               $lighting->setDescription($i);
               $lighting->setClient($client);
            }

            $em->persist($lighting);
            $em->flush();
         }
         
      }
   }

   
   /**
   * @Route("/ajax_barangay", name="client_barangay_ajax")
   */
   public function ajax_client_barangay(Request $request, AuthService $authService) {

      $results= [];
      $results['success'] = true;

      $results['data']  = $this->getDoctrine()->getManager()->getRepository(ClientEntity::class)->list_per_barangay( $this->get('session')->get('userData'));

      return new JsonResponse($results);
   }

   /**
   * @Route("/ajax_top_barangay", name="client_top_barangay_ajax")
   */
  public function ajax_client_top_barangay(Request $request, AuthService $authService) {

   $results= [];
   $results['success'] = true;

   $results['data']  = $this->getDoctrine()->getManager()->getRepository(ClientEntity::class)->list_top_barangay( $this->get('session')->get('userData'));

   return new JsonResponse($results);
}

   /**
   * @Route("/ajax_desease", name="client_desease_ajax")
   */
  public function ajax_client_desease(Request $request, AuthService $authService) {

   $results= [];
   $results['success'] = true;

   $results['data']  = $this->getDoctrine()->getManager()->getRepository(ClientEntity::class)->list_per_desease( $this->get('session')->get('userData'));

   return new JsonResponse($results);
}

   private function processRemove($form, $client, $em){
      $familyIds = isset($form['client_family_ids']) ? $form['client_family_ids'] : []; 
      $caseTypes = isset($form['caseType']) ? $form['caseType'] : []; 
      $assistanceTypes = isset($form['assistanceType']) ? $form['assistanceType'] : [];
      $lightnings = isset($form['lightning']) ? $form['lightning'] : [];


      //delete family
      foreach($client->getClientFamilies() as $family){
         if(!in_array($family->getIdEncoded(), $familyIds)){
            $family->setIsDeleted(true);
            $em->flush();
         }
      } 

      foreach($client->getClientCaseTypes() as $caseType){
         if(!in_array($caseType->getDescription(), $caseTypes)){
            $caseType->setIsDeleted(true);
            $em->flush();
         }
      } 

      foreach($client->getClientAssistanceTypes() as $assistantType){
         if(!in_array($assistantType->getDescription(), $assistanceTypes)){
            $assistantType->setIsDeleted(true);
            $em->flush();
         }
      } 

      foreach($client->getClientLightings() as $lighting){
         if(!in_array($lighting->getDescription(), $lightnings)){
            $lighting->setIsDeleted(true);
            $em->flush();
         }
      } 
   }

           /**
     * @Route("/import", name="client_import")
     */
    public function import(Request $request, AuthService $authService)
    {
        if(!$authService->isLoggedIn()) return $authService->redirectToLogin();
        if(!$authService->isUserHasAccesses(array('Client Import'))) return $authService->redirectToHome();

        $userData = $this->get('session')->get('userData');
        
        if($request->getMethod() == 'POST'){
            $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            
            if(isset($_FILES['field']['name']['file']) && in_array($_FILES['field']['type']['file'], $file_mimes)) {

                $reader = new Csv();
                $spreadsheet = $reader->load($_FILES['field']['tmp_name']['file']);
 
                $sheetData = $spreadsheet->getActiveSheet()->toArray();
                array_shift($sheetData);

                $em = $this->getDoctrine()->getManager();

                $city = $em->getRepository(CityEntity::class)->findOneBy(['description' => 'city of tanauan']);

                foreach($sheetData as $data){
                     $intakeDate = $data[0];
                     $name = $data[1];
                     $gender = $data[2];
                     $address = $data[3];
                     $contactNo = $data[4];
                     $disease = $data[5];
                     $worker = $data[6];
                    
                 
                     $branch = $em->getReference(BranchEntity::class, base64_decode($userData['branchId']));        
                 //    $client = $em->getRepository(ClientEntity::class)->findOneBy(['firstName' => $name, 'isDeleted' => 0, 'branch' => $branch]);
                     $desease = $em->getRepository(DeseaseEntity::class)->findOneBy(['description' => $disease, 'isDeleted' => 0, 'branch' => $branch]);

                     if(!is_null($disease)){
                        
                        if(is_null($desease)){
                        
                           $desease = new DeseaseEntity();
                           $desease->setCode($disease);
                           $desease->setDescription($disease);
                           $desease->setBranch($branch);
                           $em->persist($desease);
                           $em->flush();
                        }
                     }

                   //  if(is_null($client)){
                        $address = $em->getRepository(BarangayEntity::class)->findOneBy(['description' => $address, 'city' => $city]);

                        $client = new ClientEntity();
                        $client->setFirstName($name);
                        $client->setIntakeDate(new \DateTime($intakeDate));
                        $client->setGender($gender);
                        $client->setBarangay($address);
                        $client->setBarangayAddress($address);
                        $client->setContactNo($contactNo);
                        $client->setDesease($desease);
                        $client->setWorker($worker);
                        $client->setBranch($branch);
                        $em->persist($client);
                        $em->flush();

                   //  }

                     if(!is_null($disease)){
                        

                           $ca = new ClientAssistanceTypeEntity();
                           $ca->setDescription('FAM');
                           $ca->setClient($client);
                           $em->persist($ca);
                           $em->flush();
                        
                     }
                
                }

                $this->get('session')->getFlashBag()->set('success_messages', 'Client successfully import.');

            } else {

                $this->get('session')->getFlashBag()->set('error_messages', 'Please put a valid CSV file.');

            }

        } else {

            $this->get('session')->getFlashBag()->set('error_messages', 'Unauthorized request please call a system administrator.');

        }


       return $this->redirect($this->generateUrl('client_index'),302);
    }

}
