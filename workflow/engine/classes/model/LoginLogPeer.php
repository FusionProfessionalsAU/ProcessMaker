<?php
/**
 * LoginLogPeer.php
 * @package    workflow.engine.classes.model
 */

  // include base peer class
  require_once 'classes/model/om/BaseLoginLogPeer.php';

  // include object class
  include_once 'classes/model/LoginLog.php';


/**
 * Skeleton subclass for performing query and update operations on the 'LOGIN_LOG' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    workflow.engine.classes.model
 */
class LoginLogPeer extends BaseLoginLogPeer
{
  public static function retrieveByPK($pk, $con = null)
  {
    if ($con === null) {
      $con = Propel::getConnection(self::DATABASE_NAME);
    }

    $criteria = new Criteria(LoginLogPeer::DATABASE_NAME);

    $criteria->add(LoginLogPeer::LOG_UID, $pk);


    $v = LoginLogPeer::doSelect($criteria, $con);

    return !empty($v) > 0 ? $v[0] : null;
  }
}

