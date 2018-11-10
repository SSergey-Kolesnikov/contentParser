<?php
if (!is_file('../index.php') && is_readable('../index.php')) {
    throw new Exception('Failed to mount the file api for MODx');
}

define('MODX_API_MODE', true);

require_once '../index.php';

if (!class_exists('Modx')) {
    throw new Exception('Unable to connect the main class MODx');
}

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->error->message = null;

class uModx {
    /**
     * Создает новый ресурс
     *
     * @param array $data
     * @param string $class
     * @param string $processor
     * @return object
     * @throws Exception
     */
    function createResource(array $data, $class = 'modDocument', $processor = 'resource/create') {
        global $modx;

        foreach (['pagetitle', 'alias', 'template'] as $required) {
            if (!isset($data[$required])) {
                throw new Exception('Required field "' . $required . '" for class "' . $class . '"');
            }
        }
        if (!isset($data['published'])) {
            $data['published'] = true;
        }
        if (!isset($data['show_in_tree'])) {
            $data['show_in_tree'] = true;
        }
        $response = $modx->runProcessor($processor, $data, $class);
        if (!empty($response->response['errors'])) {
            throw new Exception('Error : resource/create<br><pre>' . print_r($response->response['errors'], true));
        }
        return (object)$response->response['object'];
    }

    /**
     * Создает нового пользователя
     *
     * @param array $data
     * @param array $groupsList
     * @param integer $role
     */
    function createUser(array $data, array $groupsList = array('Administrator'), $role = 1) {
        global $app, $modx;

        foreach (['username', 'email'] as $required) {
            if (!isset($data[$required])) {
                throw new Exception('Required field "' . $required . '" for class "' . $class . '"');
            }
        }

        if (!isset($data['password']))
            $data['password'] = $app->generatePass();

        if (!isset($data['fullname']))
            $data['fullname'] = $data['username'];

        /**
         * Создание нового пользователя
         */
        $user = $modx->newObject('modUser');

        $user->set('username', $data['username']);
        $user->set('password', $data['password']);
        $user->save();

        /**
         * Создание профиля для пользователя
         */
        $profile = $modx->newObject('modUserProfile');

        $profile->set('fullname', $data['fullname']);
        $profile->set('email',  $data['email']);
        if ($data['photo'])
            $profile->set('photo',  $data['photo']);
        $user->addOne($profile);
        $profile->save();
        $user->save();

        /**
         * Добавление пользователя к определенным группам
         */
        $groups = array();
        foreach($groupsList as $groupName){
            $group = $modx->getObject('modUserGroup', array('name' => $groupName));

            $groupMember = $modx->newObject('modUserGroupMember');
            $groupMember->set('user_group', $group->get('id'));
            $groupMember->set('role', $role);
            $groups[] = $groupMember;
        }
        $user->addMany($groups);
        $user->save();

        return (object)$user;
    }
}