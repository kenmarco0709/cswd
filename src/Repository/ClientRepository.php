<?php

namespace App\Repository;

use App\Entity\ClientEntity;
use App\Entity\BranchEntity;


/**
 * ClientRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ClientRepository extends \Doctrine\ORM\EntityRepository
{
    public function validate($form) {

        $errors = array();
        $action = $form['action'];

        // d = delete
        if($action !== 'd') {   

            $isExistMember = $this->familyRelativeExistWithIn6oDays($form);
          //  $isExistClient = $this->clientExistWithIn6oDays($form);


            
            if(empty($form['first_name'])){
                $errors[] = 'First name should not be blank.';
            }

            if(empty($form['last_name'])){
                $errors[] = 'Last name should not be blank.';
            }

            //if($form['action'] == 'n'){

                if($isExistMember){
                    $errors[] = 'This client has a relative record in the system.';
                }
            //}

            
        }

        return $errors;
    }

    public function ajax_list(array $get, array $userData){
        $columns = array(
            array('c.`case_no`', 'c.`case_no`', 'caseNo'),
            array('cct.clientCaseTypes', 'cct.clientCaseTypes', 'caseType'),
            array('DATE_FORMAT(c.`intake_date`, "%m/%d/%Y")', 'DATE_FORMAT(c.`intake_date`, "%m/%d/%Y")', 'intakeDate'),
            array('DATE_FORMAT(c.`release_date`, "%m/%d/%Y")', 'DATE_FORMAT(c.`release_date`, "%m/%d/%Y")', 'releaseDate'),
            array('CONCAT(c.`first_name`, " ", IFNULL(c.`last_name`, "") )', 'CONCAT(c.`first_name`, " ", IFNULL(c.`last_name`, "") )', 'client'),
            array('CONCAT(c.`patient_first_name`, " ", c.`patient_last_name` )', 'CONCAT(c.`patient_first_name`, " ", c.`patient_last_name` )', 'patient'),
            array('cf.familyMembers', 'cf.familyMembers', 'familyMembers'),
            array('b.`description`', 'b.`description`', 'barangay'),
            array('d.`description`', 'd.`description`', 'desease'),
            array('c.`worker`', 'c.`worker`', 'worker'),
            array('c.`assistant_type`', "c.`assistant_type`","assistantType"),
            array('c.`id`', "c.`id`"),
            array('c.`treatment`', "c.`treatment`", "treatment"),
            array('DATE_FORMAT(c.`release_date`, "%m/%d/%Y")', 'DATE_FORMAT(c.`release_date`, "%m/%d/%Y")', 'releaseDate'),

        );
        $asColumns = array();

        $select = "SELECT";
        $from = "FROM `client` c";
        $joins = "LEFT JOIN `barangay` b ON b.`id` = c.`barangay_id`";
        $joins .= "LEFT JOIN `desease` d ON d.`id` = c.`desease_id`";
        $joins .= " LEFT JOIN (
            SELECT 
                GROUP_CONCAT(CONCAT(cf.`first_name`, ' ', cf.`last_name`)) AS familyMembers,
                cf.`id`,
                cf.`client_id`
            FROM `client_family` cf
            WHERE (cf.`is_deleted` = 0 OR  cf.`is_deleted` IS NULL)
            GROUP BY cf.`client_id`
        ) cf ON cf.`client_id` = c.`id`";
        $joins .= "LEFT JOIN (
            SELECT 
                GROUP_CONCAT(cct.`description`) AS clientCaseTypes,
                cct.`client_id`
            FROM `client_case_type` cct
            WHERE cct.`is_deleted` = 0
            GROUP BY cct.`client_id`
        ) cct ON cct.`client_id` = c.`id`";
        $sqlWhere = " WHERE (c.`is_deleted` = 0 OR  c.`is_deleted` IS NULL) ";
        $groupBy = "";
        $orderBy = "";
        $limit = "";
        $stmtParams = array();
       
        if($userData['type'] != 'Super Admin'){
            $sqlWhere .= ' AND c.`branch_id` = ' . base64_decode($userData['branchId']);
        }

        foreach($columns as $key => $column) {
            $select .= ($key > 0 ? ', ' : ' ') . $column[1] . (isset($column[2]) ? ' AS ' . $column[2] : '');
        }

        /*
         * Ordering
         */
        foreach($get['columns'] as $key => $column) {
            if($column['orderable']=='true') {
                if(isSet($get['order'])) {
                    foreach($get['order'] as $order) {
                        if($order['column']==$key) {
                            $orderBy .= (!empty($orderBy) ? ', ' : 'ORDER BY ') . $columns[$key][0] . (!empty($order['dir']) ? ' ' . $order['dir'] : '');
                        }
                    }
                }
            }
        }

          /*
         * Filtering
         */
        if(!empty($get['q']) && $get['q']){
            $aLikes = array();
            foreach($get['columns'] as $key => $column) {
                if($column['searchable']=='true') {
                    $aLikes[] = $columns[$key][0] . ' LIKE :searchValue';
                }
            }
            foreach($asColumns as $asColumn) {
                $aLikes[] = $asColumn . ' LIKE :searchValue';
            }
            if(count($aLikes)) {
                $sqlWhere .= (!empty($sqlWhere) ? ' AND ' : 'WHERE ') . '(' . implode(' OR ', $aLikes) . ')';
                $stmtParams['searchValue'] = "%" . $get['q'] . "%";
            }
        } else {
            /*
            * Filtering
            */
            if(isset($get['search']) && $get['search']['value'] != ''){
                $aLikes = array();
                foreach($get['columns'] as $key => $column) {
                    if($column['searchable']=='true') {
                        $aLikes[] = $columns[$key][0] . ' LIKE :searchValue';
                    }
                }
                foreach($asColumns as $asColumn) {
                    $aLikes[] = $asColumn . ' LIKE :searchValue';
                }
                if(count($aLikes)) {
                    $sqlWhere .= (!empty($sqlWhere) ? ' AND ' : 'WHERE ') . '(' . implode(' OR ', $aLikes) . ')';
                    $stmtParams['searchValue'] = "%" . $get['search']['value'] . "%";
                }
            }

        }

    
        /* Set Limit and Length */
        if(isset( $get['start'] ) && $get['length'] != '-1'){
            $limit = 'LIMIT ' . (int)$get['start'] . ',' . (int)$get['length'];
        }

        $sql = "$select $from $joins $sqlWhere $groupBy $orderBy";
        $query = $this->getEntityManager()->getConnection()->prepare($sql);

        foreach($stmtParams as $k => $v){
            $query->bindValue($k, $v);

        }
        $res = $query->executeQuery();
        $result_count = $res->fetchAllAssociative();




        $sql = "$select $from $joins $sqlWhere $groupBy $orderBy $limit";
        $query = $this->getEntityManager()->getConnection()->prepare($sql);
        foreach($stmtParams as $k => $v){
            $query->bindValue($k, $v);

        }
        $res = $query->executeQuery();
        $result = $res->fetchAllAssociative();

        /* Data Count */
        $recordsTotal = count($result_count);


              /*
         * Output
         */
        $output = array(
            "draw" => intval($get['draw']),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => array()
        );

        $url = $get['url'];
        $formUrl = $url . '/client/ajax_form';  
        $hasUpdate = false;
        $url = $get['url'];
        $hasDelete = false;

        if($userData['type'] == 'Super Admin'|| in_array('Client Update', $userData['accesses'])){
            $hasUpdate = true;
        }

        if($userData['type'] == 'Super Admin'|| in_array('Client Delete', $userData['accesses'])){
            $hasDelete = true;
        }


        foreach($result as $row) {

            $id = base64_encode($row['id']);
           
            $action = $hasDelete ? '<a href="javascript:void(0);" class="delete-btn action-button-style btn btn-success table-btn" data-id="'.$id.'" data-message="Are you sure you want to delete this Record?">Delete</a>' : "";
            $action .= $hasUpdate ? ' <a href="javascript:void(0);" class="fullscreen href-modal action-btn action-button-style btn btn-primary table-btn" data-id="'.$id.'"  data-url="'.$formUrl.'" data-action="u">Update</a>' : "";

            $values = array(
                $row['caseNo'] . ($this->isClientStillExistService($row) ? '<input type="hidden" data-isLocked="true" />' : ''),
                $row['caseType'],
                $row['intakeDate'],
                $row['releaseDate'],
                $row['client'],
                $row['patient'],
                $row['familyMembers'],
                $row['barangay'],
                $row['desease'],
                $row['worker'],
                $action
            );

            $output['data'][] = $values;
        }

        unset($result);
        return $output;
    }

    private function isClientStillExistService(array $row){

        if(is_null($row['releaseDate'])){
            return false;
        }

        $sixMonths  = date('m/d/Y', strtotime("+6 months", strtotime($row['releaseDate'])));
        $threeMonths  = date('m/d/Y', strtotime("+3 months", strtotime($row['releaseDate'])));
        
        if(in_array($row['assistantType'], ['FAM', 'AHC'])){
            if(isset($row['treatment']) && in_array($row['treatment'], ['Chemo and Dialysis'])){
                
                if(strtotime(date('m/d/Y')) >= strtotime($threeMonths)){
                    return false;
                }
            }
           
        }

        if(strtotime(date('m/d/Y')) >= strtotime($sixMonths)){
            return false;
        }

    
        return true;
    }

    public function autocompleteSuggestions($q, array $userData) {
       
        $stmtParams = array();

        $qs = $q['query'];

        $where = ' WHERE b.`description` LIKE :description';
        $stmtParams['description'] =  "%$qs%"; 

        $sql = "
            SELECT
                 b.`id`,
                 CONCAT(b.`description`, ', ', p.`description`) AS data,
                 CONCAT(b.`description`, ', ', p.`description`) AS value
            FROM `client` b
            LEFT JOIN `province` p ON p.`id` = b.`province_id`
            ". $where ."
            AND (b.`is_deleted` = 0 OR  b.`is_deleted` IS NULL)            
            ORDER BY CONCAT(b.`description`, ', ', p.`description`)
            LIMIT 0,20
        ";


        $query = $this->getEntityManager()->getConnection()->prepare($sql);

        foreach($stmtParams as $k => $v){
            $query->bindValue($k, $v);

        }
       
        $res = $query->executeQuery();
        $result = $res->fetchAllAssociative();

        return $result;
    }

    private function familyRelativeExistWithIn6oDays($form){

        $result = [];
        $intakeCanTakeAgain  = date('Y-m-d', strtotime("+6 months", strtotime(date("y-m-d")))) . ' 23:59:59';
        $where = "WHERE ((c.`first_name` = '".$form['first_name']."' AND c.`middle_name` = '".$form['middle_name']."' ANd c.`last_name` = '".$form['last_name']."') OR (cf.`first_name` = '".$form['first_name']."' AND cf.`middle_name` = '".$form['middle_name']."' ANd cf.`last_name` = '".$form['last_name']."'))";
        if(isset($form['assistant_type']) &&  in_array($form['assistant_type'], ['FAM', 'FAM'])){
            if(isset($form['treatment']) && in_array($form['treatment'], ['Chemo and Dialysis'])){
                $intakeCanTakeAgain  = date('Y-m-d', strtotime("+3 months", strtotime(date("y-m-d")))) . ' 23:59:59';
            }
        }


        if(isset($form['familyMember'])){

            $memberNames = $this->getFamilyMemberFullName($form['familyMember']);
            $where = "WHERE (CONCAT(cf.`first_name`,  ' ', cf.`middle_name`,  ' ', cf.`last_name`) IN (".$memberNames.") OR (cf.`first_name` = '".$form['first_name']."' AND cf.`middle_name` = '".$form['middle_name']."' ANd cf.`last_name` = '".$form['last_name']."') OR (CONCAT(c.`first_name`,  ' ', c.`middle_name`,  ' ', c.`last_name`) IN (".$memberNames.")))";
        } 
        $where.= " AND c.`id` != '" . $form['id'] . "'";  

        $sql = "
            SELECT
                cf.`id`,
                c.`intake_date`,
                CONCAT(c.`first_name`,  ' ', c.`middle_name`,  ' ', c.`last_name`) AS cc,
                CONCAT(cf.`first_name`,  ' ', cf.`middle_name`,  ' ', cf.`last_name`) As fdd
            FROM `client_family` cf
            LEFT JOIN `client` c ON c.`id` = cf.`client_id`
            ".$where."
            AND cf.`is_deleted` != 1
            AND c.`is_deleted` != 1
            AND c.`release_date`  IS NOT NULL
            AND c.`release_date`  <= '".$intakeCanTakeAgain."' 
            LIMIT 1
        ";
   

        $query = $this->getEntityManager()->getConnection()->prepare($sql);
    
        $res = $query->executeQuery();
        $result = $res->fetchAllAssociative();
        return count($result) ? true: false;
    }

    private function clientExistWithIn6oDays($form){

        $result = [];
        
        $intakeCanTakeAgain  = date('Y-m-d', strtotime("+6 months", strtotime(date("y-m-d")))) . ' 23:59:59';
        if(isset($form['assistant_type']) &&  in_array($form['assistant_type'], ['AHC', 'HOSPITL ASSIS'])){
            if(isset($form['desease']) && in_array($form['desease'], ['Chemo and Dialysis', 'Cancer'])){
                $intakeCanTakeAgain  = date('Y-m-d', strtotime("+3 months", strtotime(date("y-m-d")))) . ' 23:59:59';
            }
        }

        $sql = "
            SELECT
                    c.`id`
            FROM `client` c 
            WHERE (CONCAT(c.`first_name`,  ' ', c.`middle_name`,  ' ', c.`last_name`) IN (".$memberNames.") OR (c.`first_name` = '".$form['first_name']."' AND c.`middle_name` = '".$form['middle_name']."' ANd c.`last_name` = '".$form['last_name']."'))
            AND c.`is_deleted` NOT IN (1)
            LIMIT 1
        ";


        $query = $this->getEntityManager()->getConnection()->prepare($sql);

        $res = $query->executeQuery();
        $result = $res->fetchAllAssociative();
        

        
        
        return count($result) ? true: false;
    }

    private function getFamilyMemberFullName($familyMembers){

        $memberFullnames = '';
        $familyCtr = count($familyMembers);

        foreach ($familyMembers as $k =>  $v) {

           $memberFullnames .= "'". $v['firstName'] ." " . $v['middleName']." " . $v['lastName'] . "'"; 
           if($k  != array_key_last($familyMembers)){
                $memberFullnames .= ', ';
           }
        }

        return $memberFullnames;
    }

    public function report($dateFrom, $dateTo, $serviceType, $userData){
       
        $stmtParams = array();
        $andWhere = '';

        if($userData['type'] != 'Super Admin'){
             $stmtParams['branchId'] = base64_decode($userData['branchId']);   
             $andWhere .= ' AND c.`branch_id` =:branchId';   

        }

       if($dateFrom != 'NULL' && $dateTo != 'NULL'){
            $andWhere .= ' AND c.`intake_date` BETWEEN "' . date('Y-m-d', strtotime(str_replace('-', '/', $dateFrom))) . ' 00:00:00" AND "' .  date('Y-m-d', strtotime(str_replace('-', '/', $dateTo))) . ' 23:59:59"';   
       } else if( $dateFrom != 'NULL'){
            $andWhere .= ' AND c.`intake_date` >= "' . date('Y-m-d', strtotime(str_replace('-', '/', $dateFrom))) . ' 00:00:00"';
       } else if($dateTo != 'NULL') {
            $andWhere .= ' AND c.`intake_date` <= "' .   date('Y-m-d', strtotime(str_replace('-', '/', $dateTo))) . ' 23:59:59"';
       }

       if($serviceType != 'All'){
            $andWhere .= ' AND c.`assistant_type` = "'.$serviceType.'"';   
       }

        $query = $this->getEntityManager()->getConnection()->prepare("
                SELECT 
                    CONCAT(c.`first_name` , ' ', c.`middle_name` ,' ', c.`last_name`) AS client,
                    c.`assistant_type` AS assistantType,
                    c.`approve_amount` AS approveAmount,
                    c.`case_no` AS caseNo,
                    c.`birth_place` AS birthPlace,
                    c.`civil_status` AS civilStatus,
                    c.`specified_civil_status` AS specifyCivilStatus,
                    c.`relation_to_patient` AS patientRelation,
                    c.`occupation` AS occupation,
                    c.`religion` AS religion,
                    c.`contact_no` AS contactNo,
                    c.`problem_presented` AS problemPresented,
                    c.`housing` AS housing,
                    c.`housing_structure` AS housingStructure,
                    c.`lot` AS lot,
                    c.`lightning` AS lightning,
                    c.`educational_attainment` AS educationalAttainment,
                    ba.`description` AS barangayAddress,
                    b.`description` AS barangay,
                    c.`gender` AS gender,
                    DATE_FORMAT(c.`intake_date`, '%m/%d/%Y') AS intakeDate,
                    DATE_FORMAT(c.`birth_date`, '%m/%d/%Y') AS birthDate,
                    cct.clientCaseTypes,
                    cf.familyMembers,
                    cf.familyGenders,
                    cf.familyRoles,
                    cf.familyAges,
                    cf.familyCivilStatuses,
                    cf.familyEducationalAttainments,
                    cf.familyOccupations,
                    cf.familyMonthlyIncomes
                FROM `client` c 
                LEFT JOIN `barangay` b ON b.`id` = c.`barangay_id`
                LEFT JOIN `barangay` ba ON ba.`id` = c.`barangay_address_id`
                LEFT JOIN (
                    SELECT 
                        GROUP_CONCAT(CONCAT(cf.`first_name`, ' ', cf.`last_name`)) AS familyMembers,
                        GROUP_CONCAT(cf.`gender`) AS familyGenders,
                        GROUP_CONCAT(cf.`family_role`) AS familyRoles,
                        GROUP_CONCAT(cf.`age`) AS familyAges,
                        GROUP_CONCAT(cf.`civil_status`) AS familyCivilStatuses,
                        GROUP_CONCAT(cf.`educational_attainment`) AS familyEducationalAttainments,
                        GROUP_CONCAT(cf.`occupation`) AS familyOccupations,
                        GROUP_CONCAT(cf.`monthly_income`) AS familyMonthlyIncomes,

                        cf.`id`,
                        cf.`client_id`
                    FROM `client_family` cf
                    WHERE (cf.`is_deleted` = 0 OR  cf.`is_deleted` IS NULL)
                    GROUP BY cf.`client_id`
                ) cf ON cf.`client_id` = c.`id`
                LEFT JOIN (
                    SELECT 
                        GROUP_CONCAT(cct.`description`) AS clientCaseTypes,
                        cct.`client_id`
                    FROM `client_case_type` cct
                    WHERE cct.`is_deleted` = 0
                    GROUP BY cct.`client_id`
                ) cct ON cct.`client_id` = c.`id`
               
                WHERE c.`is_deleted` = 0
                ".$andWhere."
                ORDER BY c.`intake_date` asc
        ");

        foreach($stmtParams as $k => $v){
            $query->bindValue($k, $v);

        }
       
        $res = $query->executeQuery();
        $result = $res->fetchAllAssociative();
        return $result;

    }

    public function list_top_barangay(array $userData) {

        $stmtParams = [];
        $sqlWhere = '';
        if($userData['type'] != 'Super Admin'){
            $sqlWhere .= ' WHERE c.`branch_id` = ' . base64_decode($userData['branchId']);
        }
  
        $sql = "
            SELECT
                COUNT(c.`id`) AS clientCtr,
                IFNULL(b.`description`, 'No Barangay') AS description
            FROM `client` c
            LEFT JOIN `barangay` b ON b.`id` = c.`barangay_id`
            " . $sqlWhere . "
            AND ( c.`is_deleted` = 0 OR NULL )
            GROUP BY c.`barangay_id`
            ORDER BY COUNT(c.`id`) DESC
            LIMIT 10
        ";

        $query = $this->getEntityManager()->getConnection()->prepare($sql);

        foreach($stmtParams as $k => $v){
            $query->bindValue($k, $v);

        }
       
        $res = $query->executeQuery();
        $results = $res->fetchAllAssociative();
        
        $data = [
            'labels' => [],
            'ctr' => [],
            'colors' => []
        ];

        foreach($results as $k => $result){
            $color = $this->getRandomColor();
            $data['labels'][] = $result['description'];
            $data['ctr'][] = $result['clientCtr'];
            $data['colors'][] = $color;
        }
        return $data;
    }

    public function list_per_barangay(array $userData) {

        $stmtParams = [];
        $sqlWhere = '';
        if($userData['type'] != 'Super Admin'){
            $sqlWhere .= ' WHERE c.`branch_id` = ' . base64_decode($userData['branchId']);
        }
  
        $sql = "
            SELECT
                COUNT(c.`id`) AS clientCtr,
                IFNULL(b.`description`, 'No Barangay') AS description
            FROM `client` c
            LEFT JOIN `barangay` b ON b.`id` = c.`barangay_id`
            " . $sqlWhere . "
            AND ( c.`is_deleted` = 0 OR NULL )
            GROUP BY c.`barangay_id`
        ";

        $query = $this->getEntityManager()->getConnection()->prepare($sql);

        foreach($stmtParams as $k => $v){
            $query->bindValue($k, $v);

        }
       
        $res = $query->executeQuery();
        $results = $res->fetchAllAssociative();
        
        $data = [
            'labels' => [],
            'ctr' => [],
            'colors' => []
        ];

        foreach($results as $k => $result){
            $color = $this->getRandomColor();
            $data['labels'][] = $result['description'];
            $data['ctr'][] = $result['clientCtr'];
            $data['colors'][] = $color;
        }
        return $data;
    }

    public function list_per_desease(array $userData) {

        $stmtParams = [];
        $sqlWhere = '';
        if($userData['type'] != 'Super Admin'){
            $sqlWhere .= ' WHERE c.`branch_id` = ' . base64_decode($userData['branchId']);
        }
  
        $sql = "
            SELECT
                COUNT(c.`id`) AS clientCtr,
                d.`description` AS description
            FROM `client` c
            LEFT JOIN `desease` d ON d.`id` = c.`desease_id` 
            LEFT JOIN(
                SELECT 
                    GROUP_CONCAT(cst.`id`) AS assistantTypes,
                    cst.`client_id`
                FROM `client_assistance_type` cst
                WHERE cst.`description` IN ('AHC', 'FAM')
                GROUP BY cst.`client_id`     
            ) cst ON cst.`client_id` = c.`id`
            " . $sqlWhere . "
    
            AND cst.assistantTypes IS NOT NULL 
            AND ( c.`is_deleted` = 0 OR NULL )
            GROUP BY c.`desease_id`
            ORDER BY COUNT(c.`id`) DESC
            LIMIT 10
        ";

        $query = $this->getEntityManager()->getConnection()->prepare($sql);

        foreach($stmtParams as $k => $v){
            $query->bindValue($k, $v);

        }
       
        $res = $query->executeQuery();
        $results = $res->fetchAllAssociative();
        
        $data = [
            'labels' => [],
            'ctr' => [],
            'colors' => []
        ];

        foreach($results as $k => $result){
            $color = $this->getRandomColor();
            $data['labels'][] = $result['description'];
            $data['ctr'][] = $result['clientCtr'];
            $data['colors'][] = $color;
        }
        return $data;
    }
    
    private function getRandomColor() {
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        $color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
        return $color;
    }

}