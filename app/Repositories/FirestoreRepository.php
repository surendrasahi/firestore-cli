<?php

namespace App\Repositories;

use Kreait\Firebase\Firestore;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FirestoreRepository
{
    private $firestore;

    /**
     * FirestoreRepository constructor. Initialize Firestore Component
     * @param Firestore $firestore
     */
    public function __construct(Firestore $firestore)
    {
        $this->firestore = $firestore;
    }

    /**
     * Upload parsed CSV to Firestore collection
     * @param $file
     * @param $collection
     */
    public function uploadCsvToCollection($file, $collection)
    {
        $batch = $this->firestore->database()->batch();
        $items = $this->parseCsv($file);
        foreach ($items as $key => $value) {
            $collectionRef = $this->firestore->database()->collection($collection)->newDocument();
            $batch->set($collectionRef, $value);
        }
        $batch->commit();
    }

    /**
     * Upload parsed Excel to Firestore collection
     * @param $file
     * @param $collection
     */
    public function uploadExcelToCollection($file, $collection)
    {
        $batch = $this->firestore->database()->batch();
        $items = $this->parseExcel($file);
        foreach ($items as $key => $value) {
            $collectionRef = $this->firestore->database()->collection($collection)->newDocument();
            $batch->set($collectionRef, $value);
        }
        $batch->commit();
    }

    /**
     * Method to parse CSV file and returns array
     * @param string $file
     * @param string $delimiter
     * @return array|bool
     */
    public function parseCsv($file = '', $delimiter = ',')
    {
        if (!file_exists($file) || !is_readable($file))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($file, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Method to parse Excel .xlsx file and returns array
     * @param string $file
     * @return array
     */
    public function parseExcel($file = '')
    {
        $spreadsheet = IOFactory::load( $file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        foreach ($worksheet->getRowIterator() AS $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // This loops through all cells,
            $cells = [];
            foreach ($cellIterator as $cell) {
                $cells[] = $cell->getValue();
            }
            $rows[] = $cells;
        }
        $data = [];
        // Take first row as header and assign as key to all row cells
        $headers = $rows[0];
        foreach ($rows as $key => $row) {
            if($key != 0) {
                $item = [];
                foreach ($row as $i => $value) {
                    $item[$headers[$i]] = $row[$i];
                }
                array_push($data, $item);
            }
        }
        return $data;
    }
}
