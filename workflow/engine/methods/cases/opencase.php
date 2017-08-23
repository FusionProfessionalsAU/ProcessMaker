<?php
$RBAC->requirePermissions('PM_CASES');

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'CASES';

$_POST['qs'] = '';

$arrayAux = explode('?', $_SERVER['REQUEST_URI']);

preg_match('/^.*\/cases\/opencase\/([\w\-]{32})$/', $arrayAux[0], $arrayMatch);

$applicationUid = $arrayMatch[1];

$case = new \ProcessMaker\BusinessModel\Cases();

$arrayApplicationData = $case->getApplicationRecordByPk($applicationUid, [], false);

$G_PUBLISH = new Publisher();

if ($arrayApplicationData !== false) {
    $_SESSION['__CD__'] = '../';
    $_SESSION['__OPEN_APPLICATION_UID__'] = $applicationUid;

    $G_PUBLISH->AddContent('view', 'cases/cases_Load');

    $headPublisher = &headPublisher::getSingleton();
    $headPublisher->addScriptFile('/jscore/src/PM.js');
    $headPublisher->addScriptFile('/jscore/src/Sessions.js');
} else {
    $G_PUBLISH->AddContent(
        'xmlform',
        'xmlform',
        'login/showMessage',
        '',
        ['MESSAGE' => \G::LoadTranslation('ID_CASE_DOES_NOT_EXIST2', ['app_uid', $applicationUid])]
    );
}

G::RenderPage('publish');
