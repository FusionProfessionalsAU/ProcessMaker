<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ListUnassignedPeer.php';

/**
 * Base class that represents a row from the 'LIST_UNASSIGNED' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseListUnassigned extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ListUnassignedPeer
    */
    protected static $peer;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the del_index field.
     * @var        int
     */
    protected $del_index = 0;

    /**
     * The value for the tas_uid field.
     * @var        string
     */
    protected $tas_uid = '';

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid = '';

    /**
     * The value for the app_number field.
     * @var        int
     */
    protected $app_number = 0;

    /**
     * The value for the app_title field.
     * @var        string
     */
    protected $app_title = '';

    /**
     * The value for the app_pro_title field.
     * @var        string
     */
    protected $app_pro_title = '';

    /**
     * The value for the app_tas_title field.
     * @var        string
     */
    protected $app_tas_title = '';

    /**
     * The value for the del_previous_usr_username field.
     * @var        string
     */
    protected $del_previous_usr_username = '';

    /**
     * The value for the del_previous_usr_firstname field.
     * @var        string
     */
    protected $del_previous_usr_firstname = '';

    /**
     * The value for the del_previous_usr_lastname field.
     * @var        string
     */
    protected $del_previous_usr_lastname = '';

    /**
     * The value for the app_update_date field.
     * @var        int
     */
    protected $app_update_date;

    /**
     * The value for the del_previous_usr_uid field.
     * @var        string
     */
    protected $del_previous_usr_uid = '';

    /**
     * The value for the del_delegate_date field.
     * @var        int
     */
    protected $del_delegate_date;

    /**
     * The value for the del_due_date field.
     * @var        int
     */
    protected $del_due_date;

    /**
     * The value for the del_priority field.
     * @var        string
     */
    protected $del_priority = '3';

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Get the [app_uid] column value.
     * 
     * @return     string
     */
    public function getAppUid()
    {

        return $this->app_uid;
    }

    /**
     * Get the [del_index] column value.
     * 
     * @return     int
     */
    public function getDelIndex()
    {

        return $this->del_index;
    }

    /**
     * Get the [tas_uid] column value.
     * 
     * @return     string
     */
    public function getTasUid()
    {

        return $this->tas_uid;
    }

    /**
     * Get the [pro_uid] column value.
     * 
     * @return     string
     */
    public function getProUid()
    {

        return $this->pro_uid;
    }

    /**
     * Get the [app_number] column value.
     * 
     * @return     int
     */
    public function getAppNumber()
    {

        return $this->app_number;
    }

    /**
     * Get the [app_title] column value.
     * 
     * @return     string
     */
    public function getAppTitle()
    {

        return $this->app_title;
    }

    /**
     * Get the [app_pro_title] column value.
     * 
     * @return     string
     */
    public function getAppProTitle()
    {

        return $this->app_pro_title;
    }

    /**
     * Get the [app_tas_title] column value.
     * 
     * @return     string
     */
    public function getAppTasTitle()
    {

        return $this->app_tas_title;
    }

    /**
     * Get the [del_previous_usr_username] column value.
     * 
     * @return     string
     */
    public function getDelPreviousUsrUsername()
    {

        return $this->del_previous_usr_username;
    }

    /**
     * Get the [del_previous_usr_firstname] column value.
     * 
     * @return     string
     */
    public function getDelPreviousUsrFirstname()
    {

        return $this->del_previous_usr_firstname;
    }

    /**
     * Get the [del_previous_usr_lastname] column value.
     * 
     * @return     string
     */
    public function getDelPreviousUsrLastname()
    {

        return $this->del_previous_usr_lastname;
    }

    /**
     * Get the [optionally formatted] [app_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getAppUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->app_update_date === null || $this->app_update_date === '') {
            return null;
        } elseif (!is_int($this->app_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->app_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [app_update_date] as date/time value: " .
                    var_export($this->app_update_date, true));
            }
        } else {
            $ts = $this->app_update_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [del_previous_usr_uid] column value.
     * 
     * @return     string
     */
    public function getDelPreviousUsrUid()
    {

        return $this->del_previous_usr_uid;
    }

    /**
     * Get the [optionally formatted] [del_delegate_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDelDelegateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->del_delegate_date === null || $this->del_delegate_date === '') {
            return null;
        } elseif (!is_int($this->del_delegate_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->del_delegate_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [del_delegate_date] as date/time value: " .
                    var_export($this->del_delegate_date, true));
            }
        } else {
            $ts = $this->del_delegate_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [optionally formatted] [del_due_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDelDueDate($format = 'Y-m-d H:i:s')
    {

        if ($this->del_due_date === null || $this->del_due_date === '') {
            return null;
        } elseif (!is_int($this->del_due_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->del_due_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [del_due_date] as date/time value: " .
                    var_export($this->del_due_date, true));
            }
        } else {
            $ts = $this->del_due_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [del_priority] column value.
     * 
     * @return     string
     */
    public function getDelPriority()
    {

        return $this->del_priority;
    }

    /**
     * Set the value of [app_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_uid !== $v || $v === '') {
            $this->app_uid = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::APP_UID;
        }

    } // setAppUid()

    /**
     * Set the value of [del_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_index !== $v || $v === 0) {
            $this->del_index = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_INDEX;
        }

    } // setDelIndex()

    /**
     * Set the value of [tas_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setTasUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->tas_uid !== $v || $v === '') {
            $this->tas_uid = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::TAS_UID;
        }

    } // setTasUid()

    /**
     * Set the value of [pro_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setProUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->pro_uid !== $v || $v === '') {
            $this->pro_uid = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [app_number] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppNumber($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->app_number !== $v || $v === 0) {
            $this->app_number = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::APP_NUMBER;
        }

    } // setAppNumber()

    /**
     * Set the value of [app_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_title !== $v || $v === '') {
            $this->app_title = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::APP_TITLE;
        }

    } // setAppTitle()

    /**
     * Set the value of [app_pro_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppProTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_pro_title !== $v || $v === '') {
            $this->app_pro_title = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::APP_PRO_TITLE;
        }

    } // setAppProTitle()

    /**
     * Set the value of [app_tas_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppTasTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_tas_title !== $v || $v === '') {
            $this->app_tas_title = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::APP_TAS_TITLE;
        }

    } // setAppTasTitle()

    /**
     * Set the value of [del_previous_usr_username] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelPreviousUsrUsername($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_previous_usr_username !== $v || $v === '') {
            $this->del_previous_usr_username = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_PREVIOUS_USR_USERNAME;
        }

    } // setDelPreviousUsrUsername()

    /**
     * Set the value of [del_previous_usr_firstname] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelPreviousUsrFirstname($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_previous_usr_firstname !== $v || $v === '') {
            $this->del_previous_usr_firstname = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_PREVIOUS_USR_FIRSTNAME;
        }

    } // setDelPreviousUsrFirstname()

    /**
     * Set the value of [del_previous_usr_lastname] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelPreviousUsrLastname($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_previous_usr_lastname !== $v || $v === '') {
            $this->del_previous_usr_lastname = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_PREVIOUS_USR_LASTNAME;
        }

    } // setDelPreviousUsrLastname()

    /**
     * Set the value of [app_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setAppUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [app_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->app_update_date !== $ts) {
            $this->app_update_date = $ts;
            $this->modifiedColumns[] = ListUnassignedPeer::APP_UPDATE_DATE;
        }

    } // setAppUpdateDate()

    /**
     * Set the value of [del_previous_usr_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelPreviousUsrUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_previous_usr_uid !== $v || $v === '') {
            $this->del_previous_usr_uid = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_PREVIOUS_USR_UID;
        }

    } // setDelPreviousUsrUid()

    /**
     * Set the value of [del_delegate_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelDelegateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [del_delegate_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->del_delegate_date !== $ts) {
            $this->del_delegate_date = $ts;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_DELEGATE_DATE;
        }

    } // setDelDelegateDate()

    /**
     * Set the value of [del_due_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelDueDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [del_due_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->del_due_date !== $ts) {
            $this->del_due_date = $ts;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_DUE_DATE;
        }

    } // setDelDueDate()

    /**
     * Set the value of [del_priority] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDelPriority($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->del_priority !== $v || $v === '3') {
            $this->del_priority = $v;
            $this->modifiedColumns[] = ListUnassignedPeer::DEL_PRIORITY;
        }

    } // setDelPriority()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (1-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
     * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
     * @return     int next starting column
     * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate(ResultSet $rs, $startcol = 1)
    {
        try {

            $this->app_uid = $rs->getString($startcol + 0);

            $this->del_index = $rs->getInt($startcol + 1);

            $this->tas_uid = $rs->getString($startcol + 2);

            $this->pro_uid = $rs->getString($startcol + 3);

            $this->app_number = $rs->getInt($startcol + 4);

            $this->app_title = $rs->getString($startcol + 5);

            $this->app_pro_title = $rs->getString($startcol + 6);

            $this->app_tas_title = $rs->getString($startcol + 7);

            $this->del_previous_usr_username = $rs->getString($startcol + 8);

            $this->del_previous_usr_firstname = $rs->getString($startcol + 9);

            $this->del_previous_usr_lastname = $rs->getString($startcol + 10);

            $this->app_update_date = $rs->getTimestamp($startcol + 11, null);

            $this->del_previous_usr_uid = $rs->getString($startcol + 12);

            $this->del_delegate_date = $rs->getTimestamp($startcol + 13, null);

            $this->del_due_date = $rs->getTimestamp($startcol + 14, null);

            $this->del_priority = $rs->getString($startcol + 15);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 16; // 16 = ListUnassignedPeer::NUM_COLUMNS - ListUnassignedPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating ListUnassigned object", $e);
        }
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      Connection $con
     * @return     void
     * @throws     PropelException
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ListUnassignedPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            ListUnassignedPeer::doDelete($this, $con);
            $this->setDeleted(true);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.  If the object is new,
     * it inserts it; otherwise an update is performed.  This method
     * wraps the doSave() worker method in a transaction.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update
     * @throws     PropelException
     * @see        doSave()
     */
    public function save($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ListUnassignedPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            $affectedRows = $this->doSave($con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update and any referring
     * @throws     PropelException
     * @see        save()
     */
    protected function doSave($con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;


            // If this object has been modified, then save it to the database.
            if ($this->isModified()) {
                if ($this->isNew()) {
                    $pk = ListUnassignedPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += ListUnassignedPeer::doUpdate($this, $con);
                }
                $this->resetModified(); // [HL] After being saved an object is no longer 'modified'
            }

            $this->alreadyInSave = false;
        }
        return $affectedRows;
    } // doSave()

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return     array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param      mixed $columns Column name or an array of column names.
     * @return     boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();
            return true;
        } else {
            $this->validationFailures = $res;
            return false;
        }
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param      array $columns Array of column names to validate.
     * @return     mixed <code>true</code> if all validations pass; 
                   array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = ListUnassignedPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = ListUnassignedPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->getByPosition($pos);
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return     mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch($pos) {
            case 0:
                return $this->getAppUid();
                break;
            case 1:
                return $this->getDelIndex();
                break;
            case 2:
                return $this->getTasUid();
                break;
            case 3:
                return $this->getProUid();
                break;
            case 4:
                return $this->getAppNumber();
                break;
            case 5:
                return $this->getAppTitle();
                break;
            case 6:
                return $this->getAppProTitle();
                break;
            case 7:
                return $this->getAppTasTitle();
                break;
            case 8:
                return $this->getDelPreviousUsrUsername();
                break;
            case 9:
                return $this->getDelPreviousUsrFirstname();
                break;
            case 10:
                return $this->getDelPreviousUsrLastname();
                break;
            case 11:
                return $this->getAppUpdateDate();
                break;
            case 12:
                return $this->getDelPreviousUsrUid();
                break;
            case 13:
                return $this->getDelDelegateDate();
                break;
            case 14:
                return $this->getDelDueDate();
                break;
            case 15:
                return $this->getDelPriority();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param      string $keyType One of the class type constants TYPE_PHPNAME,
     *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = ListUnassignedPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getAppUid(),
            $keys[1] => $this->getDelIndex(),
            $keys[2] => $this->getTasUid(),
            $keys[3] => $this->getProUid(),
            $keys[4] => $this->getAppNumber(),
            $keys[5] => $this->getAppTitle(),
            $keys[6] => $this->getAppProTitle(),
            $keys[7] => $this->getAppTasTitle(),
            $keys[8] => $this->getDelPreviousUsrUsername(),
            $keys[9] => $this->getDelPreviousUsrFirstname(),
            $keys[10] => $this->getDelPreviousUsrLastname(),
            $keys[11] => $this->getAppUpdateDate(),
            $keys[12] => $this->getDelPreviousUsrUid(),
            $keys[13] => $this->getDelDelegateDate(),
            $keys[14] => $this->getDelDueDate(),
            $keys[15] => $this->getDelPriority(),
        );
        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name peer name
     * @param      mixed $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = ListUnassignedPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return     void
     */
    public function setByPosition($pos, $value)
    {
        switch($pos) {
            case 0:
                $this->setAppUid($value);
                break;
            case 1:
                $this->setDelIndex($value);
                break;
            case 2:
                $this->setTasUid($value);
                break;
            case 3:
                $this->setProUid($value);
                break;
            case 4:
                $this->setAppNumber($value);
                break;
            case 5:
                $this->setAppTitle($value);
                break;
            case 6:
                $this->setAppProTitle($value);
                break;
            case 7:
                $this->setAppTasTitle($value);
                break;
            case 8:
                $this->setDelPreviousUsrUsername($value);
                break;
            case 9:
                $this->setDelPreviousUsrFirstname($value);
                break;
            case 10:
                $this->setDelPreviousUsrLastname($value);
                break;
            case 11:
                $this->setAppUpdateDate($value);
                break;
            case 12:
                $this->setDelPreviousUsrUid($value);
                break;
            case 13:
                $this->setDelDelegateDate($value);
                break;
            case 14:
                $this->setDelDueDate($value);
                break;
            case 15:
                $this->setDelPriority($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
     * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return     void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = ListUnassignedPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setAppUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDelIndex($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setTasUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setProUid($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setAppNumber($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setAppTitle($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setAppProTitle($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setAppTasTitle($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setDelPreviousUsrUsername($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setDelPreviousUsrFirstname($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setDelPreviousUsrLastname($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setAppUpdateDate($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setDelPreviousUsrUid($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setDelDelegateDate($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setDelDueDate($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setDelPriority($arr[$keys[15]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ListUnassignedPeer::DATABASE_NAME);

        if ($this->isColumnModified(ListUnassignedPeer::APP_UID)) {
            $criteria->add(ListUnassignedPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_INDEX)) {
            $criteria->add(ListUnassignedPeer::DEL_INDEX, $this->del_index);
        }

        if ($this->isColumnModified(ListUnassignedPeer::TAS_UID)) {
            $criteria->add(ListUnassignedPeer::TAS_UID, $this->tas_uid);
        }

        if ($this->isColumnModified(ListUnassignedPeer::PRO_UID)) {
            $criteria->add(ListUnassignedPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(ListUnassignedPeer::APP_NUMBER)) {
            $criteria->add(ListUnassignedPeer::APP_NUMBER, $this->app_number);
        }

        if ($this->isColumnModified(ListUnassignedPeer::APP_TITLE)) {
            $criteria->add(ListUnassignedPeer::APP_TITLE, $this->app_title);
        }

        if ($this->isColumnModified(ListUnassignedPeer::APP_PRO_TITLE)) {
            $criteria->add(ListUnassignedPeer::APP_PRO_TITLE, $this->app_pro_title);
        }

        if ($this->isColumnModified(ListUnassignedPeer::APP_TAS_TITLE)) {
            $criteria->add(ListUnassignedPeer::APP_TAS_TITLE, $this->app_tas_title);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_PREVIOUS_USR_USERNAME)) {
            $criteria->add(ListUnassignedPeer::DEL_PREVIOUS_USR_USERNAME, $this->del_previous_usr_username);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_PREVIOUS_USR_FIRSTNAME)) {
            $criteria->add(ListUnassignedPeer::DEL_PREVIOUS_USR_FIRSTNAME, $this->del_previous_usr_firstname);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_PREVIOUS_USR_LASTNAME)) {
            $criteria->add(ListUnassignedPeer::DEL_PREVIOUS_USR_LASTNAME, $this->del_previous_usr_lastname);
        }

        if ($this->isColumnModified(ListUnassignedPeer::APP_UPDATE_DATE)) {
            $criteria->add(ListUnassignedPeer::APP_UPDATE_DATE, $this->app_update_date);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_PREVIOUS_USR_UID)) {
            $criteria->add(ListUnassignedPeer::DEL_PREVIOUS_USR_UID, $this->del_previous_usr_uid);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_DELEGATE_DATE)) {
            $criteria->add(ListUnassignedPeer::DEL_DELEGATE_DATE, $this->del_delegate_date);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_DUE_DATE)) {
            $criteria->add(ListUnassignedPeer::DEL_DUE_DATE, $this->del_due_date);
        }

        if ($this->isColumnModified(ListUnassignedPeer::DEL_PRIORITY)) {
            $criteria->add(ListUnassignedPeer::DEL_PRIORITY, $this->del_priority);
        }


        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return     Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(ListUnassignedPeer::DATABASE_NAME);

        $criteria->add(ListUnassignedPeer::APP_UID, $this->app_uid);
        $criteria->add(ListUnassignedPeer::DEL_INDEX, $this->del_index);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return     array
     */
    public function getPrimaryKey()
    {
        $pks = array();

        $pks[0] = $this->getAppUid();

        $pks[1] = $this->getDelIndex();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param      array $keys The elements of the composite key (order must match the order in XML file).
     * @return     void
     */
    public function setPrimaryKey($keys)
    {

        $this->setAppUid($keys[0]);

        $this->setDelIndex($keys[1]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of ListUnassigned (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setTasUid($this->tas_uid);

        $copyObj->setProUid($this->pro_uid);

        $copyObj->setAppNumber($this->app_number);

        $copyObj->setAppTitle($this->app_title);

        $copyObj->setAppProTitle($this->app_pro_title);

        $copyObj->setAppTasTitle($this->app_tas_title);

        $copyObj->setDelPreviousUsrUsername($this->del_previous_usr_username);

        $copyObj->setDelPreviousUsrFirstname($this->del_previous_usr_firstname);

        $copyObj->setDelPreviousUsrLastname($this->del_previous_usr_lastname);

        $copyObj->setAppUpdateDate($this->app_update_date);

        $copyObj->setDelPreviousUsrUid($this->del_previous_usr_uid);

        $copyObj->setDelDelegateDate($this->del_delegate_date);

        $copyObj->setDelDueDate($this->del_due_date);

        $copyObj->setDelPriority($this->del_priority);


        $copyObj->setNew(true);

        $copyObj->setAppUid(''); // this is a pkey column, so set to default value

        $copyObj->setDelIndex('0'); // this is a pkey column, so set to default value

    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return     ListUnassigned Clone of current object.
     * @throws     PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);
        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return     ListUnassignedPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ListUnassignedPeer();
        }
        return self::$peer;
    }
}

