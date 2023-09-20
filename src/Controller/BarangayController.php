<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Service\AuthService;
use App\Entity\BarangayEntity;
use App\Form\BarangayForm;




/**
 * @Route("/barangay")
 */
class BarangayController extends AbstractController
{
    /**
     * @Route("/ajax_form", name="barangay_ajax_form")
     */
    public function ajaxForm(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];
       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}
       
       $em = $this->getDoctrine()->getManager();
       $formData = $request->request->all();
       $barangay = $em->getRepository(BarangayEntity::class)->find(base64_decode($formData['id']));
       
       if(!$barangay) {
          $barangay = new BarangayEntity();
       }

       $formOptions = array('action' => $formData['action']);
       $form = $this->createForm(BarangayForm::class, $barangay, $formOptions);
    
       $result['html'] = $this->renderView('Barangay/ajax_form.html.twig', [
            'page_title' => ($formData['action'] == 'n' ? 'New' : 'Update') . '  Barangay',
            'form' => $form->createView(),
            'action' => $formData['action'],
            'javascripts' => array('/js/barangay/ajax_form.js') 
        ]);

       return new JsonResponse($result);
    }

    /**
     * @Route("/ajax_form_process", name="barangay_ajax_form_process")
     */
    public function ajaxFormProcess(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];

       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}

       if($request->getMethod() == "POST"){
         $barangayForm = $request->request->get('barangay_form');
         
         $em = $this->getDoctrine()->getManager();
         $errors = $em->getRepository(BarangayEntity::class)->validate($barangayForm);

         if(!count($errors)){
            
            $barangay = $em->getRepository(BarangayEntity::class)->find($barangayForm['id']);
            
            if(!$barangay) {
               $barangay = new BarangayEntity();
            }
     
            $formOptions = array('action' => $barangayForm['action']);
            $form = $this->createForm(BarangayForm::class, $barangay, $formOptions);


            switch($barangayForm['action']) {
               case 'n': // new

                     $form->handleRequest($request);
      
                     if ($form->isValid()) {
                        
                        $em->persist($barangay);
                        $em->flush();
   
                        $result['msg'] = 'Record successfully added to record.';
                     } else {
      
                        $result['error'] = 'Ooops something went wrong please try again later.';
                     }
      
                     break;
      
               case 'u': // update

                     $form->handleRequest($request);
                     if ($form->isValid()) {
      
                        $em->persist($barangay);
                        $em->flush();
                        $result['msg'] = 'Record successfully updated.';
                     } else {
                       
                        $result['error'] = 'Ooops2 something went wrong please try again later.';
                     }
      
                     break;
      
               case 'd': // delete
                     $form->handleRequest($request);
                     if ($form->isValid()) {
                          
                        $barangay->setIsDeleted(true);
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
    * @Route("", name="barangay_index")
    */
   public function index(Request $request, AuthService $authService)
   {
      
      if(!$authService->isLoggedIn()) return $authService->redirectToLogin();
      if(!$authService->isUserHasAccesses(array('CMS Barangay'))) return $authService->redirectToHome();
      
      $page_title = ' Barangay'; 
      return $this->render('Barangay/index.html.twig', [ 
         'page_title' => $page_title, 
         'javascripts' => array('plugins/datatables/jquery.dataTables.js','/js/barangay/index.js') ]
      );
   }



   /**
    * @Route("/autocomplete", name="barangay_autocomplete")
    */
   public function autocompleteAction(Request $request) {

      return new JsonResponse(array(
         'query' => 'operational_detachements',
         'suggestions' => $this->getDoctrine()->getManager()->getRepository(BarangayEntity::class)->autocompleteSuggestions($request->query->all(), $this->get('session')->get('userData'))
      ));
   }

   /**
    * @Route("/ajax_list", name="barangay_ajax_list")
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
          $result = $this->getDoctrine()->getManager()->getRepository(BarangayEntity::class)->ajax_list($get, $this->get('session')->get('userData'));
      }

      return new JsonResponse($result);
   }

}
