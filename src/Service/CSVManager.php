<?php

namespace App\Service;

use App\Entity\Invoice;
use App\Entity\UploadError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CSVManager
{
    private $fileManager;
    private $em;
    private $invoicesFileName;
    private $invoiceCoefficients;

    const INVOICE_ID = 'invoiceId';
    const AMOUNT     = 'amount';
    const DUE_ON     = 'dueOn';
    const MAPPINGS = [
        self::INVOICE_ID => 0,
        self::AMOUNT     => 1,
        self::DUE_ON     => 2,
    ];
    const TRANSACTION_STEP = 100;


    /**
     * CSVManager constructor.
     * @param $invoiceCoefficients
     * @param EntityManagerInterface $entityManager
     * @param FileManager $fileManager
     */
    public function __construct($invoiceCoefficients, EntityManagerInterface $entityManager, FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
        $this->em = $entityManager;
        $this->invoiceCoefficients = $invoiceCoefficients;
    }

    /**
     * @param UploadedFile $invoicesFile
     * @return array
     */
    public function processInvoicesFile(UploadedFile $invoicesFile)
    {
        $processCounts = [
            'success' => 0,
            'error' => 0
        ];

        $upload = $this->fileManager->upload($invoicesFile, 'csv');

        $rowNumber = 0;
        if ($upload['success']) {
            $invoicesFileNameFull = $upload['fileNameFull'];
            $this->invoicesFileName = $upload['originalFileName'];

            //get values and save to db
            $fileRead = fopen($invoicesFileNameFull, 'r');

            while (!feof($fileRead) ) {
                $rowNumber++;

                $row = fgetcsv($fileRead, 1024);

                if (is_array($row)) {
                    $process = $this->processInvoiceRow($row, $rowNumber);

                    if ($process['errorsCount'] > 0) {
                        $processCounts['error']++;
                    } else {
                        $processCounts['success']++;
                    }

                    if ( ($processCounts['success'] + $processCounts['error']) % self::TRANSACTION_STEP == 0) {
                        $this->em->flush();
                    }
                }
            }

            fclose($fileRead);

            $this->em->flush();

        }

        return [
            'success' => $upload['success'],
            'fileMessage' => $upload['message'],
            'totalRowsProcessedCount' => --$rowNumber,
            'processCounts' => $processCounts,
        ];

    }


    /**
     * @param array $row
     * @param int $rowNumber
     * @return array
     */
    private function processInvoiceRow(array $row, int $rowNumber){
        $now = new \DateTime('now');
        $errors = [];

        $invoiceId = $row[self::MAPPINGS[self::INVOICE_ID]];
        $amount    = $row[self::MAPPINGS[self::AMOUNT]];
        $dueOn     = \DateTime::createFromFormat('Y-m-d H:i:s', $row[self::MAPPINGS[self::DUE_ON]] . '00:00:00');

        //quick validation
        if (!ctype_digit($invoiceId))
            $errors[] = ['field' => self::INVOICE_ID, 'message' => 'ID is invalid', 'value' => $invoiceId];
        if (!is_numeric($amount) || floatval($amount) <= 0)
            $errors[] = ['field' => self::AMOUNT, 'message' => 'Amount needs to be a positive numerical value', 'value' => $amount];
        if (!$dueOn)
            $errors[] = ['field' => self::DUE_ON, 'message' => 'Wrong date format', 'value' => $dueOn];

        if (count($errors) == 0) {
            $invoice = new Invoice();
            $invoice->setInvoiceId((int)$invoiceId);
            $invoice->setFileName($this->invoicesFileName);
            $invoice->setAmount($amount);
            $invoice->setDueOn($dueOn);

            $dateDiff = (int)$now->diff($dueOn)->format('%r%a');

            $coefficient = ($dateDiff > '30') ? $this->invoiceCoefficients['moreThan30'] : $this->invoiceCoefficients['lessOrEq30'];
            $sellingPrice = $coefficient * $amount;

            $invoice->setSellingPrice($sellingPrice);
            $this->em->persist($invoice);
        } else {
            $fullRow = $row[self::MAPPINGS[self::INVOICE_ID]]. ', ' . $row[self::MAPPINGS[self::AMOUNT]] . ', ' .$row[self::MAPPINGS[self::DUE_ON]];

            foreach ($errors as $error) {
                $uploadError = new UploadError();

                $highlightStart = strpos($fullRow, $error['value']. ',') ;

                $uploadError->setFileName($this->invoicesFileName);
                $uploadError->setMessage($error['message']);
                $uploadError->setHighlightStart($highlightStart);
                $uploadError->setHighlightEnd($highlightStart + strlen($error['value']));
                $uploadError->setRowText($fullRow);
                $uploadError->setRowNumber($rowNumber);
                $uploadError->setUploadDate(new \DateTime('now'));

                $this->em->persist($uploadError);
            }
        }

        return ['errorsCount' => count($errors)];
    }
}