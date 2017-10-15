<?php

class PacienteRecord extends TRecord
{
    const TABLENAME = 'paciente';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'max'; // {max, serial}

    private $causa_obito;
    private $municipio;
    

    public function get_municipio()
    {
      if (empty ($this->municipio)) {
        $this->municipio = new MunicipioRecord($this->id);
    }
    return $this->municipio->nome;
}

function get_causa_obito_nome(){

    if (empty ($this->causa_obito)){
        $this->causa_obito = new CausaObitoRecord($this->causa_obito_id);
    }
    
    return $this->causa_obito->descricao;

}

}



