<?php

/**
 * Domain class for a Wave InsightsExternalData, and enumerations for Picklists.
 *
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Wave\Domain;

use skyflow\Domain\AbstractModel;
use skyflow\Domain\InsightsExternalData\Action;
use skyflow\Domain\InsightsExternalData\Operation;
use skyflow\Domain\InsightsExternalData\NotificationSent;
use skyflow\Domain\InsightsExternalData\Status;

{
    /**
     * Domain class for a Wave InsightsExternalData.
     *
     * The InsightsExternalData object enables you to configure and control external
     * data uploads. You can use it to provide metadata, trigger the start of the
     * upload process, check status, and request cancelation and cleanup.
     *
     * @link https://developer.salesforce.com/docs/atlas.en-us.bi_dev_guide_ext_data.meta/bi_dev_guide_ext_data/bi_ext_data_object_externaldata.htm The InsightsExternalData object documentation.
     */
    class InsightsExternalData extends AbstractModel
    {
        /**
         * The alias of a dataset, which must be unique across an organization.
         *
         * The alias must follow the same guidelines as other field names, except
         * that they can’t end with “__c”. Can be up to 80 characters.
         *
         * @var string
         * @todo setEdgemartAlias() must respect these restrictions.
         */
        private $edgemartAlias;

        /**
         * Set the dataset alias.
         *
         * The alias must follow the same guidelines as other field names, except
         * that they can’t end with “__c”. Can be up to 80 characters.
         *
         * @param  $edgemartAlias The dataset alias.
         */
        public function setEdgemartAlias($edgemartAlias)
        {
            $length = strlen($edgemartAlias);

            if ($length > 80) {
                throw new \Exception(
                    'InsightsExternalData EdgemartAlias field too long. '
                    . 'Can be up to 80 characters. '
                    . 'Provided value: ' . $edgemartAlias
                );
            }

            if (substr($edgemartAlias, $length - 3, 3) === '__c') {
                throw new \Exception(
                    'InsightsExternalData EdgemartAlias field can\'t end with __c '
                    . 'Provided value: ' . $edgemartAlias
                );
            }

            $this->edgemartAlias = $edgemartAlias;
        }

        /**
         * Get the dataset alias.
         *
         * @return string The dataset alias.
         */
        public function getEdgemartAlias()
        {
            return $this->edgemartAlias;
        }

        /**
         * The name of the app that contains the dataset.
         *
         * If the name is omitted when you’re creating a dataset, the name of the
         * user’s private app is used.
         *
         * If the name is omitted for an existing dataset, the system resolves the
         * app name.
         *
         * If the name is specified for an existing dataset, the name is required to
         * match the name of the current app that contains the dataset.
         *
         * @var string
         */
        private $edgemartContainer;

        /**
         * Set the name of the app that contains the dataset.
         *
         * @param string $edgemartContainer The name of the app that contains the dataset.
         */
        public function setEdgemartContainer($edgemartContainer)
        {
            $this->edgemartContainer = $edgemartContainer;
        }

        /**
         * Get the name of the app that contains the dataset.
         *
         * @return string The name of the app that contains the dataset.
         */
        public function getEdgemartContainer()
        {
            return $this->edgemartContainer;
        }

        /**
         * Metadata in JSON format, which describes the structure of the uploaded file.
         *
         * @var Blob (Base64-encoded string)
         */
        private $metadataJson;

        /**
         * Set the metadata in JSON format.
         *
         * The metadata describes the structure of the uploaded file. It must be
         * of type Blob (Base64-encoded string). setMetadataJson parameter must
         * be of type string, it will be automatically encoded into Base64.
         *
         * @param string $metadataJson The JSON metadata as non Base64 string.
         */
        public function setMetadataJson($metadataJson)
        {
            $this->metadataJson = base64_encode($metadataJson);
        }

        /**
         * Get the metadata in JSON format.
         *
         * The metadata is stored as Blob (Base64-encoded string). It is returned
         * decoded when using getMetadataJson().
         *
         * @return string The metadata in JSON format, decoded.
         */
        public function getMetadataJson()
        {
            return $this->metadataJson;
        }

        /**
         * The format of the uploaded data. Must be "Csv".
         *
         * @var string
         */
        private $format = 'Csv';

        /**
         * Set the format of the uploaded data.
         *
         * The format has to be 'Csv'. I don't know why i wrote this method...
         *
         * @param string $format The format of the uploaded data.
         */
        public function setFormat($format)
        {
            $this->format = $format;
        }

        /**
         * Get the format of the uploaded data.
         *
         * @return string The format of the uploaded data.
         */
        public function getFormat()
        {
            return $this->format;
        }

        /**
         * Indicates which operation to use when you’re loading data into the dataset.
         *
         * @var Operation
         */
        private $operation;

        /**
         * Set the operation to use when you’re loading data into the dataset.
         *
         * @param Operation $operation The operation.
         */
        public function setOperation(Operation $operation)
        {
            $this->operation = $operation;
        }

        /**
         * Get the operation to use when you're loading data into the dataset.
         *
         * @return Operation The operation.
         */
        public function getOperation()
        {
            return $this->operation;
        }

        /**
         * The status of this data upload.
         *
         * The initial value is null. This field is not editable.
         *
         * @var Status
         */
        private $status;

        /**
         * Get the status of this data upload.
         *
         * The status field is not editable.
         *
         * @return Status
         */
        public function getStatus()
        {
            return $this->status;
        }

        /**
         * The action to perform on this data.
         *
         * @var Action
         */
        private $action;

        /**
         * Set the action to perform on this data.
         *
         * @param Action $action The action to perform.
         */
        public function setAction(Action $action)
        {
            $this->action = $action;
        }

        /**
         * Get the action to perform on this data.
         *
         * @return Action The action to perform.
         */
        public function getAction()
        {
            return $this->action;
        }

        /**
         * Reserved for future use.
         *
         * When true, indicates that file parts have been divided on row
         * boundaries and can be processed independently of each other.
         *
         * The default is false.
         *
         * @var boolean
         */
        private $isIndependentParts = false;

        /**
         * Set if that file parts have been divided on row boundaries and can be
         * processed independently of each other.
         *
         * @param boolean $isIndependentParts The value for this field.
         */
        public function setIsIndependentParts($isIndependentParts)
        {
            $this->isIndependentParts = $isIndependentParts;
        }

        /**
         * Get if that file parts have been divided on row boundaries and can be
         * processed independently of each other.
         *
         * @return boolean The value of this field.
         */
        public function getIsIndependentParts()
        {
            return $this->isIndependentParts;
        }

        /**
         * Reserved for future use.
         *
         * When false, indicates that this upload depends on the previous upload
         * to the same dataset name.
         *
         * @var boolean
         */
        private $isDependentOnLastUpload;

        /**
         * Set if that this upload depends on the previous upload to the same
         * dataset name.
         *
         * @param boolean $isDependentOnLastUpload The value for this field.
         */
        public function setIsDependentOnLastUpload($isDependentOnLastUpload)
        {
            $this->isDependentOnLastUpload = $isDependentOnLastUpload;
        }

        /**
         * Get if that this upload depends on the previous upload to the same
         * dataset name.
         *
         * @return boolean The value of this field.
         */
        public function getIsDependentOnLastUpload()
        {
            return $this->isDependentOnLastUpload;
        }

        /**
         * The length of the compressed metadata .json file.
         *
         * This field is overwritten when data is uploaded. This system field is
         * not editable.
         *
         * @var integer
         */
        private $metaDataLength;

        /**
         * Get the length of the compressed metadata .json file.
         *
         * This system field is not editable.
         *
         * @return integer The length of the compressed editable .json file.
         */
        public function getMetaDataLength()
        {
            return $this->metaDataLength;
        }

        /**
         * The length of the compressed metadata .json file.
         *
         * This field is overwritten when data is uploaded. This system field is
         * not editable.
         *
         * @var integer
         */
        private $compressedMetadataLength;

        /**
         * Get the length of the compressed metadata .json file.
         *
         * This system field is not editable.
         *
         * @return integer The length of the compressed editable .json file.
         */
        public function getCompressedMetadataLength()
        {
            return $this->compressedMetadataLength;
        }

        /**
         * Indicates when to send notifications about the upload.
         *
         * @var NotificationSent
         */
        private $notificationSent;

        /**
         * Set when to send notifications about the upload.
         *
         * @param NotificationSent $notificationSent When to send notifications
         *                                           about the upload.
         */
        public function setNotificationSent(NotificationSent $notificationSent)
        {
            $this->notificationSent = $notificationSent;
        }

        /**
         * Get when to send notifications about the upload.
         *
         * @return NotificationSent When to send notifications about the upload.
         */
        public function getNotificationSent()
        {
            return $this->notificationSent;
        }

        /**
         * The email address to send notifications to.
         *
         * Can be up to 255 characters and can contain only one email address.
         * Defaults to the current user’s email address.
         */
        private $notificationEmail;

        /**
         * Set the email address to send notifications to.
         *
         * Can be up to 255 characters and can contain only one email address.
         *
         * @param string $notificationEmail The notification email address.
         */
        public function setNotificationEmail($notificationEmail)
        {
            $this->notificationEmail = $notificationEmail;
        }

        /**
         * Get the email address to send notifications to.
         *
         * @return string The notification email address.
         */
        public function getNotificationEmail()
        {
            return $this->notificationEmail;
        }

        /**
         * The display name for the dataset.
         *
         * Can be up to 255 characters.
         *
         * @var string
         */
        private $edgemartLabel;

        /**
         * Set the display name for the dataset.
         *
         * @param string $edgemartLabel The display name for the dataset.
         */
        public function setEdgemartLabel($edgemartLabel)
        {
            if (strlen($edgemartLabel) > 255) {
                throw new \Exception(
                    'InsightsExternalData EdgemartLabel field too long. '
                    . 'Can be up to 255 characters. '
                    . 'Provided value: ' . $edgemartLabel
                );
            }

            $this->edgemartLabel = $edgemartLabel;
        }

        /**
         * Get the display name for the dataset.
         *
         * @return string The display name for the dataset.
         */
        public function getEdgemartLabel()
        {
            return $this->edgemartLabel;
        }

        /**
         * Indicates whether the object has been moved to the Recycle Bin (true)
         * or not (false).
         *
         * This system field is not editable.
         *
         * @var boolean
         */
        private $isDeleted;

        /**
         * Get whether the object has been moved to the Recycle Bin (true)
         * or not (false).
         *
         * This system field is not editable.
         *
         * @return boolean Whether the object has been moved to the Recycle Bin or not.
         */
        public function getisDeleted()
        {
            return $this->isDeleted;
        }

        /**
         * The unique ID of the dataflow that was used to create the dataset.
         *
         * For dataflows that were created in API version 34.0 and later.
         * You can use this field to get the status of the dataflow.
         * This system field is not editable.
         *
         * @var string
         */
        private $dataflow;

        /**
         * Get the unique ID of the dataflow that was used to create the dataset.
         *
         * This system field is not editable.
         *
         * @return string The unique ID of the dataflow.
         */
        public function getDataFlow()
        {
            return $this->dataFlow;
        }

        /**
         * Deprecated in API version 34.0. Use the Dataflow attribute instead.
         *
         * @deprecated
         */
        //private $workflowId;
        
        /**
         * The time when the upload was submitted or set to Process.
         *
         * This system field is not editable.
         *
         * @var string
         */
        private $submittedDate;

        /**
         * Get the time when the upload was submitted or set to Process.
         *
         * This system field is not editable.
         *
         * @return string The time when the upload was submitted or set to Process.
         */
        public function getSubmittedDate()
        {
            return $this->submittedDate;
        }

        /**
         * Identifier of the external data file, such as the file name.
         *
         * The value does not have to be unique.
         *
         * It can contain only alphanumeric characters and underscores. It must
         * begin with a letter, not include spaces, not end with an underscore,
         * and not contain two consecutive underscores.
         * The maximum file name is 255 characters.
         *
         * @var string
         */
        private $fileName;

        /**
         * Set the identifier of the external data file, such as the file name.
         *
         * It can contain only alphanumeric characters and underscores. It must
         * begin with a letter, not include spaces, not end with an underscore,
         * and not contain two consecutive underscores.
         * The maximum file name is 255 characters.
         *
         * @param string The identifier of the external data file.
         */
        public function setFileName($fileName)
        {
            if (strlen($fileName) > 255) {
                throw new \Exception(
                    'InsightsExternalData FileName field too long. '
                    . 'Can be up to 255 characters. '
                    . 'Provided value: ' . $fileName
                );
            }
            if (preg_match('/^[a-z](?:(?:[a-z])|(?:(_)(?!\1)))*[a-z]$/i')) {
                $this->fileName = $fileName;
            } else {
                throw new \Exception(
                    'InsightsExternalData FileName field invalid. '
                    . 'Can contain only alphanumeric characters and underscores, '
                    . 'must begin with a letter, '
                    . 'not include spaces, '
                    . 'not end with an underscore, '
                    . 'and not contain two consecutive underscores. '
                    . 'Provided value: ' . $fileName
                );
            }
        }

        /**
         * Get the identifier of the external data file.
         *
         * @return string The identifier of the external data file.
         */
        public function getFileName()
        {
            return $this->fileName;
        }

        /**
         * The description of the dataset.
         *
         * This is only used when creating the dataset.
         *
         * @var string
         */
        private $description;

        /**
         * Set the description of the dataset.
         *
         * @param string $description The descripton of the dataset.
         */
        public function setDescription($description)
        {
            $this->description = $description;
        }

        /**
         * Get the description of the dataset.
         *
         * @return string The description of the dataset.
         */
        public function getDescription()
        {
            return $this->description;
        }

        /**
         * The reason for the file upload failed or has warnings.
         *
         * This system field is not editable.
         *
         * @var string
         */
        private $statusMessage;

        /**
         * Get the reason for the file upload failed or has warnings.
         *
         * This system field is not editable.
         *
         * @return string The reason for the file upload failed or has warnings.
         */
        public function getStatusMessage()
        {
            return $this->statusMessage;
        }
    }
}

