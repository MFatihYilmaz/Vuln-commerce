<?php 

class Mail{
    private $mail;
    public function __construct($mail) {
        $this->mail = $mail;
    }
    public function __wakeup()
    {
        return $this->mailControl();
    }

    private function mailControl(){
        $domain=preg_split('{@}',$this->mail)[1];
        return in_array(system('dig mx '.$domain),['ANSWER:']);    
    }
}

?>