<?php
    class Mailer
    {
        private $mail;
        private $smtp_server          = "smtp.gmail.com";
        private $smtp_port            = 465;
        private $smtp_username        = "fordrive.net@gmail.com";
        private $smtp_password        = "szd@242424";
        private $sender_email_address = "fordrive.net@gmail.com";
        private $sender_email_heading = "Fordrive.net";
        
        public function __construct()
        {
            $this->mail = new PHPMailer();
            
            $this->mail->IsSMTP();
            
            $this->mail->Host       = $this->smtp_server;
            $this->mail->SMTPAuth   = true;
            $this->mail->SMTPSecure = "ssl";
            $this->mail->Host       = $this->smtp_server;
            $this->mail->Port       = $this->smtp_port;
            $this->mail->Username   = $this->smtp_username;
            $this->mail->Password   = $this->smtp_password;
            
            $this->set_sender($this->sender_email_address,
                                    $this->sender_email_heading);
        }
        
        public function set_sender($sender_email_address, 
                                            $sender_email_heading)
        {
            $this->mail->SetFrom($sender_email_address, 
                                        $sender_email_heading);
        }
        
        public function set_subject($subject)
        {
            $this->mail->Subject = $subject;
        }
        
        public function set_body_html($html)
        {
            $this->mail->MsgHTML($html);
        }
        
        public function add_target($target_email_address)
        {
            $this->mail->AddAddress($target_email_address);
        }
        
        public function send()
        {
            return ($this->mail->Send()) ? true : false;
        }
    }
?>