<?php

namespace App\Repository;

use App\Entity\CityEntity;
use App\Entity\BranchEntity;


/**
 * CityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CityRepository extends \Doctrine\ORM\EntityRepository
{
    public function validate($form) {

        $errors = array();
        $action = $form['action'];

        // d = delete
        if($action !== 'd') {

            
            $cityExist = $this->getEntityManager()->getRepository(CityEntity::class)
                ->createQueryBuilder('c')
                ->where('c.id != :id')
                ->andWhere('c.description = :description')
                ->andWhere('c.isDeleted = :is_deleted')
                ->setParameters(array(
                    'id' => $form['id'],
                    'description' => $form['description'],
                    'is_deleted' => false,
                ))
                ->getQuery()->getResult();
            
            if($action != 'u' && $cityExist){
                $errors[] = 'City already exist.';
            }

            if(empty($form['province'])){
                $errors[] = 'Province should not be blank.';
            }

            if(empty($form['description'])){
                $errors[] = 'Description should not be blank.';
            }

            
        }

        return $errors;
    }

    public function ajax_list(array $get, array $userData){
        $columns = array(
            array('p.`description`', 'p.`description`', 'province'),
            array('c.`code`', 'c.`code`'),
            array('c.`description`', 'c.`description`'),
            array('c.`id`', "c.`id`")
        );
        $asColumns = array();

        $select = "SELECT";
        $from = "FROM `city` c";
        $joins = " LEFT JOIN `province` p ON p.`id` = c.`province_id`";
        $sqlWhere = " WHERE (c.`is_deleted` = 0 OR  c.`is_deleted` IS NULL) ";
        $groupBy = "";
        $orderBy = "";
        $limit = "";
        $stmtParams = array();


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
        $formUrl = '';
        $hasUpdate = false;
        $url = $get['url'];

        if($userData['type'] == 'Super Admin'|| in_array('CMS City Update', $userData['accesses'])){

            $formUrl = $url . '/city/ajax_form';  
            $hasUpdate = true;
        }

        foreach($result as $row) {

            $id = base64_encode($row['id']);

            $action = $hasUpdate ? '<a href="javascript:void(0);" class="href-modal action-btn action-button-style btn btn-primary table-btn" data-id="'.$id.'"  data-url="'.$formUrl.'" data-action="u">Update</a>' : "";

            $values = array(
                $row['province'],
                $row['code'],
                $row['description'],
                $action
            );

            $output['data'][] = $values;
        }

        unset($result);

        return $output;
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
            FROM `city` b
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

}
