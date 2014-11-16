<?php

class UsuarioController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $log = Zend_Auth::getInstance();
        if ($log->hasIdentity()) {
            $cpf = $log->getIdentity()->userCpf;
            $auth = new Application_Model_DbTable_Auth();
            $this->view->auth = $auth->getAuth($cpf);
        }
    }

    public function novoAction()
    {
        if ($this->getRequest()->isPost()) {
            $cpf = $_POST['cpf'];
            $nome = $_POST['nome'];
            $rg = $_POST['rg'];
            $dataNasc = $_POST['dataNasc'];
            $cep = $_POST['cep'];
            $endereco = $_POST['endereco'];
            $numEndereco = $_POST['numEndereco'];
            $bairro = $_POST['bairro'];
            $cidade = $_POST['cidade'];
            $estado = $_POST['estado'];
            
            $login = $_POST['login'];
            $pw = $_POST['pw'];
            
            $usuarios = new Application_Model_DbTable_Usuario();
            $usuarios->addUsuario($cpf, $nome, $rg, $dataNasc, $cep, $endereco, $numEndereco, $bairro, $cidade, $estado);
            
            $auth = new Application_Model_DbTable_Auth();
            $auth->addAuth($login, $pw, $cpf);
            
            $this->_helper->redirector('index');
        }
    }
    
    public function editAction () 
    {
        if ($this->getRequest()->isPost()) {          
            $cpf = $_POST['cpf'];
            $nome = $_POST['nome'];
            $rg = $_POST['rg'];
            $dataNasc = $_POST['dataNasc'];
            $cep = $_POST['cep'];
            $endereco = $_POST['endereco'];
            $numEndereco = $_POST['numEndereco'];
            $bairro = $_POST['bairro'];
            $cidade = $_POST['cidade'];
            $estado = $_POST['estado'];
            
            $usuarios = new Application_Model_DbTable_Usuario();
            $usuarios->updateUsuario($cpf, $nome, $rg, $dataNasc, $cep, $endereco, $numEndereco, $bairro, $cidade, $estado);
            $this->_helper->redirector('index');
        } else {
            $cpf = $this->_getParam('cpf');
            $usuarios = new Application_Model_DbTable_Usuario();
            $this->view->usuarios = $usuarios->getUsuario($cpf);
            $auth = new Application_Model_DbTable_Auth();
            $this->view->auth = $auth->getAuth($cpf);
        }
    }
    
    public function deleteAction () 
    {
        if ($this->getRequest()->isPost()) {
        $del = $this->getRequest()->getPost('del');
        if ($del == 'Sim') { 
        $cpf = $this->getRequest()->getPost('cpf');
        $usuarios = new Application_Model_DbTable_Usuario();
        $usuarios->deleteUsuario($cpf);
        }
        $this->_helper->redirector('index');
        } else {
        $cpf = $this->_getParam('cpf');
        $usuarios = new Application_Model_DbTable_Usuario();
        $this->view->usuarios = $usuarios->getUsuario($cpf);
        } 
    }
    
    public function listaAction ()
    {
        $log = Zend_Auth::getInstance();
        if ($log->hasIdentity()) {
            $adm = $log->getIdentity()->isAdm;

            if($adm == 1) {
                $usuarios = new Application_Model_DbTable_Usuario();
                $this->view->usuarios = $usuarios->fetchAll(); 
            } 
        }
    }

}
