<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RestClientController
 *
 * @author leng
 */
class RestClientController extends DooController{

    public function foodById(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/list/'. $this->params['id'].$this->extension;

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->get();

         $data['title'] = 'Food by ID <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;
         
        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            if($this->extension=='.xml'){
                $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
                $data['printr'] = $client->xml_result();
            }
            else if($this->extension=='.json'){
                $data['content'] .= '<br/><br/>JSON string: <pre>' . $client->result() .'</pre>';
                $data['printr'] = $client->json_result();
            }
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

    public function foodAllXml(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/list/all.xml';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->get();

         $data['title'] = 'Food by ID <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

    public function foodAllJson(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/list/all.json';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->get();

         $data['title'] = 'Food by ID <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>JSON string: <pre>' . $client->result() .'</pre>';
            $data['printr'] = $client->json_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

    public function foodCreateNew(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/create';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )
               ->accept(DooRestClient::XML)
               ->data($this->params)
               ->post();

         $data['title'] = 'Food Create <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }

        $this->view()->render('template', $data);
    }

    public function foodUpdate(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/update';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )
               ->accept(DooRestClient::XML)
               ->data($this->params)
               ->put();

         $data['title'] = 'Food Update <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>XML string: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }

        $this->view()->render('template', $data);
    }

    public function foodDelete(){
        $server_url = Doo::conf()->APP_URL . 'index.php/api/food/delete/' . $this->params['id'];

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )->accept( DooRestClient::XML )->delete();

         $data['title'] = 'Food Delete <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;

        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['content'] .= '<br/><br/>Nothing return. Delete success.';
            $data['printr'] = $client->xml_result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . htmlentities($client->result()) .'</pre>';
            $data['printr'] = $client->xml_result();
        }

        $this->view()->render('template', $data);
    }

    public function foodAdmin(){
        if(empty($this->params['username']) || empty($this->params['password']))
            return 404;
            
        $server_url = Doo::conf()->APP_URL . 'index.php/api/admin/dostuff';

        $this->load()->helper('DooRestClient');
        $client = new DooRestClient;
        $client->connect_to( $server_url )
               ->auth( $this->params['username'], $this->params['password'] )
               ->post();
               
         $data['title'] = 'Food Admin <em>'. $server_url .'</em>';
         $data['baseurl'] = Doo::conf()->APP_URL;
         
        if($client->isSuccess()){
            $data['content'] = '<br/>HTTP result code: '. $client->resultCode() .'<br/>Received content-type: ' . $client->resultContentType();
            $data['printr'] = $client->result();
        }else{
            $data['content'] = 'Error code ' . $client->resultCode();
            $data['content'] .= '<br/><br/>Error content: <pre>' . $client->result() .'</pre>';
            $data['printr'] = null;
        }

        $this->view()->render('template', $data);
    }

}

?>