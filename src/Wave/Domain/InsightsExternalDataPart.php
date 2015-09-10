<?php

/**
 * Domain class for a Wave InsightsExternalDataPart.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Domain;

/**
 * Domain class for a Wave InsightsExternalDataPart.
 *
 * The InsightsExternalDataPart object enables you to upload an external data
 * file that has been split into parts.
 *
 * The InsightsExternalDataPart object works with the InsightsExternalData object.
 * After you insert a row into the InsightsExternalData object, you can create
 * part objects to split up your data into parts. If your initial data file is
 * larger than 10 MB, split your file into parts that are smaller than 10 MB.
 */
class InsightsExternalDataPart
{
    /**
     * The part number.
     *
     * Part numbers are required to be in a contiguous sequence, starting with 1.
     * (For example, 1, 2, 3, etc.)
     *
     * @var integer
     */
    private $partNumber;

    /**
     * The ID of the InsightsExternalData object that this part belongs to.
     *
     * @var string
     */
    private $insightsExternalDataId;

    /**
     * The data bytes.
     *
     * Parts are required to be smaller than 10 MB. For data greater than 10 MB,
     * compress the file and then split it into parts. Only the gzip format is supported.
     *
     * @var Blob (Base64-encoded string)
     */
    private $dataFile;

    /**
     * The length of the data.
     *
     * This field is overwritten when data is uploaded.
     *
     * @var integer
     */
    private $dataLength;

    /**
     * The length of the compressed data.
     *
     * This field is overwritten when data is uploaded.
     *
     * @var integer
     */
    private $compressedDataLength;

    /**
     * Indicates whether the object has been moved to the Recycle Bin (true) or
     * not (false).
     *
     * @var boolean
     */
    private $isDeleted;

    /**
     * Set the part number.
     *
     * Part numbers are required to be in a contiguous sequence, starting with 1.
     * (For example, 1, 2, 3, etc.)
     *
     * @param integer $partNumber The part number.
     */
    public function setPartNumber($partNumber)
    {
        if ($partNumber < 1) {
            throw new \Exception(
                'InsightsExternalDataPart PartNumber must be 1 or higher. '
                . 'Provided value: ' . $partNumber
            );
        }

        $this->partNumber = $partNumber;
    }

    /**
     * Get the part number.
     *
     * @return integer The part number.
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }

    /**
     * Set the ID of the InsightsExternalData object that this part belongs to.
     *
     * @param string The ID of the InsightsExternalData object.
     */
    public function setInsightsExternalDataId($insightsExternalDataId)
    {
        $this->insightsExternalDataId = $insightsExternalDataId;
    }

    /**
     * Get the ID of the InsightsExternalData object that this part belongs to.
     *
     * @return string The ID of the InsightsExternalData object.
     */
    public function getInsightsExternalDataId()
    {
        return $this->insightsExternalDataId;
    }

    /**
     * Set the data bytes.
     *
     * Parts are required to be smaller than 10 MB. For data greater than 10 MB,
     * compress the file and then split it into parts. Only the gzip format is supported.
     *
     * @param Blob (Base64-encoded string) The data file in Base64 encoded string format.
     */
    public function setDataFile($dataFile)
    {
        $size = mb_strlen($dataFile, '8bit');
        
        if ($size > 10000) {
            throw new \Exception(
                'InsightsExternalDataPart DataFile size too big. '
                . 'Must be smaller than 10MB. '
                . 'Actual size: ' . $size . ' bytes.'
            );
        }
        
        $this->dataFile = $dataFile;
    }

    /**
     * Get the data bytes.
     *
     * @return Blob (Base64-encoded string) The data file in Base64 encoded string format.
     */
    public function getDataFile()
    {
        return $this->dataFile;
    }

    /**
     * Set the length of the data.
     *
     * This field is overwritten when data is uploaded.
     *
     * @param integer $dataLength The length of the data.
     */
    public function setDataLength($dataLength)
    {
        $this->dataLength = $dataLength;
    }

    /**
     * Get the length of the data.
     *
     * @return integer The length of the data.
     */
    public function getDataLength()
    {
        return $this->dataLength;
    }

    /**
     * Set the length of the compressed data.
     *
     * This field is overwritten when data is uploaded.
     *
     * @param integer $compressedDataLength The length of the compressed data.
     */
    public function setCompressedDataLength($compressedDataLength)
    {
        $this->compressedDataLength = $compressedDataLength;
    }

    /**
     * Get the length of the compressed data.
     *
     * @return integer The length of the compressed data.
     */
    
    public function getCompressedDataLength()
    {
        return $this->compressedDataLength;
    }

    /**
     * Set whether the object has been moved to the Recycle Bin (true) or not (false).
     *
     * @param boolean $isDeleted Whether the object has been moved to the
     *                           Recycle Bin or not.
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }

    /**
     * Get whether the object has been moved to the Recycle Bin (true) or not (false).
     *
     * @return boolean Whether the object has been moved to the Recycle Bin or not.
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
}
