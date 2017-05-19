<?php

// Revisado 19.05.17

class MunicipioList extends TPage{
    
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct() {

        parent::__construct();

        $this->form = new BootstrapFormBuilder("form_list_municipio" );
        $this->form->setFormTitle( "Listagem de Município" );
        $this->form->class = "tform";

        $opcao  = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty ( 'title', '"Informe os dados de acordo com a opção" ' );

        $opcao->setSize( '30%' );
        $dados->setSize( '30%' );
 
        
        $opcao->addItems( [ "nome" => "Nome","codibge" => "Cod.IBGE"] );
        $this->form->addFields( [ new TLabel( 'Opção de filtro:' ) ], [ $opcao ] );        
        $this->form->addFields( [ new TLabel( 'Dados da busca:' )  ], [ $dados ] );

        $this->form->addAction( 'Buscar', new TAction( [$this, 'onSearch'] ), 'fa:search' );
        $this->form->addAction( 'Novo', new TAction( ["MunicipioForm", 'onShow'] ), 'fa:save' );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_id = new TDataGridColumn( "codibge", "Cod. IBGE", "center", 50);
        $column_nome = new TDataGridColumn( "nome", "Nome", "center" );
        $column_uf = new TDataGridColumn( "uf", "Estado", "center" );

        $this->datagrid->addColumn( $column_id );
        $this->datagrid->addColumn( $column_nome );
        $this->datagrid->addColumn( $column_uf );

        $order_id = new TAction( [ $this, "onReload" ] );
        $order_id->setParameter( "order", "id" );
        $column_id->setAction( $order_id );

        $order_nome = new TAction( [ $this, "onReload" ] );
        $order_nome->setParameter( "order", "nome" );
        $column_nome->setAction( $order_nome );

        $order_uf = new TAction( [ $this, "onReload" ] );
        $order_uf->setParameter( "order", "uf" );
        $column_uf->setAction( $order_uf );

        $action_edit = new TDataGridAction (["MunicipioForm", "onEdit"]);
        $action_edit->setButtonClass ( "btn btn-default" );
        $action_edit->setLabel ( "Editar" );
        $action_edit->setImage ( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField ( "id" );
        $this->datagrid->addAction ( $action_edit );

        $action_del = new TDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $this->datagrid->addAction( $action_del );

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( new TXMLBreadCrumb( "menu.xml", __CLASS__ ) );
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }
    

    public function onSave() {
        
        try{
            TTransaction::open('dbsic');
            $object = $this->form->getData('MunicipioRecord');
            $object->store();
            
            TTransaction::close();
            
            new TMessage( 'info', 'Sucess');
        }
        catch (Exception $se){
            new TMessage('erro', $se->getMessage());
            TTransaction::rollback();
        }
    }
    
     public function onReload( $param = NULL )
    {
        try
        {
            TTransaction::open( "dbsic" );

            $repository = new TRepository( "MunicipioRecord" );

            if ( empty( $param[ "order" ] ) )
            {
                $param[ "order" ] = "id";
                $param[ "direction" ] = "asc";
            }

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );

            $objects = $repository->load( $criteria, FALSE );

            $this->datagrid->clear();

            if ( !empty( $objects ) )
            {
                foreach ( $objects as $object )
                {
                    $this->datagrid->addItem( $object );
                }
            }

            $criteria->resetProperties();

            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit); 

            TTransaction::close();

            $this->loaded = true;
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

    public function onSearch()
    {
        $data = $this->form->getData();

        try
        {
            if( !empty( $data->opcao ) && !empty( $data->dados ) )
            {
                TTransaction::open( "dbsic" );

                $repository = new TRepository( "MunicipioRecord" );

                if ( empty( $param[ "order" ] ) )
                {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );

                if( $data->opcao == "nome" )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                }
                else if ( ( $data->opcao == "cpf" || $data->opcao == "rg" ) && ( is_numeric( $data->dados ) ) )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                }
                else
                {
                    // new TMessage( "error", "O valor informado não é valido para um " . strtoupper( $data->opcao ) . "." );
                }

                $objects = $repository->load( $criteria, FALSE );

                $this->datagrid->clear();

                if ( $objects )
                {
                    foreach ( $objects as $object )
                    {
                        $this->datagrid->addItem( $object );
                    }
                }

                $criteria->resetProperties();

                $count = $repository->count( $criteria );

                $this->pageNavigation->setCount( $count ); // count of records
                $this->pageNavigation->setProperties( $param ); // order, page
                $this->pageNavigation->setLimit( $limit ); //Limita a quantidade de registros

                TTransaction::close();

                $this->form->setData( $data );

                $this->loaded = true;

            }
            else
            {
                $this->onReload();

                $this->form->setData( $data );

                // new TMessage( "error", "Selecione uma opção e informe os dados da busca corretamente!" );
            }
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();

            $this->form->setData( $data );

            new TMessage( "error", $ex->getMessage() );
        }

    }

    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) )
        {
            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameter( "key", $param[ "key" ] );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    function Delete( $param = NULL )
    {
        try
        {
            TTransaction::open( "dbsic" );

            $object = new MunicipioRecord( $param[ "key" ] );

            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage("info", "Registro apagado com sucesso!");
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();

            new TMessage("error", $ex->getMessage());
        }
    }

    public function show()
    {
        $this->onReload();

        parent::show();
    }
    
}
