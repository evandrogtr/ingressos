<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function loginAction()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->messages = $this->_flashMessenger->getMessages();
        $form = new Form_Login();
        $this->view->form = $form;
        
        if ( $this->getRequest()->isPost() ) {
            $data = $this->getRequest()->getPost();
            
            if ( $form->isValid($data) ) {
                $login = $form->getValue('login');
                $senha = $form->getValue('senha');
 
                $dbAdapter = Zend_Db_Table::getDefaultAdapter();
                
                $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                $authAdapter->setTableName('auth')
                        ->setIdentityColumn('login')
                        ->setCredentialColumn('pw')
                        ->setCredentialTreatment('SHA1(?)');
                
                $authAdapter->setIdentity($login)
                            ->setCredential($senha);
                
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                
                if ( $result->isValid() ) {            
                    $info = $authAdapter->getResultRowObject(null, 'senha');
                    $storage = $auth->getStorage();
                    $storage->write($info); 
                    $login = $auth->getIdentity()->isAdm;
                    if($login == 1) {
                        return $this->_helper->redirector->goToRoute( array('controller' => 'adm'), null, true);                        
                    } else {
                        return $this->_helper->redirector->goToRoute( array('controller' => 'usuario'), null, true);
                    }
                } else {                 
                    $this->_helper->FlashMessenger('Usuário ou senha inválidos!');
                    $this->_redirect('/auth/login');
                }
            } else {
                $form->populate($data);
            }
        }
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector('login');
    }
    
    public function deniedAction()
    {
        $msg = 'Você não ter permissões suficientes para acessar esta página';
        $this->view->msg = $msg;
    }
    
    public function setadmAction ()
    {
        $log = Zend_Auth::getInstance();
        if (!$log->hasIdentity()) {
            $this->_redirect('auth/denied');
        } else {
            $login = $log->getIdentity()->isAdm;
            if ($login != 1) {
                $this->_redirect('auth/denied');
            } else {
                $cpf = $this->_getParam('cpf');
                $this->view->cpf = $cpf;
                $auth = new Application_Model_DbTable_Auth();
                $auth->setadmAuth($cpf);
            }
        }
    }
}