namespace Wave\Domain\InsightsExternalData;

{
    /**
     * Indicates which operation to use when you’re loading data into the dataset.
     *
     * A metadata JSON file is required for the append, upsert, and delete operations.
     */
    class Operation extends SplEnum
    {
        /**
         * Append all data to the dataset. Creates a dataset if it doesn’t exist.
         * If the dataset or rows contain a unique identifier, the append
         * operation is not allowed.
         */
        const APPEND = 'Append';

        /**
         * Delete the rows from the dataset. The rows to delete must contain one
         * (and only one) field with a unique identifier.
         */
        const DELETE = 'Delete';

        /**
         * Create a dataset with the given data, and replace the dataset if it exists.
         */
        const OVERWRITE = 'Overwrite';

        /**
         * Insert or update rows in the dataset. Creates a dataset if it doesn’t
         * exist. The rows to upsert must contain one (and only one) field with
         * a unique identifier.
         */
        const UPSERT = 'Upsert';
    }

    /**
     * The status of this data upload.
     */
    class Status extends SplEnum
    {
        /**
         * The initial value is null.
         */
        const __default = null;

        /**
         * The data upload job was completed successfully.
         * Data parts are retained for seven days after completion.
         */
        const COMPLETED = 'Completed';

        /**
         * The data upload job completed, but contains warnings.
         * Data parts are retained for seven days after completion.
         */
        const COMPLETED_WITH_WARNINGS = 'CompletedWithWarnings';

