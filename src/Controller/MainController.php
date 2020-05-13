<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\UploadError;
use App\Form\InvoiceType;
use App\Service\CSVManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param CSVManager $csvManager
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, EntityManagerInterface $em, CSVManager $csvManager)
    {
        $invoice = new Invoice();

        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $invoicesFile */
            $invoicesFile = $form['fileName']->getData();

            if ($invoicesFile) {
                $process = $csvManager->processInvoicesFile($invoicesFile);
                $this->addUploadFlashMessages($process);
            }
        }

        $invoices = $em->getRepository(Invoice::class)->listInvoices($this->getParameter('invoice_list.pageLimit'), 0);
        $uploadErrors = $em->getRepository(UploadError::class)->listUploadErrors($this->getParameter('invoice_errors_list.pageLimit'), 0);

        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
            'invoices' => $invoices,
            'uploadErrors' => $uploadErrors
        ]);
    }

    /**
     * @Route("/load-data-ajax/{type}", name="load_table_ajax")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function loadTableDataAjaxAction(Request $request, EntityManagerInterface $em, string $type){
        $result = [
            'success' => true,
            'html'    => ''
        ];

        $limit = $this->getParameter('invoice_list.pageLimit');
        $offset = $request->query->has('offset') ? $request->query->get('offset') : 0;

        if ($type == 'error') {
            $uploadErrors = $em->getRepository(UploadError::class)->listUploadErrors($limit, $offset);
            foreach ($uploadErrors as $error){
                $result['html'] .= $this->renderView('main/partial/error-table-row.html.twig', [
                    'uploadError' => $error
                ]);
            }
        } else {
            $invoices = $em->getRepository(Invoice::class)->listInvoices($limit, $offset);

            foreach ($invoices as $invoice){
                $result['html'] .= $this->renderView('main/partial/invoice-table-row.html.twig', [
                    'invoice' => $invoice
                ]);
            }
        }

        return new JsonResponse($result);

    }

    /**
     * @param array $process
     */
    private function addUploadFlashMessages($process){
        if ($process['success']) {
            $this->addFlash(
                'success',
                $process['fileMessage'] . ' <br /> ' .
                '<strong>Invoices saved: </strong>'. $process['processCounts']['success'] . ' <br /> ' .
                '<span class="text-danger"><strong>Rows not saved due to errors: </strong>'. $process['processCounts']['error'] . '</span> <br /> ' .
                '<strong>Total Rows: </strong>'. $process['totalRowsProcessedCount'] . ' <br /> '
            );

        } else {
            $this->addFlash(
                'error',
                $process['fileMessage']
            );
        }
    }
}
