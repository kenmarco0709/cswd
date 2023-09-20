<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Service\AuthService;
use App\Entity\DeseaseEntity;
use App\Form\DeseaseForm;

/**
 * @Route("/desease")
 */
class DeseaseController extends AbstractController
{
    /**
     * @Route("/ajax_form", name="desease_ajax_form")
     */
    public function ajaxForm(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];
       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}
       
       $em = $this->getDoctrine()->getManager();
       $formData = $request->request->all();
       $desease = $em->getRepository(DeseaseEntity::class)->find(base64_decode($formData['id']));
       
       if(!$desease) {
          $desease = new DeseaseEntity();
       }

       $formOptions = array('action' => $formData['action'], 'branchId' => $authService->getUser()->getBranch()->getIdEncoded());
       $form = $this->createForm(DeseaseForm::class, $desease, $formOptions);
    
       $result['html'] = $this->renderView('Desease/ajax_form.html.twig', [
            'page_title' => ($formData['action'] == 'n' ? 'New' : 'Update') . '  Disease',
            'form' => $form->createView(),
            'action' => $formData['action'],
            'javascripts' => array('/js/desease/ajax_form.js') 
        ]);

       return new JsonResponse($result);
    }

    /**
     * @Route("/ajax_form_process", name="desease_ajax_form_process")
     */
    public function ajaxFormProcess(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];

       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}

       if($request->getMethod() == "POST"){
         $deseaseForm = $request->request->get('desease_form');
         
         $em = $this->getDoctrine()->getManager();
         $errors = $em->getRepository(DeseaseEntity::class)->validate($deseaseForm);

         if(!count($errors)){
            
            $desease = $em->getRepository(DeseaseEntity::class)->find($deseaseForm['id']);
            
            if(!$desease) {
               $desease = new DeseaseEntity();
            }
     
            $formOptions = array('action' => $deseaseForm['action'], 'branchId' => $authService->getUser()->getBranch()->getIdEncoded());
            $form = $this->createForm(DeseaseForm::class, $desease, $formOptions);


            switch($deseaseForm['action']) {
               case 'n': // new

                     $form->handleRequest($request);
      
                     if ($form->isValid()) {
                        
                        $em->persist($desease);
                        $em->flush();
   
                        $result['msg'] = 'Record successfully added to record.';
                     } else {
      
                        $result['error'] = 'Ooops something went wrong please try again later.';
                     }
      
                     break;
      
               case 'u': // update

                     $form->handleRequest($request);
                     if ($form->isValid()) {
      
                        $em->persist($desease);
                        $em->flush();
                        $result['msg'] = 'Record successfully updated.';
                     } else {
                       
                        $result['error'] = 'Ooops2 something went wrong please try again later.';
                     }
      
                     break;
      
               case 'd': // delete
                     $form->handleRequest($request);
                     if ($form->isValid()) {
                          
                        $desease->setIsDeleted(true);
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
    * @Route("", name="desease_index")
    */
   public function index(Request $request, AuthService $authService)
   {
      
      if(!$authService->isLoggedIn()) return $authService->redirectToLogin();
      if(!$authService->isUserHasAccesses(array('CMS Desease'))) return $authService->redirectToHome();
      
      $page_title = ' Disease'; 
      return $this->render('Desease/index.html.twig', [ 
         'page_title' => $page_title, 
         'javascripts' => array('plugins/datatables/jquery.dataTables.js','/js/desease/index.js') ]
      );
   }



   /**
    * @Route("/autocomplete", name="desease_autocomplete")
    */
   public function autocompleteAction(Request $request) {

      return new JsonResponse(array(
         'query' => 'deseases',
         'suggestions' => $this->getDoctrine()->getManager()->getRepository(DeseaseEntity::class)->autocomplete_suggestions($request->query->all(), $this->get('session')->get('userData'))
      ));
   }

   /**
    * @Route("/ajax_list", name="desease_ajax_list")
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
          $result = $this->getDoctrine()->getManager()->getRepository(DeseaseEntity::class)->ajax_list($get, $this->get('session')->get('userData'));
      }

      return new JsonResponse($result);
   }

}