        /**
         * The data upload job failed.
         * Data parts are retained for seven days after failure.
         */
        const FAILED = 'Failed';

        /**
         * The data upload job is in progress.
         */
        const IN_PROGRESS = 'InProgress';

        /**
         * The data upload job has been created.
         */
        const _NEW = 'New';

        /**
         * The data upload job was aborted on user request.
         * Data parts have been removed.
         */
        const NOT_PROCESSED = 'NotProcessed';

        /**
         * The data upload job has been scheduled.
         */
        const QUEUED = 'Queued';
    }

    /**
     * The action to perform on this data.
     */
    class Action extends SplEnum
    {
        /**
         * Reserved for future use.
         *
         * The user no longer wants to upload the data and is requesting that
         * the system stop processing, if possible.
         */
        const ABORT = 'Abort';

        /**
         * Reserved for future use.
         *
         * The user wants to remove uploaded data parts as soon as possible.
         * Implies that an Abort status is queued.
         */
        const DELETE = 'Delete';

        /**
         * The user has not completed the data upload. Default value when the
         * object is created.
         */
        const NONE = 'None';

        /**
         * The user has completed the data upload and is requesting that the
         * system process the data.
         */
        const PROCESS = 'Process';
    }

    /**
     * Indicates when to send notifications about the upload.
     */
    class NotificationSent extends SplEnum
    {
        /**
         * Always send notifications.
         */
        const ALWAYS = 'Always';

        /**
         * Never send notifications.
         */
        const NEVER = 'Never';

        /**
         * Send notifications if the upload process failed.
         */
        const FAILURES = 'Failures';

        /**
         * Send notifications if warnings or errors occurred during the upload.
         */
        const WARNINGS = 'Warnings';
    }
}
