<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

use App\Entity\ClientEntity;


use App\Service\AuthService;

/**
 * @Route("/report")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/intake_sheet", name="report_intake_sheet")
     */
    public function intake_sheet(Request $request, AuthService $authService)
    {
        if(!$authService->isLoggedIn()) return $authService->redirectToLogin();
        if(!$authService->isUserHasAccesses(array('Report Intake Sheet'))) return $authService->redirectToHome();

        $userData = $this->get('session')->get('userData');
        $page_title = ' Intake Sheet Report'; 
       return $this->render('Report/intake_sheet.html.twig', [ 
            'page_title' => $page_title, 
            'assistantTypes' => $authService->assistantTypes(),
            'javascripts' => array('/js/report/intake_sheet.js') ]
       );
    }
    


        /**
     * @Route("/export_intake_sheet_csv/{dateFrom}/{dateTo}/{serviceType}", name = "report_export_intake_sheet_csv")
     */
    public function exportIntakeSheetCsv(Request $request, AuthService $authService, $dateFrom, $dateTo, $serviceType ){

        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $userData = $this->get('session')->get('userData');

        $clients = $this->getDoctrine()->getManager()->getRepository(ClientEntity::class)->report($dateFrom,$dateTo, $serviceType, $this->get('session')->get('userData'));

        $columnRange = range('A', 'Z');
        $cellsData = array(
            array('cell' => 'A1', 'data' => 'Start Date: ' .($dateFrom != 'null' ? $dateFrom : '')),
            array('cell' => 'A2', 'data' => 'End Date: ' .($dateTo != 'null' ? $dateTo : '')),
            array('cell' => 'A3', 'data' => 'Assistant Type: ' . $serviceType ),

        );

        $row = 5;

        $cellsData[] = array('cell' => 'A'.$row, 'data' => 'Intake Date');
        $cellsData[] = array('cell' => 'B'.$row, 'data' => 'Barangay');
        $cellsData[] = array('cell' => 'C'.$row, 'data' => 'Case No.');
        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Approved Amount');
        $cellsData[] = array('cell' => 'E'.$row, 'data' => 'Assistant Type');
        $cellsData[] = array('cell' => 'F'.$row, 'data' => 'Case Types');
        $cellsData[] = array('cell' => 'G'.$row, 'data' => 'Client');
        $cellsData[] = array('cell' => 'H'.$row, 'data' => 'Client Address');
        $cellsData[] = array('cell' => 'I'.$row, 'data' => 'Client Birth Place');
        $cellsData[] = array('cell' => 'J'.$row, 'data' => 'Client Birth Date');
        $cellsData[] = array('cell' => 'K'.$row, 'data' => 'Client Educational Attainment');
        $cellsData[] = array('cell' => 'L'.$row, 'data' => 'Client Gender');
        $cellsData[] = array('cell' => 'M'.$row, 'data' => 'Client Civil Status');
        $cellsData[] = array('cell' => 'N'.$row, 'data' => 'Client Relationship to Patient');
        $cellsData[] = array('cell' => 'O'.$row, 'data' => 'Client Occupation');
        $cellsData[] = array('cell' => 'P'.$row, 'data' => 'Client Contact No.');
        $cellsData[] = array('cell' => 'Q'.$row, 'data' => 'Client Family Member');
        $cellsData[] = array('cell' => 'R'.$row, 'data' => 'Client Family Member Age');
        $cellsData[] = array('cell' => 'S'.$row, 'data' => 'Client Family Member Educational Attainment');
        $cellsData[] = array('cell' => 'T'.$row, 'data' => 'Client Family Member Gender');
        $cellsData[] = array('cell' => 'U'.$row, 'data' => 'Client Family Member Civil Status');
        $cellsData[] = array('cell' => 'V'.$row, 'data' => 'Client Family Member Relationship to Patient');
        $cellsData[] = array('cell' => 'W'.$row, 'data' => 'Client Family Member Occupation');
        $cellsData[] = array('cell' => 'X'.$row, 'data' => 'Client Family Member Monthly Income');
        $cellsData[] = array('cell' => 'Y'.$row, 'data' => 'Problem Presented');
        $cellsData[] = array('cell' => 'Z'.$row, 'data' => 'Housing');
        $cellsData[] = array('cell' => 'AA'.$row, 'data' => 'Housing Structure');
        $cellsData[] = array('cell' => 'AB'.$row, 'data' => 'Lot');
        $cellsData[] = array('cell' => 'AC'.$row, 'data' => 'Lightning');

        $totalCtr = 0;
        foreach ($clients as $client) {
            $row++;
            $totalCtr++;
            $cellsData[] = array('cell' => 'A'.$row, 'data' => $client['intakeDate']);
            $cellsData[] = array('cell' => 'B'.$row, 'data' => $client['barangay']);
            $cellsData[] = array('cell' => 'C'.$row, 'data' => $client['caseNo']);
            $cellsData[] = array('cell' => 'D'.$row, 'data' => $client['approveAmount']);
            $cellsData[] = array('cell' => 'E'.$row, 'data' => $client['assistantType']);
            $cellsData[] = array('cell' => 'F'.$row, 'data' => $client['clientCaseTypes']);
            $cellsData[] = array('cell' => 'G'.$row, 'data' => $client['client']);
            $cellsData[] = array('cell' => 'H'.$row, 'data' => $client['barangayAddress']);
            $cellsData[] = array('cell' => 'I'.$row, 'data' => $client['birthPlace']);
            $cellsData[] = array('cell' => 'J'.$row, 'data' => $client['birthDate']);
            $cellsData[] = array('cell' => 'K'.$row, 'data' => $client['educationalAttainment']);
            $cellsData[] = array('cell' => 'L'.$row, 'data' => $client['gender']);
            $cellsData[] = array('cell' => 'M'.$row, 'data' => $client['civilStatus']);
            $cellsData[] = array('cell' => 'N'.$row, 'data' => $client['patientRelation']);
            $cellsData[] = array('cell' => 'O'.$row, 'data' => $client['occupation']);
            $cellsData[] = array('cell' => 'P'.$row, 'data' => $client['contactNo']);
            $cellsData[] = array('cell' => 'Q'.$row, 'data' => $client['familyMembers']);
            $cellsData[] = array('cell' => 'R'.$row, 'data' => $client['familyAges']);
            $cellsData[] = array('cell' => 'S'.$row, 'data' => $client['familyEducationalAttainments']);
            $cellsData[] = array('cell' => 'T'.$row, 'data' => $client['familyGenders']);
            $cellsData[] = array('cell' => 'U'.$row, 'data' => $client['familyCivilStatuses']);
            $cellsData[] = array('cell' => 'V'.$row, 'data' => $client['familyRoles']);
            $cellsData[] = array('cell' => 'W'.$row, 'data' => $client['familyOccupations']);
            $cellsData[] = array('cell' => 'X'.$row, 'data' => $client['familyMonthlyIncomes']);
            $cellsData[] = array('cell' => 'Y'.$row, 'data' => $client['problemPresented']);
            $cellsData[] = array('cell' => 'Z'.$row, 'data' => $client['housing']);
            $cellsData[] = array('cell' => 'AA'.$row, 'data' => $client['housingStructure']);
            $cellsData[] = array('cell' => 'AB'.$row, 'data' => $client['lot']);
            $cellsData[] = array('cell' => 'AC'.$row, 'data' => $client['lightning']);
        }
        $row++;
        $row++;

        $cellsData[] = array('cell' => 'A'.$row, 'data' => 'Total Count: '. $totalCtr);


            

    
        $page_title = 'intake-sheet-report';
        return $this->export_to_excel($columnRange, $cellsData, $page_title);
    }

    /**
     * @Route("/consumption_ajax_list", name="report_consumption_ajax_list")
     */
    public function consumption_ajax_list(Request $request, AuthService $authService) {

     
        $get = $request->query->all();

        $result = array(
            "draw" => intval($get['draw']),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => array()
        );

        if($authService->isLoggedIn()) {
            $result = $this->getDoctrine()->getManager()->getRepository(ClientAccountBillingEntity::class)->consumption_ajax_list($get, $this->get('session')->get('userData'));
        }

        return new JsonResponse($result);
    }

    /**
     * @Route("/export_income_csv/{dateFrom}/{dateTo}/{purok}", name = "report_export_income_csv")
     */
    public function exportIncomeCsv(Request $request, AuthService $authService, $dateFrom, $dateTo, $purok ){

        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $userData = $this->get('session')->get('userData');

        $payments = $this->getDoctrine()->getManager()->getRepository(ClientAccountPaymentEntity::class)->income_report($dateFrom,$dateTo, $purok, $this->get('session')->get('userData'));
        $expenses = $this->getDoctrine()->getManager()->getRepository(ExpenseEntity::class)->income_report($dateFrom,$dateTo, $purok, $this->get('session')->get('userData'));

        $columnRange = range('A', 'Z');
        $cellsData = array(
            array('cell' => 'A1', 'data' => 'Start Date: ' .($dateFrom != 'null' ? $dateFrom : '')),
            array('cell' => 'A2', 'data' => 'End Date: ' .($dateTo != 'null' ? $dateTo : '')),
        );

        $row = 4;
        $cellsData[] = array('cell' => 'A'.$row, 'data' => 'Collections');
        $row++; 

        $cellsData[] = array('cell' => 'A'.$row, 'data' => 'Client');
        $cellsData[] = array('cell' => 'B'.$row, 'data' => 'Payment Type');
        $cellsData[] = array('cell' => 'C'.$row, 'data' => 'Reference No.');
        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Payment Date');
        $cellsData[] = array('cell' => 'E'.$row, 'data' => 'Amount');

        $totalPayment = 0;
        foreach ($payments as $payment) {
            $row++;
            $totalPayment += $payment['amount'];
            $cellsData[] = array('cell' => 'A'.$row, 'data' => $payment['fullName']);
            $cellsData[] = array('cell' => 'B'.$row, 'data' => $payment['paymentType']);
            $cellsData[] = array('cell' => 'C'.$row, 'data' => $payment['refNo']);
            $cellsData[] = array('cell' => 'D'.$row, 'data' => $payment['paymentDate']);
            $cellsData[] = array('cell' => 'E'.$row, 'data' => $payment['amount']);
        }
        $row++;

        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Total: ');
        $cellsData[] = array('cell' => 'E'.$row, 'data' => $totalPayment);

        $row++;
        $row++;
        $cellsData[] = array('cell' => 'A'.$row, 'data' => 'Expenses');
        $row++;
        $cellsData[] = array('cell' => 'A'.$row, 'data' => 'Expense Type');
        $cellsData[] = array('cell' => 'B'.$row, 'data' => 'Description');
        $cellsData[] = array('cell' => 'C'.$row, 'data' => 'Expense Date');
        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Amount');

        $totalExpense = 0;
        foreach ($expenses as $expense) {
            $row++;
            $totalExpense += $expense['amount'];
            $cellsData[] = array('cell' => 'A'.$row, 'data' => $expense['expenseType']);
            $cellsData[] = array('cell' => 'B'.$row, 'data' => $expense['description']);
            $cellsData[] = array('cell' => 'C'.$row, 'data' => $expense['expenseDate']);
            $cellsData[] = array('cell' => 'D'.$row, 'data' => $expense['amount']);
        }
        $row++;
        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Total: ');
        $cellsData[] = array('cell' => 'E'.$row, 'data' => $totalExpense);

        $row++;
        $row++;

        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Total Collection: ');
        $cellsData[] = array('cell' => 'E'.$row, 'data' => $totalPayment);
        $row++;
        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Total Expenses: ');
        $cellsData[] = array('cell' => 'E'.$row, 'data' => $totalExpense);
        $row++;
        $cellsData[] = array('cell' => 'D'.$row, 'data' => 'Gross Income: ');
        $cellsData[] = array('cell' => 'E'.$row, 'data' => $totalPayment - $totalExpense);
    
        $page_title = 'income-report';
        return $this->export_to_excel($columnRange, $cellsData, $page_title);
    }

    /**
     * @Route("/export_consumption_csv/{date}/{purok}", name = "report_export_consumption_csv")
     */
    public function exportConsumptionCsv(Request $request, AuthService $authService, $date, $purok ){

        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $data = $request->request->all();
        $em = $this->getDoctrine()->getManager();
        $userData = $this->get('session')->get('userData');

        $reports  = $this->getDoctrine()->getManager()->getRepository(ClientAccountBillingEntity::class)->consumptionData($date, $purok, $this->get('session')->get('userData'));
        $puroks = $this->getDoctrine()->getManager()->getRepository(PurokEntity::class)->findBy(array('isDeleted' => 0, 'branch' => base64_decode($userData['branchId'])));

        
        $cellsData = array();

        $startCol = 'b';
        $column = 'b';
        $cellsData[] = array('cell' => "a1" , 'data' => 'Client Account');
        $colsArray = [];
        $billedArray = [];

        foreach($puroks as $k =>  $purok){
            $colsArray[$column] = 0;
            $billedArray[$column] = 0;


            $cellsData[] = array('cell' => $column. "1" , 'data' => $purok->getDescription());
            $column++;
        }

        $columnRange = range($startCol, $column);

        $rowCtr = 1;
        $totalCount = 0;
        foreach($reports as $report) {
            $rowCtr++;

            $cellsData[] = array('cell' => "A$rowCtr", 'data' => $report['account']);
            $column = 'b';
            foreach($puroks as $k =>  $purok){
                if($purok->getDescription() == $report['purok']){
                    $colsArray[$column] += $report['consume'];
                    $billedArray[$column] += $report['billedAmount'];

                    $cellsData[] = array('cell' => $column. $rowCtr, 'data' => $report['consume'] . ' / ' . $report['billedAmount']);
                }
                $column++;
            }
        }

        $rowCtr++;

        $cellsData[] = array('cell' => "A$rowCtr" , 'data' => 'Total Consume');
        $column = 'b';
        foreach($colsArray as $k =>  $col){
            if($k == $column){
                $cellsData[] = array('cell' => $column. $rowCtr, 'data' => $col);
                $column++;
            }
        }

        $rowCtr++;

        $cellsData[] = array('cell' => "A$rowCtr" , 'data' => 'Total Billed Amount');
        $column = 'b';

        foreach($billedArray as $k =>  $col){
            if($k == $column){
                $cellsData[] = array('cell' => $column. $rowCtr, 'data' => $col);
                $column++;
            }
        }
    
        $page_title = 'consume-report';
        return $this->export_to_excel($columnRange, $cellsData, $page_title);
    }

    private function export_to_excel($columnRange, $cellsData, $page_title, $customStyle=array()) {


        $spreadSheet = new SpreadSheet();
        $activeSheet = $spreadSheet->getActiveSheet(0);

        foreach($cellsData as $cellData) {
            $activeSheet->getCell($cellData['cell'])->setValue($cellData['data']);
        }

        $activeSheet->getColumnDimension('A')->setAutoSize(true);

        $writer = new Xlsx($spreadSheet);

        $response =  new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            }
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $page_title . '.xlsx"');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}
