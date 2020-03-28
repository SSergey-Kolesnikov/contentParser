<?php
class uModx extends App {
    /** @var modX $modx */
    public $modx;

    /**
     * uModx constructor
     *
     * @param array $config
     *
     * @throws Exception
     */
    function __construct(array $config) {
        parent::__construct($config);

        define('MODX_API_MODE', true);

        if (!is_file('../index.php') && is_readable('../index.php')) {
            throw new Exception('Failed to mount the file api for MODX');
        }
        /** @noinspection PhpIncludeInspection */
        require_once('../index.php');

        if (!class_exists('Modx')) {
            throw new Exception('Unable to connect the main class MODX');
        }

        /** @var modX $modx */
        $this->modx = $modx;

        $this->modx->getService('error', 'error.modError');
        $this->modx->setLogLevel(modX::LOG_LEVEL_ERROR);
        $this->modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
    }

    /**
     * Создание нового ресурса
     *
     * @param array $data
     * @param string $class
     * @param string $processor
     *
     * @return object
     * @throws Exception
     */
    function createResource(array $data, $class = 'modDocument', $processor = 'resource/create') {
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
        $response = $this->modx->runProcessor($processor, $data, $class);
        if (!empty($response->response['errors'])) {
            $this->view('[uModx::createResource] Error creating a resource');
            $this->view($response->response['errors']);
            $this->viewEnd($data);
        }
        return (object) $response->response['object'];
    }

    /**
     * Создание нового пользователя (НЕ ПРОВЕРЕНО!)
     *
     * @param array $data
     * @param array $groupsList
     * @param integer $role
     *
     * @return object
     * @throws Exception
     */
    function createUser(array $data, array $groupsList = array('Administrator'), $role = 1) {
        foreach (['username', 'email'] as $required) {
            if (!isset($data[$required])) {
                throw new Exception('Required field "' . $required . '" for class "' . $class . '"');
            }
        }

        if (!isset($data['password']))
            $data['password'] = $this->generatePass();

        if (!isset($data['fullname']))
            $data['fullname'] = $data['username'];

        /**
         * Создание нового пользователя
         */
        $user = $this->modx->newObject('modUser');

        $user->set('username', $data['username']);
        $user->set('password', $data['password']);
        $user->save();

        /**
         * Создание профиля для пользователя
         */
        $profile = $this->modx->newObject('modUserProfile');

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
            $group = $this->modx->getObject('modUserGroup', array('name' => $groupName));

            $groupMember = $this->modx->newObject('modUserGroupMember');
            $groupMember->set('user_group', $group->get('id'));
            $groupMember->set('role', $role);
            $groups[] = $groupMember;
        }
        $user->addMany($groups);
        $user->save();

        return (object) $user;
    }
}