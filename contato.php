<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



	class Contato extends CI_Controller{

		public function index(){	

			$header = preencheConteudoHeader();

			/*------------------- CONTENT ------------------------*/

			$content = array(	"header"        => $header              ,
                                "description"	=> ""					,
								"atualPage"		=> "contato"			,
								"title" 		=> "Contato | Fale Conosco");

			

			/*------------------- VIEW ---------------------------*/

			$this->load->template_conteudo('contato/contato.php',$content);

			

		}

	

	public function email_send(){

				

			//CONTEUDO

			$assunto = $this->input->post('assunto');

			$nome = $this->input->post('nome');

			$email = $this->input->post('email');

			$telefone = $this->input->post('tel');

			$estado = $this->input->post('estado');

			$cidade = $this->input->post('cidade');		

			$mensagem = $this->input->post('mensagem');

				

			$content_email = array(	"assunto" => $assunto,

									"nome" => $nome ,

									"email" => $email ,

									"telefone" => $telefone,

									"estado" => $estado,

									"cidade" => $cidade,

									"mensagem" => $mensagem);

			

			$conteudo = $this->load->view('contato/email_content.php',$content_email, TRUE);

				

			//CONFIGURA E-MAIL

			$config = Array(

					'protocol' => 'smtp',

					'smtp_host' => 'ssl://smtp.googlemail.com',

					'smtp_port' => '465',

					'smtp_user' => 'andrebonetti2@gmail.com',

					'smtp_pass' => 'flatron500G',

					'mailtype'  => 'html',

					'charset'   => 'utf-8');

				

			$this->load->library('email', $config);

			$this->email->set_newline("\r\n");

		

			//SET CONTEUDO

			$this->email->from( $email, $nome);

			$this->email->to('jobara@jobara.com.br');

			

			$this->email->subject("Site Jobara - ".$assunto);

			$this->email->message($conteudo);

		

			//RESULTADO

			if($this->email->send()){

				$msg = "E-mail enviado com sucesso !";

				$content = array("atualPage" => "Contato",

						"mensagem" =>	$msg);

						$this->session->set_flashdata('msg-success',"Email enviado com sucesso!");

						redirect("contato/index");

			}

			else{

				echo "Erro ao enviar mensagem";

				echo $this->email->print_debugger();

			}

		

		}		

}