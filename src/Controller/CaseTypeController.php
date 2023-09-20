<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Service\AuthService;
use App\Entity\CaseTypeEntity;
use App\Form\CaseTypeForm;

/**
 * @Route("/case_type")
 */
class CaseTypeController extends AbstractController
{
    /**
     * @Route("/ajax_form", name="case_type_ajax_form")
     */
    public function ajaxForm(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];
       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}
       
       $em = $this->getDoctrine()->getManager();
       $formData = $request->request->all();
       $caseType = $em->getRepository(CaseTypeEntity::class)->find(base64_decode($formData['id']));
       
       if(!$caseType) {
          $caseType = new CaseTypeEntity();
       }

       $formOptions = array('action' => $formData['action'], 'branchId' => $authService->getUser()->getBranch()->getIdEncoded());
       $form = $this->createForm(CaseTypeForm::class, $caseType, $formOptions);
    
       $result['html'] = $this->renderView('CaseType/ajax_form.html.twig', [
            'page_title' => ($formData['action'] == 'n' ? 'New' : 'Update') . '  Case Type',
            'form' => $form->createView(),
            'action' => $formData['action'],
            'javascripts' => array('/js/case_type/ajax_form.js') 
        ]);

       return new JsonResponse($result);
    }

    /**
     * @Route("/ajax_form_process", name="case_type_ajax_form_process")
     */
    public function ajaxFormProcess(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];

       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}

       if($request->getMethod() == "POST"){
         $case_typeForm = $request->request->get('case_type_form');
         
         $em = $this->getDoctrine()->getManager();
         $errors = $em->getRepository(CaseTypeEntity::class)->validate($case_typeForm);

         if(!count($errors)){
            
            $caseType = $em->getRepository(CaseTypeEntity::class)->find($case_typeForm['id']);
            
            if(!$caseType) {
               $caseType = new CaseTypeEntity();
            }
     
            $formOptions = array('action' => $case_typeForm['action'], 'branchId' => $authService->getUser()->getBranch()->getIdEncoded());
            $form = $this->createForm(CaseTypeForm::class, $caseType, $formOptions);


            switch($case_typeForm['action']) {
               case 'n': // new

                     $form->handleRequest($request);
      
                     if ($form->isValid()) {
                        
                        $em->persist($caseType);
                        $em->flush();
   
                        $result['msg'] = 'Record successfully added to record.';
                     } else {
      
                        $result['error'] = 'Ooops something went wrong please try again later.';
                     }
      
                     break;
      
               case 'u': // update

                     $form->handleRequest($request);
                     if ($form->isValid()) {
      
                        $em->persist($caseType);
                        $em->flush();
                        $result['msg'] = 'Record successfully updated.';
                     } else {
                       
                        $result['error'] = 'Ooops2 something went wrong please try again later.';
                     }
      
                     break;
      
               case 'd': // delete
                     $form->handleRequest($request);
                     if ($form->isValid()) {
                          
                        $caseType->setIsDeleted(true);
                        $em->flush();
      
                        $result['msg'] = 'Record successfully deleted.';
      
                     } else {
      
                        $result['error'] = 'Ooops 3something went wrong please try again later.';
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
    * @Route("", name="case_type_index")
    */
   public function index(Request $request, AuthService $authService)
   {
      
      if(!$authService->isLoggedIn()) return $authService->redirectToLogin();
      if(!$authService->isUserHasAccesses(array('CMS Case Type'))) return $authService->redirectToHome();
      
      $page_title = ' CaseType'; 
      return $this->render('CaseType/index.html.twig', [ 
         'page_title' => $page_title, 
         'javascripts' => array('plugins/datatables/jquery.dataTables.js','/js/case_type/index.js') ]
      );
   }



   /**
    * @Route("/autocomplete", name="case_type_autocomplete")
    */
   public function autocompleteAction(Request $request) {

      return new JsonResponse(array(
         'query' => 'caseTypes',
         'suggestions' => $this->getDoctrine()->getManager()->getRepository(CaseTypeEntity::class)->autocomplete_suggestions($request->query->all(), $this->get('session')->get('userData'))
      ));
   }

   /**
    * @Route("/ajax_list", name="case_type_ajax_list")
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
          $result = $this->getDoctrine()->getManager()->getRepository(CaseTypeEntity::class)->ajax_list($get, $this->get('session')->get('userData'));
      }

      return new JsonResponse($result);
   }

}
