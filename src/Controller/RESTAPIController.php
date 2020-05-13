<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RESTAPIController extends AbstractController
{
    /**
     * @Route("/api/list-invoices", name="api_list_invoices")
     */
    public function listInvoicesAction(Request $request, EntityManagerInterface $em)
    {
        $limit = $request->query->has('limit') ? (int)$request->query->get('limit') : $this->getParameter('invoice_list.pageLimit');
        $offset = $request->query->has('offset') ? (int)$request->query->get('offset') : 0;
        $invoices = $em->getRepository(Invoice::class)->listInvoices($limit, $offset);

        foreach ($invoices as $key => $invoice) {
            $invoices[$key]['dueOn'] = $invoices[$key]['dueOn']->format('Y-m-d');
        }
        return $this->sendSuccessResponse($invoices);
    }

    /**
     * @param array $result
     * @return JsonResponse
     */
    private function sendSuccessResponse(array $result){
        $response = new JsonResponse(['results' => $result], 200);
        $response->setEncodingOptions( $response->getEncodingOptions() | JSON_PRETTY_PRINT );
        return $response;
    }
}
