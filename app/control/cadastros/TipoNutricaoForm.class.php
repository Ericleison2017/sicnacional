<?php

// Revisado 19.05.17

class TipoNutricaoForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();


        $this->form = new BootstrapFormBuilder( "form_tiponutricao" );
        $this->form->setFormTitle( "Formulário de Tipos de Nutrição" );
        $this->form->class = "tform";

        $id = new THidden('id');
        $nome = new TEntry('nome');

        $this->form->addFields( [$id] );
        $this->form->addFields( [new TLabel('Tipo de Nutrição <font color=red>*</font>')], [$nome] );

        $id->setEditable(FALSE);
        $id->setSize('38%');
        $nome->setSize('38%');
        $nome->addValidation('Tipo de Nutrição', new TRequiredValidator );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para listagem", new TAction( [ "TipoNutricaoList", "onReload" ] ), "fa:table blue" );
       
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add(new TXMLBreadCrumb('menu.xml', 'TipoNutricaoList'));
        $container->add($this->form);

        parent::add($container);
    }

    public function onSave()
    {
        try
        {
            $this->form->validate();

            TTransaction::open( "dbsic" );

            $object = $this->form->getData( "TipoNutricaoRecord" );

            //$object->usuarioalteracao = TSession::getValue("login");
            //$object->dataalteracao = date( "Y-m-d H:i:s" );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "TipoNutricaoList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

            // TApplication::gotoPage("CadastroClientesList", "onReload");
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br>" . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if( isset( $param[ "key" ] ) )
            {
                TTransaction::open( "dbsic" );

                $object = new TipoNutricaoRecord( $param[ "key" ] );

                $this->form->setData( $object );

                TTransaction::close();
            }
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}
