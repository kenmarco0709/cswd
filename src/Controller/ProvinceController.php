<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Service\AuthService;
use App\Entity\ProvinceEntity;
use App\Form\ProvinceForm;

/**
 * @Route("/province")
 */
class ProvinceController extends AbstractController
{
    /**
     * @Route("/ajax_form", name="province_ajax_form")
     */
    public function ajaxForm(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];
       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}
       
       $em = $this->getDoctrine()->getManager();
       $formData = $request->request->all();
       $province = $em->getRepository(ProvinceEntity::class)->find(base64_decode($formData['id']));
       
       if(!$province) {
          $province = new ProvinceEntity();
       }

       $formOptions = array('action' => $formData['action']);
       $form = $this->createForm(ProvinceForm::class, $province, $formOptions);
    
       $result['html'] = $this->renderView('Province/ajax_form.html.twig', [
            'page_title' => ($formData['action'] == 'n' ? 'New' : 'Update') . '  Province',
            'form' => $form->createView(),
            'action' => $formData['action'],
            'javascripts' => array('/js/province/ajax_form.js') 
        ]);

       return new JsonResponse($result);
    }

    /**
     * @Route("/ajax_form_process", name="province_ajax_form_process")
     */
    public function ajaxFormProcess(Request $request, AuthService $authService): JsonResponse
    {
       
       $result = [ 'success' =>  true, 'msg' => ''];

       if(!$authService->isLoggedIn()) { $result['success'] = false; $result['msg'] = 'Unauthorized access please call a system administrator.';}

       if($request->getMethod() == "POST"){
         $provinceForm = $request->request->get('province_form');
         
         $em = $this->getDoctrine()->getManager();
         $errors = $em->getRepository(ProvinceEntity::class)->validate($provinceForm);

         if(!count($errors)){
            
            $province = $em->getRepository(ProvinceEntity::class)->find($provinceForm['id']);
            
            if(!$province) {
               $province = new ProvinceEntity();
            }
     
            $formOptions = array('action' => $provinceForm['action']);
            $form = $this->createForm(ProvinceForm::class, $province, $formOptions);


            switch($provinceForm['action']) {
               case 'n': // new

                     $form->handleRequest($request);
      
                     if ($form->isValid()) {
                        
                        $em->persist($province);
                        $em->flush();
   
                        $result['msg'] = 'Record successfully added to record.';
                     } else {
      
                        $result['error'] = 'Ooops something went wrong please try again later.';
                     }
      
                     break;
      
               case 'u': // update

                     $form->handleRequest($request);
                     if ($form->isValid()) {
      
                        $em->persist($province);
                        $em->flush();
                        $result['msg'] = 'Record successfully updated.';
                     } else {
                       
                        $result['error'] = 'Ooops2 something went wrong please try again later.';
                     }
      
                     break;
      
               case 'd': // delete
                     $form->handleRequest($request);
                     if ($form->isValid()) {
                          
                        $province->setIsDeleted(true);
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
    * @Route("", name="province_index")
    */
   public function index(Request $request, AuthService $authService)
   {
      
      if(!$authService->isLoggedIn()) return $authService->redirectToLogin();
      if(!$authService->isUserHasAccesses(array('CMS Province'))) return $authService->redirectToHome();
      
      $page_title = ' Province'; 
      return $this->render('Province/index.html.twig', [ 
         'page_title' => $page_title, 
         'javascripts' => array('plugins/datatables/jquery.dataTables.js','/js/province/index.js') ]
      );
   }



   /**
    * @Route("/autocomplete", name="province_autocomplete")
    */
   public function autocompleteAction(Request $request) {

      return new JsonResponse(array(
         'query' => 'operational_detachements',
         'suggestions' => $this->getDoctrine()->getManager()->getRepository(ProvinceEntity::class)->autocomplete_suggestions($request->query->all(), $this->get('session')->get('userData'))
      ));
   }

   /**
    * @Route("/ajax_list", name="province_ajax_list")
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
          $result = $this->getDoctrine()->getManager()->getRepository(ProvinceEntity::class)->ajax_list($get, $this->get('session')->get('userData'));
      }

      return new JsonResponse($result);
   }

}
