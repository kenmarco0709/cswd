<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\UserEntity;

Class AuthService {

    private $em;
    private $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container) {

        $this->em = $em;
        $this->container = $container;
    }

    public function isLoggedIn($requestUri = true) {

        $session = $this->container->get('session');

        if($session->has('userData')) {
            return true;
        } else {
            if($requestUri) {
                $req_uri = $_SERVER['REQUEST_URI'];
                if($req_uri !== $this->container->get('router')->generate('auth_login') &&
                    $req_uri !== $this->container->get('router')->generate('auth_logout') &&
                    strpos($req_uri, 'ajax') === false) $session->set('req_uri', $req_uri);
            }
            return false;
        }
    }

    /**
     * Redirects to login page
     */
    public function redirectToLogin() {

        return new RedirectResponse($this->container->get('router')->generate('auth_login'), 302);
    }

      /**
     * Get user
     */
    public function getUser() {

        $userData = $this->container->get('session')->get('userData');
        return $this->em->getRepository(UserEntity::class)->find($userData['id']);
    }

    // Original PHP code by Chirp Internet: www.chirp.com.au
    public function better_crypt($input, $rounds = 7)
    {
        $salt = "";
        $salt_chars = array_merge(range('A','Z'), range('a','z'), range(0,9));
        for($i=0; $i < 22; $i++) {
            $salt .= $salt_chars[array_rand($salt_chars)];
        }
        return crypt($input, sprintf('$2a$%02d$', $rounds) . $salt);
    }

    /**
     * Get user types
     */
    public function getUserTypes() {

        return array(
            'Admin',
            'Worker',
            'Encoder'
        );
    }

    /**
     * Get sms types
     */
    public function getSmsTypes() {

        return array(
            'Pending - Remaining Balance'
        );
    }

     /**
     * Get caseTypes
     */
    public function caseTypes(){
        return [
            'Old' => 'Old',
            'New' => 'New',
            'SC' => 'SC',
            'CDW' => 'CDW',
            'OSY' => 'OSY',
            'Solo Parent' => 'Solo Parent',
            'Women' => 'Women',
            'FHONA' => 'FHONA',
            'Youth' => 'Youth',
            'ERPAT' => 'ERPAT',
            'PWD/PSN' => 'PWD/PSN',
            'Children' => 'Children',
            'Others' => 'Others',
            '4Ps' => '4Ps',
           
        ];
    } 

    public function patientRelationships(){
        return [
            'Spouse' => 'Spouse',
            'Grandfather/Grandmother' => 'Grandfather/Grandmother',
            'Grandson/Grandaughter' => 'Grandson/Grandaughter',
            'Nephew/Niece' => 'Nephew/Niece',
            'Foster Child' => 'Foster Child',
            'Stepson/Stepdaughter' => 'Stepson/Stepdaughter',
            'Self' => 'Self',
            'Mother/Father' => 'Mother/Father',
            'Live in Partner' => 'Live in Partner',
            'Brother/Sister' => 'Brother/Sister',
            'Son/Daughter' => 'Son/Daughter',
            'Other' => 'Other',
        ];
    }

    public function civilStatuses(){
        return [
            'Single' => 'Single',
            'Married' => 'Married',
            'Widowed' => 'Widowed',
            'Divorce' => 'Divorce',
            'Others' => 'Others'
        ];
    }

    public function educationalAttainments(){
        return [
            'Elementary' => 'Elementary',
            'HighSchool' => 'HighSchool',
            'College' => 'College',
            'Doctoral' => 'Doctoral',
            'Masteral' => 'Masteral',
            'Technical/Vocational' => 'Technical/Vocational',
            'HIGH SCHOOL UNDERGRADUATE' =>  'HIGH SCHOOL UNDERGRADUATE',
            'SENIOR HIGH SCHOOL UNDERGRADUATE' => 'SENIOR HIGH SCHOOL UNDERGRADUATE',
            'COLLEGE UNDERGRADUATE' => 'COLLEGE UNDERGRADUATE' ,
            'ELEMENTARY UNDERGRADUATE' => 'ELEMENTARY UNDERGRADUATE'
        ];
    }

    public function genders(){
        return [
            'Male' => 'Male',
            'Female' => 'Female'
        ];
    }

    public function assistantTypes(){
        return [
            'Tulong Hatid' => 'Tulong Hatid',
            'FAB' => 'FAB',
            'AHC' => 'AHC',
            'FAM' => 'FAM',
            'Social Case Study' => 'Social Case Study'
        ];
    }

    public function deseases(){
        return [
            'Chemo and Dialysis' => 'Chemo and Dialysis',
            'Cancer' => 'Cancer', 
            'TB' => 'TB',
            'Catarrh' => 'Catarrh',
            'Chicken Pox' => 'Chicken Pox',
            'Dementia' => 'Dementia',
            'Epilepsy' => 'Epilepsy',
            'Gallstones' => 'Galstone',
            'Gout' => 'Gout',
            'Heart Failure' => 'Heart Failure',
            'Stroke' => 'Stroke'
        ];
    }

    public function treatments(){
        return [
            'Chemo and Dialysis' => 'Chemo and Dialysis',
            'Non - Chemo and Dialysis' => 'Non - Chemo and Dialysis', 
        ];
    }

    public function getAccesses() {

        return array(
            array('label' => 'Dashboard', 'description' => 'Dashboard', 'children' => array(
             
            )),
            // array('label' => 'Report', 'description' => 'Report', 'children' => array(
            //     array('label' => 'Consumption', 'description' => 'Report Consumption'),
            //     array('label' => 'Income', 'description' => 'Report Income'),
            //     array('label' => 'Billing', 'description' => 'Report Billing')

            // )),
            // array('label' => 'Company', 'description' => 'Company', 'children' => array(
            //     array('label' => 'Company View', 'description' => 'Company View', 'children' => array(
            //         array('label' => 'User', 'description' => 'Company View User', 'children' => array(
            //             array('label' => 'New', 'description' => 'Company View User New'),
            //             array('label' => 'Update', 'description' => 'Company View User Update'),
            //             array('label' => 'Delete', 'description' => 'Company View User Delete'),
            //         )),
            //         array('label' => 'Branch', 'description' => 'Company View Branch', 'children' => array(
            //             array('label' => 'New', 'description' => 'Company View Branch New'),
            //             array('label' => 'Update', 'description' => 'Company View Branch Update'),
            //             array('label' => 'Delete', 'description' => 'Company View Branch Delete'),
            //         )),
            //         array('label' => 'Access', 'description' => 'Company View Access', 'children' => array(
            //             array('label' => 'Update', 'description' => 'Company View Access'),
            //             array('label' => 'Update', 'description' => 'Company View Access Form'),
            //         )),
            //         array('label' => 'Sms', 'description' => 'Company View Sms', 'children' => array(
            //             array('label' => 'New', 'description' => 'Company View Sms New'),
            //             array('label' => 'Update', 'description' => 'Company View Sms Update'),
            //             array('label' => 'Delete', 'description' => 'Company View Sms Delete'),
            //         )),
            //     ))
            // )),
            array('label' => 'Client', 'description' => 'Client', 'children' => array(
                array('label' => 'New', 'description' => 'Client New'),
                array('label' => 'Update', 'description' => 'Client Update'),
                array('label' => 'Delete', 'description' => 'Client Delete'),
                array('label' => 'Import', 'description' => 'Client Import'),

            )),
            array('label' => 'Report', 'description' => 'Report', 'children' => array(
                array('label' => 'Intake Sheet', 'description' => 'Report Intake Sheet')
            )),
            array('label' => 'CMS', 'description' => 'CMS', 'children' => array(
                array('label' => 'Case Type', 'description' => 'CMS Case Type', 'children' => array(
                    array('label' => 'New', 'description' => 'CMS Case Type New'),
                    array('label' => 'Update', 'description' => 'CMS Case Type Update'),
                    array('label' => 'Delete', 'description' => 'CMS Case Type Delete'),
                )),
                array('label' => 'Desease', 'description' => 'CMS Desease', 'children' => array(
                    array('label' => 'New', 'description' => 'CMS Desease New'),
                    array('label' => 'Update', 'description' => 'CMS Desease Update'),
                    array('label' => 'Delete', 'description' => 'CMS Desease Delete'),
                ))
            )),
            array('label' => 'Settings', 'description' => 'Settings', 'children' => array(
                array('label' => 'Variable', 'description' => 'Settings Branch Variable', 'children' => array(
                    array('label' => 'New', 'description' => 'Settings Branch Variable New'),
                    array('label' => 'Update', 'description' => 'Settings Branch Variable Update'),
                    array('label' => 'Delete', 'description' => 'Settings Branch Variable Delete'),
                )),
            )),
        );
    }

     /**
     * Redirects to home page
     */
    public function redirectToHome() {

        $userData = $this->container->get('session')->get('userData');
        
        return new RedirectResponse($this->container->get('router')->generate('dashboard_index'), 302);
    }

     /**
     * Checks if the user has the ess
     */
    public function isUserHasAccesses($accessDescriptions, $hasErrorMsg=true, $matchCtr=false) {
        $session = $this->container->get('session');
        $userData = $session->get('userData');


        if($userData['type'] === 'Super Admin') {
            return true;
        } else {
            if($matchCtr) {
                $accessCtr = 0;
                foreach($accessDescriptions as $accessDescription) if(in_array($accessDescription, $userData['accesses'])) $accessCtr++;
                $hasAccess = count($accessDescriptions) === $accessCtr;
                if(!$hasAccess) {
                    if($hasErrorMsg) {
                        $session->getFlashBag()->set('error_messages', "You don't have the right to access the page. Please contact the administrator.");
                    }
                    return false;
                } else {
                    return true;
                }
            } else {
                foreach($accessDescriptions as $accessDescription) if(in_array($accessDescription, $userData['accesses'])) return true;
                if($hasErrorMsg) $session->getFlashBag()->set('error_messages', "You don't have the right to access the page. Please contact the administrator.");
                return false;
            }
        }
    }
    
    /**
     * getTimeago
     */
    function timeAgo( $time )
    {
        $time_difference = time() - strtotime($time);

        if( $time_difference < 1 ) { return 'less than 1 second ago'; }
        $condition = array( 12 * 30 * 24 * 60 * 60 =>  'year',
                    30 * 24 * 60 * 60       =>  'month',
                    24 * 60 * 60            =>  'day',
                    60 * 60                 =>  'hour',
                    60                      =>  'minute',
                    1                       =>  'second'
        );

        foreach( $condition as $secs => $str )
        {
            $d = $time_difference / $secs;

            if( $d >= 1 )
            {
                $t = round( $d );
                return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
            }
        }
    }
}